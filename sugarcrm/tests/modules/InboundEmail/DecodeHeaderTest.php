<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Professional End User
 * License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You may
 * not use this file except in compliance with the License. Under the terms of the
 * license, You shall not, among other things: 1) sublicense, resell, rent, lease,
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
 

require_once('modules/InboundEmail/InboundEmail.php');

/**
 * @ticket 49983
 */
class DecodeHeaderTest extends Sugar_PHPUnit_Framework_TestCase
{
	protected $ie = null;

	public function setUp()
    {
		$this->ie = new InboundEmail();
	}

    public function getDecodingHeader()
    {
        return array(
            array('Content-Type: text/html; charset="utf-8"', 
                array(
                    "Content-Type" => array(
                        "type" => "text/html",
                        "charset" => "utf-8",
                        ),
                    ),
                ),
            array('Content-Type: text/html; charset=utf-8', 
                array(
                    "Content-Type" => array(
                        "type" => "text/html",
                        "charset" => "utf-8",
                        ),
                    ),
                ),
            array('Content-Type: text/html; charset=    utf-8', 
                array(
                    "Content-Type" => array(
                        "type" => "text/html",
                        "charset" => "utf-8",
                        ),
                    ),
                ),
            );
    }

    /**
     * @dataProvider getDecodingHeader
     * @param string $url
     */
	function testDecodingHeader($header, $res)
	{
	    $ie = new InboundEmail();
	    $this->assertEquals($res,$ie->decodeHeader($header));
	}


    public function intlTextProvider()
    {
        return array(
            // commenting out windows-1256, since PHP doesn't have an easy way to detect this encoding.
//            array(
//                '7cvU3iDI5d7L7O3TIOUg1cfU7N0g3c4g5ezR1NPd5eHU3csg287a',
//                'يثشق بهقثىيس ه صاشىف فخ هىرشسفهلشفث غخع',
//                // 'windows-1256'
//            ),
//            array(
//                '7cjT7cjU0+3IwcbExNE=',
//                'يبسيبشسيبءئؤؤر',
//                // 'windows-1256'
//            ),
            array( // params related to 45059 ticket
                'GyRCJWYhPCU2TD4bKEI=',
                'ユーザ名',
                // 'ISO-2022-JP'
            ),
            array(
                '5LiN6KaB55u06KeG6ZmM55Sf5Lq655qE55y8552b',
                '不要直视陌生人的眼睛',
                // 'utf-8'
            )
        );

    }
    /**
     * @group bug45059
     * @dataProvider intlTextProvider
     * @param string $inputText - our input from the provider, base64'ed
     * @param string $expected - what our goal is
     */
    public function testConvertToUtf8($inputText, $expected)
    {
        // the email server is down, so this test doesn't work
        if (!function_exists('mb_convert_encoding')) {
            $this->markTestSkipped('Need multibyte encoding support');
        }

        $ie = new InboundEmail();
        $inputText = base64_decode($inputText);
        $this->assertEquals($expected, $ie->convertToUtf8($inputText), 'We should be able to convert to UTF-8');
    }

}