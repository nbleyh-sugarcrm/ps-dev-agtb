<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

require_once('clients/base/api/ModuleApi.php');
require_once('include/RecordListFactory.php');

class RelateRecordApi extends ModuleApi {
    public function registerApiRest() {
        return array(
            'fetchRelatedRecord' => array(
                'reqType'   => 'GET',
                'path'      => array('<module>','?',     'link','?',        '?'),
                'pathVars'  => array('module',  'record','',    'link_name','remote_id'),
                'method'    => 'getRelatedRecord',
                'shortHelp' => 'Fetch a single record related to this module',
                'longHelp'  => 'include/api/help/module_record_link_link_name_remote_id_get_help.html',
            ),
            'createRelatedRecord' => array(
                'reqType'   => 'POST',
                'path'      => array('<module>','?',     'link','?'),
                'pathVars'  => array('module',  'record','',    'link_name'),
                'method'    => 'createRelatedRecord',
                'shortHelp' => 'Create a single record and relate it to this module',
                'longHelp'  => 'include/api/help/module_record_link_link_name_post_help.html',
            ),
            'createRelatedLink' => array(
                'reqType'   => 'POST',
                'path'      => array('<module>','?',     'link','?'        ,'?'),
                'pathVars'  => array('module',  'record','',    'link_name','remote_id'),
                'method'    => 'createRelatedLink',
                'shortHelp' => 'Relates an existing record to this module',
                'longHelp'  => 'include/api/help/module_record_link_link_name_remote_id_post_help.html',
            ),
            'createRelatedLinks' => array(
                'reqType' => 'POST',
                'path' => array('<module>', '?', 'link'),
                'pathVars' => array('module', 'record', ''),
                'method' => 'createRelatedLinks',
                'shortHelp' => 'Relates existing records to this module.',
                'longHelp' => 'include/api/help/module_record_link_post_help.html',
            ),
            'updateRelatedLink' => array(
                'reqType'   => 'PUT',
                'path'      => array('<module>','?',     'link','?'        ,'?'),
                'pathVars'  => array('module',  'record','',    'link_name','remote_id'),
                'method'    => 'updateRelatedLink',
                'shortHelp' => 'Updates relationship specific information ',
                'longHelp'  => 'include/api/help/module_record_link_link_name_remote_id_put_help.html',
            ),
            'deleteRelatedLink' => array(
                'reqType'   => 'DELETE',
                'path'      => array('<module>','?'     ,'link','?'        ,'?'),
                'pathVars'  => array('module'  ,'record',''    ,'link_name','remote_id'),
                'method'    => 'deleteRelatedLink',
                'shortHelp' => 'Deletes a relationship between two records',
                'longHelp'  => 'include/api/help/module_record_link_link_name_remote_id_delete_help.html',
            ),
            'createRelatedLinksFromRecordList' => array(
                'reqType' => 'POST',
                'path' => array('<module>', '?', 'link', '?', 'add_record_list', '?'),
                'pathVars' => array('module', 'record', '', 'link_name', '', 'remote_id'),
                'method' => 'createRelatedLinksFromRecordList',
                'shortHelp' => 'Relates existing records from a record list to this record.',
                'longHelp' => 'include/api/help/module_record_links_from_recordlist_post_help.html',
            ),
        );
    }


    /**
     * Fetches data from the $args array and updates the bean with that data
     * @param $api ServiceBase The API class of the request, used in cases where the API changes how security is applied
     * @param $args array The arguments array passed in from the API
     * @param $primaryBean SugarBean The near side of the link
     * @param $securityTypeLocal string What ACL to check on the near side of the link
     * @param $securityTypeRemote string What ACL to check on the far side of the link
     * @return array Two elements: The link name, and the SugarBean of the far end
     */
    protected function checkRelatedSecurity(ServiceBase $api, $args, SugarBean $primaryBean, $securityTypeLocal='view', $securityTypeRemote='view') {
        if ( empty($primaryBean) ) {
            throw new SugarApiExceptionNotFound('Could not find the primary bean');
        }
        if ( ! $primaryBean->ACLAccess($securityTypeLocal) ) {
            throw new SugarApiExceptionNotAuthorized('No access to '.$securityTypeLocal.' records for module: '.$args['module']);
        }
        // Load up the relationship
        $linkName = $args['link_name'];
        if ( ! $primaryBean->load_relationship($linkName) ) {
            // The relationship did not load, I'm guessing it doesn't exist
            throw new SugarApiExceptionNotFound('Could not find a relationship named: '.$args['link_name']);
        }
        // Figure out what is on the other side of this relationship, check permissions
        $linkModuleName = $primaryBean->$linkName->getRelatedModuleName();
        $linkSeed = BeanFactory::getBean($linkModuleName);

        // FIXME: No create ACL yet
        if ( $securityTypeRemote == 'create' ) { $securityTypeRemote = 'edit'; }

        // only check here for edit...view and list are checked on formatBean
        if ( $securityTypeRemote == 'edit' && ! $linkSeed->ACLAccess($securityTypeRemote) ) {
            throw new SugarApiExceptionNotAuthorized('No access to '.$securityTypeRemote.' records for module: '.$linkModuleName);
        }

        return array($linkName, $linkSeed);

    }

