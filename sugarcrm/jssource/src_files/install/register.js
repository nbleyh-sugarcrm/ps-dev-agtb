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
// $Id: register.js 8223 2005-10-03 23:55:49Z lam $

function submitbutton()
{
   var form = document.mosForm;
   var r = new RegExp("[^0-9A-Za-z]", "i");

   if (form.email1.value != "")
   {
      var myString = form.email1.value;
      var pattern = /(\W)|(_)/g;
      var adate = new Date();
      var ms = adate.getMilliseconds();
      var sec = adate.getSeconds();
      var mins = adate.getMinutes();
      ms = ms.toString();
      sec = sec.toString();
      mins = mins.toString();
      newdate = ms + sec + mins;
   
      var newString = myString.replace(pattern,"");
      newString = newString + newdate;
      //form.username.value = newString;
      //form.password.value = newString;
      //form.password2.value = newString;
   }

   // do field validation
   if (form.name.value == "")
   {
      form.name.focus();
      alert( "Please provide your name" );
      return false;
   }
   else if (form.email1.value == "")
   {
      form.email1.focus();
      alert( "Please provide your email address" );
      return false;
   }
   else
   {
      form.submit();
   }

   document.appform.submit();
   window.focus();
}
