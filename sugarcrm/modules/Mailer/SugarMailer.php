<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
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
/*********************************************************************************
 * $Header$
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
require_once('SimpleMailer.php');
require_once('include/OutboundEmail/OutboundEmail.php');

/**
 * Sugar mailer
 * @api
 */

 /* */
class SugarMailer extends SimpleMailer
{
    var $protocol = "tcp://";
    var $mailer = "smtp";
    var $host   = "localhost";
    var $port   = "25";

    var $user;
    var $locale;
    var $sugar_config;

    var $preppedForOutbound = false;
    var $disclosureEnabled;
    var $disclosureText;
    var $isHostEmpty = false;
    var $opensslOpened = true;

    /**
     * @param User    $current_user   - Current Signed In User
     * @param         $locale
     * @param         $sugar_config
     */
    function __construct(User $current_user, Locale $locale, SugarConfig $sugar_config) {
        $admin = new Administration();
        $admin->retrieveSettings();

        if(isset($admin->settings['disclosure_enable']) && !empty($admin->settings['disclosure_enable'])) {
            $this->disclosureEnabled = true;
            $this->disclosureText = $admin->settings['disclosure_text'];
        }

        $oe = new OutboundEmail();
        $oe->getUserMailerSettings($current_user);

        if (!($current_user instanceof User) ||
            !($locale instanceof Locale) ||
            !($sugar_config instanceof SugarConfig)) {
                throw new MailerException("SugarMailer Invalid Constructor Argument");
        }

/**
        $this->SetLanguage('en', 'lib/phpmailer/language/');
        $this->Mailer	 	= 'smtp';
        // cn: i18n
        $this->CharSet		= $locale->getPrecedentPreference('default_email_charset');
        $this->Encoding		= 'quoted-printable';
        $this->IsHTML(false);  // default to plain-text email
        $this->Hostname = $sugar_config['host_name'];
        $this->WordWrap		= 996;
        // cn: gmail fix
        $this->protocol = ($this->oe->mail_smtpssl == 1) ? "ssl://" : $this->protocol;

        //BEGIN SUGARCRM flav=int ONLY
        $this->SMTPDebug	= false;
        //END SUGARCRM flav=int ONLY
**/
    }


    /**
     * Prefills outbound details
     */
    function setMailer($current_user = null) {
        $oe = new OutboundEmail();
        if ($current_user != null) {
            $oe = $oe->getUserMailerSettings($current_user);
        } else {
            $oe = $oe->getSystemMailerSettings();
        }

        // ssl or tcp - keeping outside isSMTP b/c a default may inadvertently set ssl://
        $this->protocol = ($oe->mail_smtpssl) ? "ssl://" : "tcp://";

        if($oe->mail_sendtype == "SMTP")
        {
            //Set mail send type information
            $this->Mailer = "smtp";
            $this->Host = $oe->mail_smtpserver;
            $this->Port = $oe->mail_smtpport;
            if ($oe->mail_smtpssl == 1) {
                $this->SMTPSecure = 'ssl';
            } // if
            if ($oe->mail_smtpssl == 2) {
                $this->SMTPSecure = 'tls';
            } // if

            if($oe->mail_smtpauth_req) {
                $this->SMTPAuth = TRUE;
                $this->Username = $oe->mail_smtpuser;
                $this->Password = $oe->mail_smtppass;
            }
        }
        else
            $this->Mailer = "sendmail";
    }

    /**
     * Prefills mailer for system
     */
    function setMailerForSystem() {
        $oe = new OutboundEmail();
        $oe = $oe->getSystemMailerSettings();

        // ssl or tcp - keeping outside isSMTP b/c a default may inadvertantly set ssl://
        $this->protocol = ($oe->mail_smtpssl) ? "ssl://" : "tcp://";

        if($oe->mail_sendtype == "SMTP")
        {
            //Set mail send type information
            $this->Mailer = "smtp";
            $this->Host = $oe->mail_smtpserver;
            $this->Port = $oe->mail_smtpport;
            if ($oe->mail_smtpssl == 1) {
                $this->SMTPSecure = 'ssl';
            } // if
            if ($oe->mail_smtpssl == 2) {
                $this->SMTPSecure = 'tls';
            } // if
            if($oe->mail_smtpauth_req) {
                $this->SMTPAuth = TRUE;
                $this->Username = $oe->mail_smtpuser;
                $this->Password = $oe->mail_smtppass;
            }
        }
        else
            $this->Mailer = "sendmail";
    }

