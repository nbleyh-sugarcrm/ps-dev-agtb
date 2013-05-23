<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Professional End User
 * License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You may
 * not use this file except in compliance with the License. Under the terms of the
 * license, You shall not, among other things: 1) decodesublicense, resell, rent, lease,
 * redistribute, assign or otherwise transfer Your rights to the Software, and 2)
 * use the Software for timesharing or service bureau purposes such as hosting the
 * Software for commercial gain and/or for the benefit of a third party.  Use of
 * the Software may be subject to applicable fees and any use of the Software
 * without first paying applicable fees is strictly prohibited.  You do not have
 * the right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.  Your Warranty, Limitations of liability and Indemnity are
 * expressly stated in the License.  Please refer to the License for the specific
 * language governing these rights and limitations under the License.
 * Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.;
 * All Rights Reserved.
 ********************************************************************************/

/**
 *  LOOKUP/RESOLUTION RULES
 *
(1)
If ID and module are not present, then use the email address to look for matching records in the database.
If a match is found, then include the record's ID and module in the return value.
If an email address is associated with more than one record, then return the first record.

(2)
If ID and module are present, then first validate the existence of the record before including the record's email
address and name in the return value. If the record does not exist, then set as Unresolved.

(3)
If an ID is present without a module, then ignore the ID.

(4)
If an email address and module are present without an ID, then search for records by the email address. Select the
record with the matching module if one exists and return that record's values.
Otherwise, set as Unresolved.

(5)
If an email address and ID are present without a module, then search for records by the email address. Select the
record with an ID matching the supplied ID if one exists. Otherwise, set as Unresolved.

(6)
If an email address is present and no module or ID provided, then search for records by the email address.
Select a record with a matching email_address if one exists. Note that the record selected is unpredictable
if multiple records exist for the supplied email address.

(5)
If No ID or Email Address provided, set recipient as unResolved

(6)
If a match is found, do not overwrite any parameters with data found on the Bean; the data passed in is
prioritized over the data found on the Bean.

(7)
If no name is associated with the recipient after matching a record, then include the email address as the name.
 */


/**
 * Lookup and Resolve Recipients
 */
class RecipientLookup
{
    protected $sugarEmail = null;
    protected $beanNames = null;

    /**
     * This function accepts a Recipient that provides with one or more of: ID,Module,Email,Name and tries
     * to resolve any of the fields not provided using resolution rules above.
     *
     * @param $recipient
     * @return array
     */
    public function lookup(array $recipient=array())
    {
        $recipient['resolved'] = false;
        $beanId = null;

        if (!empty($recipient['id']) && empty($recipient['module'])) {
            $beanId = $recipient['id'];
            $recipient['id'] = '';
        }

        if (!empty($recipient['id']) && !empty($recipient['module'])) {
            // If ID and module are present, then resolve recipient using the supplied ID
            $this->lookupRecipientById($recipient);
        } elseif (empty($recipient['id'])) {
            // If ID is Not Present, then then use the email address to look for matching records in the database.
            if (!empty($recipient['email'])) {
                $this->lookupRecipientByEmailAddress($beanId, $recipient);
            }
        }

        if ($recipient['id'] == '' && $beanId != null && !$recipient['resolved']) {
            // Restore original beanId if it was provided and no other valid ID resolution occurred
            $recipient['id'] = $beanId;
        }

        if ($recipient['resolved'] && empty($recipient['name'])) {
            $recipient['name'] = $recipient['email'];
        }

        return $recipient;
    }

    /**
     * This function looks up and resolves recipients that have the specified
     * ID and module.
     *
     * @param $recipient
     * @return array
     */
    protected function lookupRecipientById(&$recipient)
    {
        $bean = BeanFactory::getBean($recipient['module'], $recipient['id']);
        if (!empty($bean) && !empty($bean->id) && $bean->id == $recipient['id']) {
            $recipient['resolved'] = true;
            if (empty($recipient['name'])) {
                $recipient['name'] = empty($bean->name) ? '' : $bean->name;
            }
            if (empty($recipient['email'])) {
                $recipient['email'] = empty($bean->email1) ? '' : $bean->email1;
            }
        }
    }

    /**
     * This function looks up and resolves recipients that have the specified email address.
     * Multiple rows are resolved accordi g to the Resolution Rules above.
     *
     * @param $recipient
     * @return array
     */
    protected function lookupRecipientByEmailAddress($beanId, &$recipient)
    {
        global $beanList;

        if ($this->sugarEmail == null) {
            $this->sugarEmail = new SugarEmailAddress();
        }
        $beans = $this->sugarEmail->getBeansByEmailAddress($recipient['email']);
        if (!empty($beans)) {
            if ($this->beanNames == null) {
                // array_flip is done lazily as this method may be called many times per object instance.
                // We use array_flip as a performance enhancement providing Keyed Lookup over Sequential Lookup
                $this->beanNames = array_flip($beanList);
            }
            foreach ($beans AS $bean) {
                $beanType = get_class($bean);
                if (isset($this->beanNames[$beanType])) {
                    $module = $this->beanNames[$beanType];
                    if (empty($recipient['module']) || $module == $recipient['module']) {
                        if ($beanId == null || ($beanId == $bean->id)) {
                            $recipient['resolved'] = true;
                            $recipient['module'] = $module;
                            $recipient['id'] = $bean->id;
                            if (empty($recipient['email'])) {
                                $recipient['email'] = empty($bean->email1) ? '' : $bean->email1;
                            }
                            if (empty($recipient['name'])) {
                                $recipient['name'] = empty($bean->name) ? '' : $bean->name;
                            }
                            break;
                        }
                    }
                }
            }
        }
    }

}
