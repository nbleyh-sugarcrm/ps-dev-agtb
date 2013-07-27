<?php
/*********************************************************************************
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement (“MSA”), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright (C) 2004-2013 SugarCRM Inc.  All rights reserved.
 ********************************************************************************/
require_once 'modules/UpgradeWizard/SidecarUpdate/SidecarGridMetaDataUpgrader.php';

class SidecarMergeGridMetaDataUpgrader extends SidecarGridMetaDataUpgrader
{

    /**
     * Composite views
     * sugar7view => [ 'sugar6view1' => ['metadatadefs1', 'view1'], 'sugar6view2' => ['metadatadefs2', 'view2'] ]
     * @var array
     */
    protected $mergeViews = array(
            MB_RECORDVIEW => array(
                'detail' => array('detailviewdefs', MB_DETAILVIEW),
                'edit' => array('editviewdefs', MB_EDITVIEW),
            ),
            MB_PORTALRECORDVIEW => array(
                'detail' => array('detailviewdefs', MB_DETAILVIEW),
                'edit' => array('editviewdefs', MB_EDITVIEW),
            ),
    );

    protected $mergeViewsSidecar = array(
            MB_PORTALRECORDVIEW => array(
                'detail' => array('detail', MB_PORTALDETAILVIEW),
                'edit' => array('edit', MB_PORTALEDITVIEW),
            ),
    );

    /**
     * Panels where fields from each view are placed
     * @var array
     */
    protected $viewPanels = array(
        MB_RECORDVIEW => array(
            MB_DETAILVIEW => 1,
            MB_EDITVIEW => 2,
        ),
        MB_PORTALRECORDVIEW => array(
            MB_PORTALDETAILVIEW => 1,
            MB_PORTALEDITVIEW => 1,
            MB_DETAILVIEW => 1,
            MB_EDITVIEW => 1,
        ),
    );

    /**
     * List of upgraded dir to prevent double upgrades
     */
    protected static $upgraded = array();

    protected function getOriginalFile($filepath)
    {
        $files = explode("/", $filepath);
        // drop prefixes like custom/
        while(!empty($files) && $files[0] != 'modules') {
            array_shift($files);
        }
        if(empty($files)) {
            return $filepath;
        }

        if($this->client == 'portal' && !$this->sidecar) {
            // old portal views have prefixes
            $filename = "portal.".array_pop($files);
            array_push($files, $filename);
        }
        return join("/", $files);
    }

    /**
     * Sets the necessary legacy field defs for use in converting
     */
    public function setLegacyViewdefs()
    {
        $views = $this->sidecar?$this->mergeViewsSidecar:$this->mergeViews;
        if(empty($views[$this->viewtype])) {
            $this->logUpgradeStatus("Did not find merge views for {$this->viewtype}");
            return;
        }

        if($this->sidecar) {
            // For sidecar it's path/views/edit/edit.php
            $dirname = dirname(dirname($this->fullpath));
        } else {
            // For sugar6 it's path/metadata/editviewdefs.php
            $dirname = dirname($this->fullpath);
        }
        if(!empty(self::$upgraded[$this->viewtype][$dirname])) {
            // we already did this path for this viewtype
            $this->logUpgradeStatus("Already upgraded $dirname {$this->viewtype}");
            return;
        } else {
            self::$upgraded[$this->viewtype][$dirname] = true;
        }

        $foundCustom = false;
        // Load all views for this combined view
        foreach($views[$this->viewtype] as $view => $data) {
            unset($module_name);
            list($file, $lViewtype) = $data;
            if($this->sidecar) {
                $filepath = "$dirname/$file/$file.php";
            } else {
                $filepath = "$dirname/$file.php";
            }
            if(!file_exists($filepath)) {
                // try without custom/, as this is a merge
                $filepath = $this->getOriginalFile($filepath);
                if(!file_exists($filepath)) {
                    $this->logUpgradeStatus("Could not find $filepath for $lViewtype");
                    continue;
                }
            } else {
                $foundCustom = true;
            }

            $this->logUpgradeStatus("Loading $filepath for $lViewtype");
            include $filepath;
            // There is an odd case where custom modules are pathed without the
            // package name prefix but still use it in the module name for the
            // viewdefs. This handles that case. Also sets a prop that lets the
            // rest of the process know that the module is named differently
            if (isset($module_name)) {
                $this->modulename = $module = $module_name;
            } else {
                $module = $this->module;
            }

            $var = $this->variableMap[$this->client][$view];
            if (isset($$var)) {
                $defs = $$var;
                if($this->sidecar) {
                    if(!empty($defs[$module][$this->client]['view'][$view])) {
                        $this->legacyViewdefs[$lViewtype] = $defs[$module][$this->client]['view'][$view];
                    }
                } else {
                    if (isset($this->vardefIndexes[$this->client.$view])) {
                        $index = $this->vardefIndexes[$this->client.$view];
                        $this->legacyViewdefs[$lViewtype] = empty($index) ? $defs[$module] : $defs[$module][$index];
                        if($this->client == 'portal' && !empty($this->legacyViewdefs[$lViewtype]['data'])) {
                            // Portal views are in 'data', not 'panels'
                            // Because it'd be boring if all data formats were the same, right?
                            $this->legacyViewdefs[$lViewtype]['panels'] = array($this->legacyViewdefs[$lViewtype]['data']);
                        }
                    }
                }
            }
        }
        // If we didn't find any custom files - we don't need to do anything
        if(!$foundCustom) {
            $this->legacyViewdefs = array();
            $this->logUpgradeStatus("Did not find customizations for {$this->viewtype}");
        }
    }