    /**
     * Attaches all fs, string, and binary attachments to the message.
     * Returns an empty string on failure.
     * @access private
     * @return string
     */
    function AttachAll() {
        // Return text of body
        $mime = array();

        // Add all attachments
        for($i = 0; $i < count($this->attachment); $i++) {
            // Check for string attachment
            $bString = $this->attachment[$i][5];
            if ($bString) {
                $string = $this->attachment[$i][0];
            } else {
                $path = $this->attachment[$i][0];
            }

            // cn: overriding parent class' method to perform encode on the following
            $filename    = $this->EncodeHeader(trim($this->attachment[$i][1]));
            $name        = $this->EncodeHeader(trim($this->attachment[$i][2]));
            $encoding    = $this->attachment[$i][3];
            $type        = $this->attachment[$i][4];
            $disposition = $this->attachment[$i][6];
            $cid         = $this->attachment[$i][7];

            $mime[] = sprintf("--%s%s", $this->boundary[1], $this->LE);
            $mime[] = sprintf("Content-Type: %s; name=\"%s\"%s", $type, $name, $this->LE);
            $mime[] = sprintf("Content-Transfer-Encoding: %s%s", $encoding, $this->LE);

            if($disposition == "inline") {
                $mime[] = sprintf("Content-ID: <%s>%s", $cid, $this->LE);
            }

            $mime[] = sprintf("Content-Disposition: %s; filename=\"%s\"%s", $disposition, $name, $this->LE.$this->LE);

            // Encode as string attachment
            if($bString) {
                $mime[] = $this->EncodeString($string, $encoding);
                if($this->IsError()) { return ""; }
                $mime[] = $this->LE.$this->LE;
            } else {
                $mime[] = $this->EncodeFile($path, $encoding);

                if($this->IsError()) {
                    return "";
                }
                $mime[] = $this->LE.$this->LE;
            }
        }
        $mime[] = sprintf("--%s--%s", $this->boundary[1], $this->LE);

        return join("", $mime);
    }

    /**
     * handles Charset translation for all visual parts of the email.
     * @param string charset Default = ''
     */
    function prepForOutbound() {
        global $locale;

        if($this->preppedForOutbound == false) {
            //bug 28534. We should not set it to true to circumvent the following conversion as each email is independent.
            //$this->preppedForOutbound = true; // flag so we don't redo this
            $OBCharset = $locale->getPrecedentPreference('default_email_charset');

            // handle disclosure
            if($this->disclosureEnabled) {
                $this->Body .= "<br />&nbsp;<br />{$this->disclosureText}";
                $this->AltBody .= "\r\r{$this->disclosureText}";
            }

            // body text
            $this->Body		= from_html($locale->translateCharset(trim($this->Body), 'UTF-8', $OBCharset));
            $this->AltBody		= from_html($locale->translateCharset(trim($this->AltBody), 'UTF-8', $OBCharset));
            $subjectUTF8		= from_html(trim($this->Subject));
            $subject			= $locale->translateCharset($subjectUTF8, 'UTF-8', $OBCharset);
            $this->Subject		= $locale->translateCharset($subjectUTF8, 'UTF-8', $OBCharset);

            // HTML email RFC compliance
            if($this->ContentType == "text/html") {
                if(strpos($this->Body, '<html') === false) {

                    $langHeader = get_language_header();

                    $head=<<<eoq
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" {$langHeader}>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$OBCharset}" />
<title>{$subject}</title>
</head>
<body>
eoq;
                    $this->Body = $head.$this->Body."</body></html>";
                }
            }

            // Headers /////////////////////////////////
            // the below is done in PHPMailer::CreateHeader();
            //$this->Subject			= $locale->translateCharsetMIME(trim($this->Subject), 'UTF-8', $locale->getPrecedentPreference('default_email_charset'));
            $this->FromName		= $locale->translateCharset(trim($this->FromName), 'UTF-8', $OBCharset);
            /*
               foreach($this->ReplyTo as $k => $v) {
                   $this->ReplyTo[$k][1] = $locale->translateCharset(trim($v[1]), 'UTF-8', $OBCharset);
               }
               // TO: fields
               foreach($this->to as $k => $toArr) {
                   $this->to[$k][1]	= $locale->translateCharset(trim($toArr[1]), 'UTF-8', $OBCharset);
               }
               // CC: fields
               foreach($this->cc as $k => $ccAddr) {
                   $this->cc[$k][1]	= $locale->translateCharset(trim($ccAddr[1]), 'UTF-8', $OBCharset);
               }
               // BCC: fields
               foreach($this->bcc as $k => $bccAddr) {
                   $this->bcc[$k][1]	= $locale->translateCharset(trim($bccAddr[1]), 'UTF-8', $OBCharset);
               }
               */

        }
    }

