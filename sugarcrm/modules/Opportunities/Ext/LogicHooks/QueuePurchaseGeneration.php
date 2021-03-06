<?php
//FILE SUGARCRM flav=ent ONLY
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

/**
 * Define the after_save hook that will queue generating purchases from an
 * Opportunity's RLIs when the Opportunity is closed won.
 */
$hook_array['after_save'][] = [
    2,
    'queueRLItoPurchaseJob',
    'modules/Opportunities/OpportunityHooks.php',
    'OpportunityHooks',
    'queueRLItoPurchaseJob',
];