    /**
     * (non-PHPdoc)
     * @see SidecarAbstractMetaDataUpgrader::handleSave()
     */
    public function handleSave()
    {
        if(empty($this->sidecarViewdefs)) {
            // if we didn't create any new defs, nothing to save
            return true;
        }
        return parent::handleSave();
    }

    /**
     * Converts the legacy Grid metadata to Sidecar style
     */
    public function convertLegacyViewDefsToSidecar()
    {
        if(empty($this->legacyViewdefs)) {
            return;
        }
        $this->logUpgradeStatus('Converting ' . $this->client . ' ' . $this->viewtype . ' view defs for ' . $this->module);

        // TODO: if it's a custom module, will throw, we should use template instead
        $parser = ParserFactory::getParser($this->viewtype, $this->module, null, null, $this->client);

        // Go through merge views, add fields added to detail view to base panel
        // and fields added to edit view not in detail view ot hidden panel
        $customFields = array();
        foreach($this->legacyViewdefs as $lViewtype => $data) {
            if(empty($data['panels'])) {
                continue;
            }
            if($this->sidecar) {
                $legacyParser = ParserFactory::getParser($lViewtype, $this->module, null, null, $this->client);
            } else {
                $legacyParser = ParserFactory::getParser($lViewtype, $this->module);
            }
            foreach($legacyParser->getFieldsFromPanels($data['panels']) as $fieldname => $fielddef) {
                if(empty($fieldname) || isset($customFields[$fieldname])) {
                    continue;
                }
                $customFields[$fieldname] = array('data' => $fielddef, 'source' => $lViewtype);
            }
        }

        // Hack: we've moved email1 to email
        if(isset($customFields['email1'])) {
            $customFields['email'] = $customFields['email1'];
            unset($customFields['email1']);
        }

        $origFields = array();
        $defaultDefs = $this->loadDefaultMetadata();
        // replace viewdefs with defaults, since parser's viewdefs can be already customized by other parts
        // of the upgrade
        $parser->_viewdefs['panels'] = $parser->convertFromCanonicalForm($defaultDefs['panels'], $parser->_fielddefs);
        // get field list
        $origData = $parser->getFieldsFromPanels($defaultDefs['panels'], $parser->_fielddefs);
        // Go through existing fields and remove those not in the new data
        foreach($origData as $fname => $fielddef) {
            if(isset($fielddef['type'])) {
                if($fielddef['type'] != 'fieldset') {
                    // special-case fields can be ignored for now
                    continue;
                }
                // fieldsets - iterate over each field
                $setExists = false;
                foreach($fielddef['fields'] as $setfielddef) {
                    if(!is_array($setfielddef)) {
                        $setfname = $setfielddef;
                    } else {
                        // skip werid nameless ones
                        if(empty($setfielddef['name'])) continue;
                        $setfname = $setfielddef['name'];
                    }
                    // if we have one field - we take all set
                    if(isset($customFields[$setfname])) {
                        $setExists = true;
                        break;
                    }
                }
                if($setExists) {
                    // if fields exist, we take all the set as existing fields
                    foreach($fielddef['fields'] as $setfielddef) {
                        if(!is_array($setfielddef)) {
                            $setfname = $setfielddef;
                        } else {
                            // skip werid nameless ones
                            if(empty($setfielddef['name'])) continue;
                            $setfname = $setfielddef['name'];
                        }
                        $origFields[$setfname] = $fielddef;
                    }
                } else {
                    // else we delete the set
                    $parser->removeField($fname);
                }
            } else {
                // if it's a regular field, check against existing field in new data
                if(!isset($customFields[$fname])) {
                    // not there - remove it
                    $parser->removeField($fname);
                } else {
                    // otherwise - keep as existing
                    $origFields[$fname] = $fielddef;
                }
            }
        }

        // now go through new fields and add those not in original data
        foreach($customFields as $fieldname => $data) {
            if(isset($origFields[$fieldname])) {
                // TODO: merge the data such as label, readonly, etc.
                continue;
            } else {
                // TODO: import more data than just name
                $parser->addField(array('name' => $fieldname), $this->getPanelName($parser->_viewdefs['panels'], $data['source']));
            }
        }

        $newdefs = $parser->_viewdefs;
        $newdefs['panels'] = $parser->convertToCanonicalForm($parser->_viewdefs['panels'] ,$parser->_fielddefs);

        $this->sidecarViewdefs[$this->module][$this->client]['view'][MetaDataFiles::getName($this->viewtype)] = $newdefs;
   }

   /**
    * Get panel name where new field should be placed
    * @param array $panels Panel data for viewdef
    * @param string $source Source view for field
    * @return string|null
    */
   protected function getPanelName($panels, $source)
   {
       if(empty($this->viewPanels[$this->viewtype][$source])) {
           // will use first available panel
           return null;
       }
       $panel_names = array_keys($panels);
       $panel_id = $this->viewPanels[$this->viewtype][$source];
       if(!empty($panel_names[$panel_id])) {
           return $panel_names[$panel_id];
       }

       return null;
   }
}