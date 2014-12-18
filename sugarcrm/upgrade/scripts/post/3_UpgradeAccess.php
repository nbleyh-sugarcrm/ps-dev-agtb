<?php
 if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

/**
 * Update .htaccess files or web.config files
 */
class SugarUpgradeUpgradeAccess extends UpgradeScript
{
    public $order = 3000;
    public $type = self::UPGRADE_CORE;

    public function run()
    {
        require_once "install/install_utils.php";

        $htaccess_file = $this->context['source_dir']."/.htaccess";
        $webconfig_file = $this->context['source_dir']."/web.config";

        if(file_exists($webconfig_file) ||
            (!empty($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER["SERVER_SOFTWARE"],'Microsoft-IIS') !== false
        )) {
            $this->handleWebConfig();
        }
        if (!file_exists($webconfig_file) || file_exists($htaccess_file))
        {
            $this->handleHtaccess();
        }
    }

    protected function handleWebConfig()
    {
        handleWebConfig();
    }

    protected function handleHtaccess()
    {
        $htaccess_file = $this->context['source_dir']."/.htaccess";
        $basePath = parse_url($this->upgrader->config['site_url'], PHP_URL_PATH);
        if(empty($basePath)) $basePath = '/';

        /**
         * .htaccess change between 6.7 and 7.0.
         * This piece used to be outside # SUGARCRM RESTRICTIONS but it's been moved inside in 7.0
         * Thus we have to delete this piece prior to rebuild the htaccess, so we avoid duplicate rules
         */
        if (file_exists($htaccess_file)) {

            $cache_headers = <<<EOQ
<FilesMatch "\.(jpg|png|gif|js|css|ico)$">
        <IfModule mod_headers.c>
                Header set ETag ""
                Header set Cache-Control "max-age=2592000"
                Header set Expires "01 Jan 2112 00:00:00 GMT"
        </IfModule>
</FilesMatch>
<IfModule mod_expires.c>
        ExpiresByType text/css "access plus 1 month"
        ExpiresByType text/javascript "access plus 1 month"
        ExpiresByType application/x-javascript "access plus 1 month"
        ExpiresByType image/gif "access plus 1 month"
        ExpiresByType image/jpg "access plus 1 month"
        ExpiresByType image/png "access plus 1 month"
</IfModule>
EOQ;
            $mod_rewrite = <<<EOQ
<IfModule mod_rewrite.c>
    Options +FollowSymLinks
    RewriteEngine On
    RewriteBase {$basePath}
    RewriteRule ^cache/jsLanguage/(.._..).js$ index.php?entryPoint=jslang&module=app_strings&lang=$1 [L,QSA]
    RewriteRule ^cache/jsLanguage/(\w*)/(.._..).js$ index.php?entryPoint=jslang&module=$1&lang=$2 [L,QSA]
</IfModule>
EOQ;

        
            $htaccess_contents = file_get_contents($htaccess_file);
            $htaccess_contents = str_replace($cache_headers, '', $htaccess_contents);
            $htaccess_contents = str_replace($mod_rewrite, '', $htaccess_contents);
            $status =  $this->putFile($htaccess_file, $htaccess_contents);
            if( $status === false){
                $this->fail(sprintf($this->mod_strings['ERROR_HT_NO_WRITE'], $htaccess_file));
                return;
            }
        }

        $status =  $this->putFile($htaccess_file, getHtaccessData($htaccess_file));
        if( $status === false ){
            $this->fail(sprintf($this->mod_strings['ERROR_HT_NO_WRITE'], $htaccess_file));
            return;
        }

        if (empty($GLOBALS['sugar_config']['upload_dir'])) {
            $GLOBALS['sugar_config']['upload_dir']='upload/';
        }

        $uploadHta = "upload://.htaccess";

        $denyAll =<<<eoq
        	Order Deny,Allow
        	Deny from all
eoq;

        if(file_exists($uploadHta) && filesize($uploadHta)) {
        	// file exists, parse to make sure it is current
            $oldHtaccess = file_get_contents($uploadHta);
        	// use a different regex boundary b/c .htaccess uses the typicals
        	if(strstr($oldHtaccess, $denyAll) === false) {
                $oldHtaccess .= "\n";
        		$oldHtaccess .= $denyAll;
        	}
        	if(!file_put_contents($uploadHta, $oldHtaccess)) {
                $this->fail(sprintf($this->mod_strings['ERROR_HT_NO_WRITE'], $uploadHta));
        	}
        } else {
        	// no .htaccess yet, create a fill
        	if(!file_put_contents($uploadHta, $denyAll)) {
        		$this->fail(sprintf($this->mod_strings['ERROR_HT_NO_WRITE'], $uploadHta));
        	}
        }
    }
}