    /**
     * This function is used to popluate an fields on the relationship from the request
     * @param $api ServiceBase The API class of the request, used in cases where the API changes how security is applied
     * @param $args array The arguments array passed in from the API
     * @param $primaryBean SugarBean The near side of the link
     * @param $linkName string What is the name of the link field that you want to get the related fields for
     * @return array A list of the related fields pulled out of the $args array
     */
    protected function getRelatedFields(ServiceBase $api, $args, SugarBean $primaryBean, $linkName, $seed = null) {
        $relatedData = array();
        if ($seed instanceof SugarBean) {
            foreach ($args as $field => $value) {
                if (empty($seed->field_defs[$field]['rname_link'])) {
                    continue;
                }
                $relatedData[$seed->field_defs[$field]['rname_link']] = $value;
            }
        }
        
        return $relatedData;
    }

    /**
     * This function is here temporarily until the Link2 class properly handles these for the non-subpanel requests
     * @param $api ServiceBase The API class of the request, used in cases where the API changes how security is applied
     * @param $args array The arguments array passed in from the API
     * @param $primaryBean SugarBean The near side of the link
     * @param $relatedBean SugarBean The far side of the link
     * @param $linkName string What is the name of the link field that you want to get the related fields for
     * @param $relatedData array The data for the related fields (such as the contact_role in opportunities_contacts relationship)
     * @return array Two elements, 'record' which is the formatted version of $primaryBean, and 'related_record' which is the formatted version of $relatedBean
     */
    protected function formatNearAndFarRecords(ServiceBase $api, $args, SugarBean $primaryBean, $relatedArray = array()) {
        $api->action = 'view';
        $recordArray = $this->formatBean($api, $args, $primaryBean);
        if (empty($relatedArray))
            $relatedArray = $this->getRelatedRecord($api, $args);

        return array(
            'record'=>$recordArray,
            'related_record'=>$relatedArray
        );
    }


    function getRelatedRecord($api, $args) {
        $primaryBean = $this->loadBean($api, $args);
        
        list($linkName, $relatedBean) = $this->checkRelatedSecurity($api, $args, $primaryBean, 'view','view');

        $related = array_values($primaryBean->$linkName->getBeans(array(
            'where' => array(
                'lhs_field' => 'id',
                'operator' => '=',
                'rhs_value' => $args['remote_id'],
            )
        )));
        if ( empty($related[0]->id) ) {
            // Retrieve failed, probably doesn't have permissions
            throw new SugarApiExceptionNotFound('Could not find the related bean');
        }

        return $this->formatBean($api, $args, $related[0]);
        
    }

    function createRelatedRecord($api, $args) {
        $primaryBean = $this->loadBean($api, $args);

        list($linkName, $relatedBean) = $this->checkRelatedSecurity($api, $args, $primaryBean, 'view','create');

        if ( isset($args['id']) ) {
            $relatedBean->new_with_id = true;
        }

        $id = $this->updateBean($relatedBean, $api, $args);

        $relatedData = $this->getRelatedFields($api, $args, $primaryBean, $linkName, $relatedBean);
        $primaryBean->$linkName->add(array($relatedBean),$relatedData);

        //Clean up any hanging related records.
        SugarRelationship::resaveRelatedBeans();

        $args['remote_id'] = $relatedBean->id;

        // This forces a re-retrieval of the bean from the database
        BeanFactory::unregisterBean($relatedBean);

        return $this->formatNearAndFarRecords($api,$args,$primaryBean);
    }

    function createRelatedLink($api, $args) {
        $api->action = 'save';
        $primaryBean = $this->loadBean($api, $args);

        list($linkName, $relatedBean) = $this->checkRelatedSecurity($api, $args, $primaryBean, 'view','view');

        $relatedBean->retrieve($args['remote_id']);
        if ( empty($relatedBean->id) ) {
            // Retrieve failed, probably doesn't have permissions
            throw new SugarApiExceptionNotFound('Could not find the related bean');
        }

        $relatedData = $this->getRelatedFields($api, $args, $primaryBean, $linkName, $relatedBean);
        $primaryBean->$linkName->add(array($relatedBean),$relatedData);

        //Clean up any hanging related records.
        SugarRelationship::resaveRelatedBeans();

        // This forces a re-retrieval of the bean from the database
        BeanFactory::unregisterBean($relatedBean);

        return $this->formatNearAndFarRecords($api,$args,$primaryBean);
    }

