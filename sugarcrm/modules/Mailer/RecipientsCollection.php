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

require_once 'EmailIdentity.php';

class RecipientsCollection
{
	const FunctionAddTo  = 'addTo';
	const FunctionAddCc  = 'addCc';
	const FunctionAddBcc = 'addBcc';

	protected $to;
	protected $cc;
	protected $bcc;

	public function __construct() {
		$this->clearAll(); // set default values
	}

	public function __destruct() {
		$this->clearAll();
	}

	public function clearAll() {
		$this->clearTo();
		$this->clearCc();
		$this->clearBcc();
	}

	public function clearTo() {
		$this->to = array();
	}

	public function clearCc() {
		$this->cc = array();
	}

	public function clearBcc() {
		$this->bcc = array();
	}

	/**
	 * @param array  $recipients    Array of EmailIdentity objects.
	 * @param string $function      The name of the RecipientsCollection method to use for adding recipients.
	 * @return array    Array of invalid recipients
	 */
	public function addRecipients($recipients = array(), $function = RecipientsCollection::FunctionAddTo) {
		$recipients = $this->castRecipientsAsArray($recipients);
		$invalidRecipients = array();

		foreach ($recipients as $recipient) {
			if ($recipient instanceof EmailIdentity) {
				$this->$function($recipient);
			} else {
				$invalidRecipients[] = $recipient;
			}
		}

		return $invalidRecipients;
	}

	/**
	 * Use the recipient's email address as the key to avoid adding duplicate recipients. This will overwrite a
	 * recipient if a duplicate does exist. However, duplicates can still exist within the different recipient
	 * classifications (to/cc/bcc).
	 *
	 * @todo consider returning response codes to indicate success/failure/overwrite
	 *
	 * @param EmailIdentity $recipient
	 */
	public function addTo(EmailIdentity $recipient) {
		$this->to[$recipient->getEmail()] = $recipient;
	}

	/**
	 * Use the recipient's email address as the key to avoid adding duplicate recipients. This will overwrite a
	 * recipient if a duplicate does exist. However, duplicates can still exist within the different recipient
	 * classifications (to/cc/bcc).
	 *
	 * @todo consider returning response codes to indicate success/failure/overwrite
	 *
	 * @param EmailIdentity $recipient
	 */
	public function addCc(EmailIdentity $recipient) {
		$this->cc[$recipient->getEmail()] = $recipient;
	}

	/**
	 * Use the recipient's email address as the key to avoid adding duplicate recipients. This will overwrite a
	 * recipient if a duplicate does exist. However, duplicates can still exist within the different recipient
	 * classifications (to/cc/bcc).
	 *
	 * @todo consider returning response codes to indicate success/failure/overwrite
	 *
	 * @param EmailIdentity $recipient
	 */
	public function addBcc(EmailIdentity $recipient) {
		$this->bcc[$recipient->getEmail()] = $recipient;
	}

	public function getAll() {
		return array(
			'to'  => $this->getTo(),
			'cc'  => $this->getCc(),
			'bcc' => $this->getBcc(),
		);
	}

	public function getTo() {
		return $this->to;
	}

	public function getCc() {
		return $this->cc;
	}

	public function getBcc() {
		return $this->bcc;
	}

	protected function castRecipientsAsArray($recipients) {
		if (!is_array($recipients)) {
			$recipients = array($recipients);
		}

		return $recipients;
	}
}
