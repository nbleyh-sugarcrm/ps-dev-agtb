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
$fields = array(
    'category_name',
    'discount_price',
    'tax_class',
    'mft_part_num',
    'weight'
);

$serviceFieldDefaults = array(
    'service_start_date' => 'now()',
    'service_duration_value' => '1',
    'service_duration_unit' => '"year"',
);

$dependencies['RevenueLineItems']['read_only_fields'] = array(
    'hooks' => array("edit"),
    //Trigger formula for the dependency. Defaults to 'true'.
    'trigger' => 'true',
    'triggerFields' => array('product_template_name'),
    'onload' => true,
    //Actions is a list of actions to fire when the trigger is true
    'actions' => array(),
);

foreach ($fields as $field) {
    $dependencies['RevenueLineItems']['read_only_fields']['actions'][] = array(
        'name' => 'ReadOnly', //Action type
        //The parameters passed in depend on the action type
        'params' => array(
            'target' => $field,
            'label' => $field . '_label', //normally <field>_label
            'value' => 'not(equal($product_template_name,""))', //Formula
        ),
    );
}

/**
 * This dependency set the commit_stage to the correct value and to read only when the sales stage
 * is Closed Won (include) or Closed Lost (exclude)
 */
$dependencies['RevenueLineItems']['commit_stage_readonly_set_value'] = array(
    'hooks' => array("edit"),
    //Trigger formula for the dependency. Defaults to 'true'.
    'trigger' => 'true',
    'triggerFields' => array('sales_stage'),
    'onload' => true,
    //Actions is a list of actions to fire when the trigger is true
    'actions' => array(
        array(
            'name' => 'ReadOnly', //Action type
            //The parameters passed in depend on the action type
            'params' => array(
                'target' => 'commit_stage',
                'label' => 'commit_stage_label', //normally <field>_label
                'value' => 'isForecastClosed($sales_stage)', //Formula
            ),
        ),
        array(
            'name' => 'SetValue', //Action type
            //The parameters passed in depend on the action type
            'params' => array(
                'target' => 'commit_stage',
                'label' => 'commit_stage_label', //normally <field>_label
                'value' => 'ifElse(isForecastClosedWon($sales_stage), "include",
                    ifElse(isForecastClosedLost($sales_stage), "exclude", $commit_stage))', //Formula
            ),
        )
    ),
);

$dependencies['RevenueLineItems']['set_base_rate'] = array(
    'hooks' => array("edit"),
    //Trigger formula for the dependency. Defaults to 'true'.
    'trigger' => 'true',
    'triggerFields' => array('sales_stage'),
    'onload' => true,
    //Actions is a list of actions to fire when the trigger is true
    'actions' => array(
        array(
            'name' => 'SetValue', //Action type
            //The parameters passed in depend on the action type
            'params' => array(
                'target' => 'base_rate',
                'label' => 'base_rate_lable', //normally <field>_label
                'value' => 'ifElse(isForecastClosed($sales_stage), $base_rate, currencyRate($currency_id))', //Formula
            ),
        )
    )
);

/**
 * This dependency set the best and worst values to equal likely when the sales stage is
 * set to closed won.
 */
$dependencies['RevenueLineItems']['best_worst_sales_stage_read_only'] = array(
    'hooks' => array("edit"),
    //Trigger formula for the dependency. Defaults to 'true'.
    'trigger' => 'true',
    'triggerFields' => array('sales_stage'),
    'onload' => true,
    //Actions is a list of actions to fire when the trigger is true
    'actions' => array(
        array(
            'name' => 'ReadOnly', //Action type
            //The parameters passed in depend on the action type
            'params' => array(
                'target' => 'best_case',
                'label' => 'best_case_label', //normally <field>_label
                'value' => 'isForecastClosed($sales_stage)', //Formula
            ),
        ),
        array(
            'name' => 'ReadOnly', //Action type
            //The parameters passed in depend on the action type
            'params' => array(
                'target' => 'worst_case',
                'label' => 'worst_case_label', //normally <field>_label
                'value' => 'isForecastClosed($sales_stage)', //Formula
            ),
        ),
        array(
            'name' => 'SetValue', //Action type
            //The parameters passed in depend on the action type
            'params' => array(
                'target' => 'best_case',
                'label' => 'best_case_label',
                'value' => 'string(ifElse(isForecastClosed($sales_stage), $likely_case, $best_case))',
            ),
        ),
        array(
            'name' => 'SetValue', //Action type
            //The parameters passed in depend on the action type
            'params' => array(
                'target' => 'worst_case',
                'label' => 'worst_case_label',
                'value' => 'string(ifElse(isForecastClosed($sales_stage), $likely_case, $worst_case))',
            ),
        ),
    )
);

$dependencies['RevenueLineItems']['likely_case_copy_when_closed'] = array(
    'hooks' => array("edit"),
    //Trigger formula for the dependency. Defaults to 'true'.
    'trigger' => 'true',
    'triggerFields' => array('likely_case'),
    'onload' => true,
    //Actions is a list of actions to fire when the trigger is true
    'actions' => array(
        array(
            'name' => 'SetValue', //Action type
            //The parameters passed in depend on the action type
            'params' => array(
                'target' => 'best_case',
                'label' => 'best_case_label',
                'value' => 'string(ifElse(isForecastClosed($sales_stage), $likely_case, $best_case))',
            ),
        ),
        array(
            'name' => 'SetValue', //Action type
            //The parameters passed in depend on the action type
            'params' => array(
                'target' => 'worst_case',
                'label' => 'worst_case_label',
                'value' => 'string(ifElse(isForecastClosed($sales_stage), $likely_case, $worst_case))',
            ),
        ),
    )
);

// Handle the dependencies when the 'service' field is checked/unchecked
$serviceFieldActions = array();
foreach ($serviceFieldDefaults as $field => $defaultValue) {
    $serviceFieldActions[] = array(
        'name' => 'ReadOnly',
        'params' => array(
            'target' => $field,
            'value' => 'equal($service, "0")',
        ),
    );
    $serviceFieldActions[] = array(
        'name' => 'SetRequired',
        'params' => array(
            'target' => $field,
            'value' => 'equal($service, "1")',
        ),
    );
    $serviceFieldActions[] = array(
        'name' => 'SetValue',
        'params' => array(
            'target' => $field,
            'value' => 'ifElse(
                equal($service, "1"),
                ifElse(
                    equal($' . $field . ', ""),
                    '. $defaultValue .',
                    $'. $field .'
                ),
                "")',
        ),
    );
}

// 'renewable' field is similar to the other service fields, but never required
$serviceFieldActions[] = array(
    'name' => 'ReadOnly',
    'params' => array(
        'target' => 'renewable',
        'value' => 'equal($service, "0")',
    ),
);
$serviceFieldActions[] = array(
    'name' => 'SetValue',
    'params' => array(
        'target' => 'renewable',
        'value' => 'ifElse(
                equal($service, "1"),
                $renewable,
                "0")',
    ),
);
$dependencies['RevenueLineItems']['handle_service_dependencies'] = array(
    'hooks' => array('edit'),
    'trigger' => 'true',
    'triggerFields' => array('service'),
    'onload' => true,
    'actions' => $serviceFieldActions,
);
