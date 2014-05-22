<?php
/************************************
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
require_once("include/Expressions/Expression/Boolean/BooleanExpression.php");

/**
 * <b>isValidEmail(String email)</b><br/>
 * Returns true if <i>email</i> is in a valid email address format. <br/>
 * ex: <i>isValidEmail("invalid@zxcv")</i> = false,<br/>
 * <i>isValidEmail("good@test.com")</i> = true
 */
class IsValidEmailExpression extends BooleanExpression {
	/**
	 * Returns itself when evaluating.
	 */
	function evaluate() {
		$emailStr = $this->getParameters()->evaluate();

		if ($emailStr == "")
            return AbstractExpression::$TRUE;

        $lastChar = $emailStr[strlen($emailStr) - 1];
		if ( !preg_match('/[^\.]/i', $lastChar) )	return AbstractExpression::$FALSE;

		// validate it
		$emailArr = preg_split('/[,;]/', $emailStr);
		for ( $i = 0; $i < sizeof($emailArr) ; $i++) {
            $emailAddress = trim($emailArr[$i]);
            if ($emailAddress != '' && !SugarEmailAddress::isValidEmail($emailAddress)) {
                return AbstractExpression::$FALSE;
            }
		}

		return AbstractExpression::$TRUE;
	}

	/**
	 * Returns the JS Equivalent of the evaluate function.
     *
     * Only performs a very basic validation because the complexity of the server-side regular expression is too great
     * to mirror on the client, both in terms of maintenance and difficulty in porting to a different engine. Even if
     * the light-weight validation passes, the server-side validation may fail.
	 */
	static function getJSEvaluate() {
		return <<<EOQ
		var emailStr = this.getParameters().evaluate();
		
		if ( typeof emailStr != "string" ) return SUGAR.expressions.Expression.FALSE;

		if ( emailStr == "" ) return SUGAR.expressions.Expression.TRUE;
		
		var lastChar = emailStr.charAt(emailStr.length - 1);
		if ( !lastChar.match(/[^\.]/i) )	return SUGAR.expressions.Expression.FALSE;

		// validate it
		var emailArr = emailStr.split(/[,;]/);		// if multiple e-mail addresses
		for (var i = 0; i < emailArr.length; i++) {
			var emailAddress = emailArr[i];
			emailAddress = emailAddress.replace(/^\s+|\s+$/g,"");
            if (emailAddress != '' && !/^.+@.+$/ig.test(emailAddress)) {
                return SUGAR.expressions.Expression.FALSE;
            }
		}

		return SUGAR.expressions.Expression.TRUE;
EOQ;
	}

	/**
	 * Any generic type will suffice.
	 */
	static function getParameterTypes() {
		return array("string");
	}

	/**
	 * Returns the maximum number of parameters needed.
	 */
	static function getParamCount() {
		return 1;
	}

	/**
	 * Returns the opreation name that this Expression should be
	 * called by.
	 */
	static function getOperationName() {
		return "isValidEmail";
	}

	/**
	 * Returns the String representation of this Expression.
	 */
	function toString() {
	}
}