    /**
     * Relates existing records to related bean.
     *
     * @param ServiceBase $api The API class of the request.
     * @param array $args The arguments array passed in from the API.
     * @return array Array of formatted fields.
     * @throws SugarApiExceptionNotFound If bean can't be retrieved.
     */
    public function createRelatedLinks($api, $args)
    {
        $result = array(
            'related_records' => array(),
        );

        $primaryBean = $this->loadBean($api, $args);

        list($linkName) = $this->checkRelatedSecurity($api, $args, $primaryBean, 'view', 'view');
        $relatedModuleName = $primaryBean->$linkName->getRelatedModuleName();

        foreach ($args['ids'] as $id) {
            $relatedBean = BeanFactory::retrieveBean($relatedModuleName, $id);

            if (!$relatedBean || $relatedBean->deleted) {
                throw new SugarApiExceptionNotFound('Could not find the related bean');
            }
            $primaryBean->$linkName->add(array($relatedBean));

            $result['related_records'][] = $this->formatBean($api, $args, $relatedBean);
        }
        //Clean up any hanging related records.
        SugarRelationship::resaveRelatedBeans();

        $result['record'] = $this->formatBean($api, $args, $primaryBean);

        return $result;
    }

    function updateRelatedLink($api, $args) {
        $api->action = 'save';
        $primaryBean = $this->loadBean($api, $args);

        list($linkName, $relatedBean) = $this->checkRelatedSecurity($api, $args, $primaryBean, 'view','edit');

        $relatedBean->retrieve($args['remote_id']);
        if ( empty($relatedBean->id) ) {
            // Retrieve failed, probably doesn't have permissions
            throw new SugarApiExceptionNotFound('Could not find the related bean');
        }

        // updateBean may remove the relationship. see PAT-337 for details
        $id = $this->updateBean($relatedBean, $api, $args);

        $relatedArray = array();
        $relObj = $primaryBean->$linkName->getRelationshipObject();
        // If the relationship still exists, we need to save changes to relationship fields
        if ($relObj->relationship_exists($primaryBean, $relatedBean)) {
            $relatedData = $this->getRelatedFields($api, $args, $primaryBean, $linkName, $relatedBean);
            // This function add() is actually 'addOrUpdate'. Here we use it for update only.
            $primaryBean->$linkName->add(array($relatedBean),$relatedData);
        }
        // If the relationship has been removed, we don't need to update the relationship fields
        else {
            // Prepare the ralated bean data for formatNearAndFarRecords() below
            $relatedArray = $this->formatBean($api, $args, $relatedBean);
            // This record is unlinked to primary bean
            $relatedArray['_unlinked'] = true;
        }
        
        //Clean up any hanging related records.
        SugarRelationship::resaveRelatedBeans();

        // This forces a re-retrieval of the bean from the database
        BeanFactory::unregisterBean($relatedBean);

        return $this->formatNearAndFarRecords($api,$args,$primaryBean,$relatedArray);
    }

    function deleteRelatedLink($api, $args) {
        $primaryBean = $this->loadBean($api, $args);

        list($linkName, $relatedBean) = $this->checkRelatedSecurity($api, $args, $primaryBean, 'view','view');

        $relatedBean->retrieve($args['remote_id']);
        if ( empty($relatedBean->id) ) {
            // Retrieve failed, probably doesn't have permissions
            throw new SugarApiExceptionNotFound('Could not find the related bean');
        }

        $primaryBean->$linkName->delete($primaryBean->id,$relatedBean);

        // Get a fresh copy of the related bean so that the newly deleted relationship
        // shows as deleted. See BR-1055
        $relatedBean = BeanFactory::getBean($relatedBean->module_name, $relatedBean->id, array('use_cache' => false));

        //Because the relationship is now deleted, we need to pass the $relatedBean data into formatNearAndFarRecords
        return $this->formatNearAndFarRecords($api,$args,$primaryBean, $this->formatBean($api, $args, $relatedBean));
    }

    /**
     * Relates existing records to related bean.
     *
     * @param ServiceBase $api The API class of the request.
     * @param array $args The arguments array passed in from the API.
     * @return array Array of formatted fields.
     * @throws SugarApiExceptionNotFound If bean can't be retrieved.
     */
    public function createRelatedLinksFromRecordList($api, $args)
    {
        Activity::disable();

        $result = array(
            'related_records' => array(
                'success' => array(),
                'error' => array(),
            ),
        );

        $this->requireArgs($args, array('module', 'record', 'remote_id', 'link_name'));

        $primaryBean = $this->loadBean($api, $args);

        list($linkName) = $this->checkRelatedSecurity($api, $args, $primaryBean, 'view', 'view');

        $recordList = RecordListFactory::getRecordList($args['remote_id']);
        $relatedBeans = $primaryBean->$linkName->add($recordList['records']);

        if ($relatedBeans === true) {
            $result['related_records']['success'] = $recordList['records'];
        } elseif (is_array($relatedBeans)) {
            $result['related_records']['success'] = array_diff($recordList['records'], $relatedBeans);
            $result['related_records']['error']   = $relatedBeans;
        }

        SugarRelationship::resaveRelatedBeans();

        Activity::enable();
        $result['record'] = $this->formatBean($api, $args, $primaryBean);

        return $result;
    }
}
