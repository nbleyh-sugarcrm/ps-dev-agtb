<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

namespace Sugarcrm\SugarcrmTests\Denormalization\Relate\FunctionalityCases\Many2Many;

use SugarBean;
use SugarTestAccountUtilities;
use SugarTestOpportunityUtilities;

class OpportunityAccountFunctionalityTest extends AbstractFunctionalityTest
{
    protected static $options = [
        'primary_module' => 'Opportunities',
        'primary_link_name' => 'opportunities',
        'relate_link_name' => 'accounts',
        'field_name' => 'account_name',
        'relate_field_name' => 'name',
    ];

    protected function createPrimaryBean(?SugarBean $linkedBean): SugarBean
    {
        return SugarTestOpportunityUtilities::createOpportunity(null, $linkedBean);
    }

    protected function createLinkedBean(): SugarBean
    {
        return SugarTestAccountUtilities::createAccount();
    }

    protected static function removeCreatedBeans(): void
    {
        SugarTestAccountUtilities::removeAllCreatedAccounts();
        SugarTestOpportunityUtilities::removeAllCreatedOpportunities();
    }
}
