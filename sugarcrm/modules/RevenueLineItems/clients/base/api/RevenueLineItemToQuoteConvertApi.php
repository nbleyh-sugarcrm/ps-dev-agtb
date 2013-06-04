<?php
//FILE SUGARCRM flav=pro ONLY
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

require_once('include/api/SugarApi.php');
class RevenueLineItemToQuoteConvertApi extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'convert' => array(
                'reqType' => 'POST',
                'path' => array('RevenueLineItems', '?', 'quote'),
                'pathVars' => array('module', 'record', 'action'),
                'method' => 'convertToQuote',
                'shortHelp' => 'Convert a Revenue Line Item Into A Quote Record',
                'longHelp' => 'modules/RevenueLineItems/clients/base/api/help/convert_to_quote.html',
            ),
        );
    }

    public function convertToQuote(ServiceBase $api, array $args)
    {
        // load up the Product
        /* @var $rli RevenueLineItem */
        $rli = BeanFactory::getBean('RevenueLineItems', $args['record']);

        if (empty($rli->id)) {
            // throw a 404 (Not Found) if the rli is not found
            throw new SugarApiExceptionNotFound();
        }

        /* @var $product Product */
        $product = $rli->convertToQuotedLineItem();
        $product->save();

        // lets create a new bundle
        /* @var $product_bundle ProductBundle */
        $product_bundle = BeanFactory::getBean('ProductBundles');

        $total = SugarMath::init()->exp("?*?", array($product->quantity, $product->likely_case))->result();
        $total_base = SugarCurrency::convertWithRate($total, $product->base_rate);

        $product_bundle->bundle_stage = 'Draft';
        $product_bundle->total = $total;
        $product_bundle->total_usdollar = $total_base;
        $product_bundle->subtotal = $total;
        $product_bundle->subtotal_usdollar = $total_base;
        $product_bundle->deal_tot = $total;
        $product_bundle->deal_tot_usdollar = $total_base;
        $product_bundle->new_sub = $total;
        $product_bundle->new_sub_usdollar = $total_base;
        $product_bundle->tax = 0.00;
        $product_bundle->tax_usdollar = 0.00;
        $product_bundle->currency_id = $product->currency_id;
        $product_bundle->save();
        $product_bundle->load_relationship('products');
        $product_bundle->products->add($product, array('product_index' => 1));

        // now that we have the product bundle, lets create the quote
        /* @var $quote Quote */
        $quote = BeanFactory::getBean('Quotes');

        $quote->total = $total;
        $quote->total_usdollar = $total_base;
        $quote->subtotal = $total;
        $quote->subtotal_usdollar = $total_base;
        $quote->deal_tot = $total;
        $quote->deal_tot_usdollar = $total_base;
        $quote->new_sub = $total;
        $quote->new_sub_usdollar = $total_base;
        $quote->tax = 0.00;
        $quote->tax_usdollar = 0.00;
        $quote->currency_id = $product->currency_id;
        $quote->opportunity_id = $product->opportunity_id;
        $quote->quote_stage = "Draft";
        $quote->assigned_user_id = $GLOBALS['current_user']->id;

        // get the account from the product
        /* @var $account Account */
        $account = BeanFactory::getBean('Accounts', $product->account_id);
        if (isset($account->id)) {
            $quote->billing_address_street = $account->billing_address_street;
            $quote->billing_address_city = $account->billing_address_city;
            $quote->billing_address_country = $account->billing_address_country;
            $quote->billing_address_state = $account->billing_address_state;
            $quote->billing_address_postalcode = $account->billing_address_postalcode;

            $quote->shipping_address_street = $account->shipping_address_street;
            $quote->shipping_address_city = $account->shipping_address_city;
            $quote->shipping_address_country = $account->shipping_address_country;
            $quote->shipping_address_state = $account->shipping_address_state;
            $quote->shipping_address_postalcode = $account->shipping_address_postalcode;
        }


        $quote->save();

        $quote->set_relationship(
            'quotes_accounts',
            array('quote_id' => $quote->id, 'account_id' => $product->account_id, 'account_role' => 'Bill To'),
            false
        );

        $quote->set_relationship(
            'quotes_accounts',
            array('quote_id' => $quote->id, 'account_id' => $product->account_id, 'account_role' => 'Ship To'),
            false
        );

        $quote->set_relationship(
            'product_bundle_quote',
            array('quote_id' => $quote->id, 'bundle_id' => $product_bundle->id, 'bundle_index' => 0)
        );

        # Set the quote_id on the product so we know it's linked
        $product->quote_id = $quote->id;
        $rli->quote_id = $quote->id;
        $product->status = Product::STATUS_QUOTED;
        $rli->status = RevenueLineItem::STATUS_QUOTED;
        $product->save();
        $rli->save();

        return array('id' => $quote->id, 'name' => $quote->name);

    }
}
