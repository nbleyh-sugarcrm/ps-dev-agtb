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

require_once 'lib/phpmailer/class.phpmailer.php';
require_once 'lib/phpmailer/class.smtp.php';
require_once 'BaseMailer.php';

class SimpleMailer extends BaseMailer
{
	const Protocol   = 'smtp';
	const SecureNone = '';
	const SecureSsl  = 'ssl';
	const SecureTls  = 'tls';

	public function reset() {
		parent::reset();
		$this->mailer = new PHPMailer();
		$this->mailer->SetLanguage(); // reset to the English language pack
	}

	public function loadDefaultConfigs() {
		parent::loadDefaultConfigs();

		$defaults = array(
			'smtp.host'         => 'localhost',
			'smtp.port'         => 25,
			'smtp.secure'       => self::SecureNone,
			'smtp.authenticate' => false,
			'smtp.username'     => '',
			'smtp.password'     => '',
			'smtp.timeout'      => 10,
			'smtp.persist'      => false,
		);

		$this->mergeConfigs($defaults);
	}

	public function send() {
		try {
			if (!($this->mailer instanceof PHPMailer)) {
				throw new MailerException("Invalid mailer");
			}

			$this->transferConfigurations();
			$this->connectToHost();
			$this->transferHeaders();
			$this->transferRecipients();
			$this->transferBody();
			$this->transferAttachments();

			if (!$this->mailer->IsError()) {
				$this->mailer->Send();
			}

			if ($this->mailer->IsError()) {
				throw new MailerException($this->mailer->ErrorInfo);
			}
		} catch (MailerException $me) {
			//@todo consider using status codes and grouping them based on the error level that should be used
			// so that different error levels can be logged
			// could also catch different Exception classes that extend MailerException and log at the level
			// particular to that exception type
			$me->log('error');
			return false;
		}

		return true;
	}

	private function transferConfigurations() {
		// transfer the basic configurations
		$this->mailer->Mailer   = self::Protocol;
		$this->mailer->Hostname = $this->configs['hostname'];
		$this->mailer->CharSet  = $this->configs['charset'];
		$this->mailer->Encoding = $this->configs['encoding'];
		$this->mailer->WordWrap = $this->configs['wordwrap'];

		// transfer the smtp configurations
		$this->mailer->Host          = $this->configs['smtp.host'];
		$this->mailer->Port          = $this->configs['smtp.port'];
		$this->mailer->SMTPSecure    = $this->configs['smtp.secure'];
		$this->mailer->SMTPAuth      = $this->configs['smtp.authenticate'];
		$this->mailer->Username      = $this->configs['smtp.username'];
		$this->mailer->Password      = $this->configs['smtp.password']; //@todo do we need to wrap this value in from_html()?
		$this->mailer->Timeout       = $this->configs['smtp.timeout'];
		$this->mailer->SMTPKeepAlive = $this->configs['smtp.persist'];
	}

	private function connectToHost() {
		//@todo may need to reuse the SMTP object in the event that there is a valid use case for
		// keeping the SMTP connection alive
		$this->mailer->smtp = new SMTP();

		if (!$this->mailer->SmtpConnect()) {
			//@todo need to tell the class what error messages to use, so the following is for reference only
//			global $app_strings;
//			if(isset($this->oe) && $this->oe->type == "system") {
//				$this->SetError($app_strings['LBL_EMAIL_INVALID_SYSTEM_OUTBOUND']);
//			} else {
//				$this->SetError($app_strings['LBL_EMAIL_INVALID_PERSONAL_OUTBOUND']);
//			}
			throw new MailerException("Failed to connect to the remote server");
		}
	}

	private function transferHeaders() {
		// packageHeaders() will throw an exception if errors occur and that exception will be caught by send()
		$headers = $this->headers->packageHeaders();

		foreach ($headers as $key => $value) {
			switch ($key) {
				case Headers::From:
					$this->mailer->From = $value[0];
					$this->mailer->FromName = $value[1]; //@todo might not want to require this value
					break;
				case Headers::ReplyTo:
					$this->mailer->ClearReplyTos();
					$this->mailer->AddReplyTo($value[0], $value[1]); //@todo might not want to require the second value
					break;
				case Headers::Sender:
					$this->mailer->Sender = $value;
					break;
				case Headers::MessageId:
					$this->mailer->MessageId = $value;
					break;
				case Headers::Priority:
					$this->mailer->Priority = $value;
					break;
				case Headers::DispositionNotificationTo:
					$this->mailer->ConfirmReadingTo = $value;
					break;
				case Headers::Subject:
					$this->mailer->Subject = $value;
					break;
				default:
					// throw it away if it's not a valid header
					break;
			}
		}
	}

	private function transferRecipients() {
		$this->mailer->ClearAllRecipients();
		$to = $this->recipients->getTo();
		$cc = $this->recipients->getCc();
		$bcc = $this->recipients->getBcc();

		//@todo should you be able to initiate a send without any To recipients?
		foreach ($to as $recipient) {
			$recipient->decode();
			$this->mailer->AddAddress($recipient->getEmail(), $recipient->getName());
		}

		foreach ($cc as $recipient) {
			$recipient->decode();
			$this->mailer->AddCC($recipient->getEmail(), $recipient->getName());
		}

		foreach ($bcc as $recipient) {
			$recipient->decode();
			$this->mailer->AddBCC($recipient->getEmail(), $recipient->getName());
		}
	}

	/**
	 * @throws MailerException
	 */
	protected function transferBody() {
		$hasText = $this->hasMessagePart($this->textBody);
		$hasHtml = $this->hasMessagePart($this->htmlBody);

		if (!$hasText && !$hasHtml) {
			throw new MailerException("No email body was provided");
		}

		if ($hasHtml) {
			$this->mailer->IsHTML(true);
			$this->mailer->Encoding = self::EncodingBase64; // so that embedded images are encoding properly
			$this->mailer->Body = $this->htmlBody;
		}

		if ($hasText && $hasHtml) {
			$this->mailer->AltBody = $this->textBody;
		} elseif ($hasText) {
			$this->mailer->Body = $this->textBody;
		} else {
			// you should never actually send an email without a plain-text part, but we'll allow it (for now)
			//throw new MailerException("No text body was provided");
		}
	}

	/**
	 * Transfers both file attachments and embedded images to PHPMailer.
	 *
	 * @throws MailerException
	 */
	private function transferAttachments() {
		$this->mailer->ClearAttachments();

		foreach ($this->attachments as $attachment) {
			if ($attachment instanceof Attachment) {
				if (!$this->mailer->AddAttachment(
					$attachment->getPath(),
					$attachment->getName(),
					$attachment->getEncoding(),
					$attachment->getMimeType())
				) {
					throw new MailerException("Invalid attachment");
				}
			} elseif ($attachment instanceof EmbeddedImage) {
				if (!$this->mailer->AddEmbeddedImage(
					$attachment->getPath(),
					$attachment->getCid(),
					$attachment->getName(),
					$attachment->getEncoding(),
					$attachment->getMimeType())
				) {
					throw new MailerException("Invalid image");
				}
			} else {
				throw new MailerException("Invalid file");
			}
		}
	}
}