    /**
     * Replace images with locations specified by regex with cid: images
     * and attach needed files
     * @param string $regex Regular expression
     * @param string $local_prefix Prefix where local files are stored
     * @param bool $object Use attachment object
     */
    public function replaceImageByRegex($regex, $local_prefix, $object = false)
    {
        preg_match_all("#<img[^>]*[\s]+src[^=]*=[\s]*[\"']($regex)(.+?)[\"']#si", $this->Body, $matches);
        $i = 0;
        foreach($matches[2] as $match) {
            $filename = urldecode($match);
            $cid = $filename;
            $file_location = $local_prefix.$filename;
            if(!file_exists($file_location)) continue;
            if($object) {
                if(preg_match('#&(?:amp;)?type=([\w]+)#i', $matches[0][$i], $typematch)) {
                    switch(strtolower($typematch[1])) {
                        case 'documents':
                            $beanname = 'DocumentRevisions';
                            break;
                        case 'notes':
                            $beanname = 'Notes';
                            break;
                    }
                }
                $mime_type = "application/octet-stream";
                if(isset($beanname)) {
                    $bean = SugarModule::get($beanname)->loadBean();
                    $bean->retrieve($filename);
                    if(!empty($bean->id)) {
                        $mime_type = $bean->file_mime_type;
                        $filename = $bean->filename;
                    }
                }
            } else {
                $mime_type = "image/".strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            }
            $this->AddEmbeddedImage($file_location, $cid, $filename, 'base64', $mime_type);
            $i++;
        }
        //replace references to cache with cid tag
        $this->Body = preg_replace("|\"$regex|i",'"cid:',$this->Body);
        // remove bad img line from outbound email
        $this->Body = preg_replace('#<img[^>]+src[^=]*=\"\/([^>]*?[^>]*)>#sim', '', $this->Body);
    }

    /**
     * @param notes	array of note beans
     */
    function handleAttachments($notes) {
        global $sugar_config;

        //replace references to cache/images with cid tag
        $this->Body = str_replace(sugar_cached('images/'),'cid:',$this->Body);

        if (empty($notes)) {
            return;
        }
        // cn: bug 4864 - reusing same SugarPHPMailer class, need to clear attachments
        $this->ClearAttachments();

        $this->replaceImageByRegex("(?:{$sugar_config['site_url']})?/?cache/images/", sugar_cached("images/"));

        //Replace any embeded images using the secure entryPoint for src url.
        $this->replaceImageByRegex("(?:{$sugar_config['site_url']})?index.php[?]entryPoint=download&(?:amp;)?[^\"]+?id=", "upload://", true);

        //Handle regular attachments.
        foreach($notes as $note) {
            $mime_type = 'text/plain';
            $file_location = '';
            $filename = '';

            if($note->object_name == 'Note') {
                if (! empty($note->file->temp_file_location) && is_file($note->file->temp_file_location)) {
                    $file_location = $note->file->temp_file_location;
                    $filename = $note->file->original_file_name;
                    $mime_type = $note->file->mime_type;
                } else {
                    $file_location = "upload://{$note->id}";
                    $filename = $note->id.$note->filename;
                    $mime_type = $note->file_mime_type;
                }
            } elseif($note->object_name == 'DocumentRevision') { // from Documents
                $filename = $note->id.$note->filename;
                $file_location = "upload://$filename";
                $mime_type = $note->file_mime_type;
            }

            $filename = substr($filename, 36, strlen($filename)); // strip GUID	for PHPMailer class to name outbound file
            if (!$note->embed_flag) {
                $this->AddAttachment($file_location, $filename, 'base64', $mime_type);
            } // else
        }
    }

    /**
     * overloads class.phpmailer's SetError() method so that we can log errors in sugarcrm.log
     *
     */
    function SetError($msg) {
        $GLOBALS['log']->fatal("SugarPHPMailer encountered an error: {$msg}");
        parent::SetError($msg);
    }

    function SmtpConnect() {
        $connection = parent::SmtpConnect();
        if (!$connection) {
            global $app_strings;
            if(isset($this->oe) && $this->oe->type == "system") {
                $this->SetError($app_strings['LBL_EMAIL_INVALID_SYSTEM_OUTBOUND']);
            } else {
                $this->SetError($app_strings['LBL_EMAIL_INVALID_PERSONAL_OUTBOUND']);
            } // else
        }
        return $connection;
    } // fn

} // end class definition
