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

use PHPUnit\Framework\TestCase;

/**
 * Test for SugarWidgetReportField.
 */
class SugarWidgetReportFieldTest extends TestCase
{
    /**
     * Bean to work with.
     * @var SugarBean
     */
    protected $bean;

    /**
     * Definition of layout for SugarWidget.
     * @var array
     */
    protected $layoutDef = [];

    /**
     * @inheritdoc
     */
    protected function setUp() : void
    {
        SugarTestHelper::setUp('beanList');
        SugarTestHelper::setUp('current_user', [true, false]);

        $this->bean = BeanFactory::newBean('Contacts');
        $this->bean->id = create_guid();
        $this->bean->new_with_id = true;
        $this->bean->save();

        $this->layoutDef = [
            'table' => $this->bean->table_name,
            'table_alias' => $this->bean->table_name,
            'input_name0' => [],
            'name' => 'first_name',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function tearDown() : void
    {
        $this->bean->mark_deleted($this->bean->id);
    }

    /**
     * @covers SugarWidgetReportField::queryFilterEmpty
     */
    public function testEmptyMethod()
    {
        $this->bean->first_name = '';
        $this->bean->save();

        $query = $this->getQueryObject();
        $widget = $this->getSugarWidget();

        $query->whereRaw($widget->queryFilterEmpty($this->layoutDef));
        $result = $query->execute();

        $this->assertCount(1, $result);
    }

    /**
     * @covers SugarWidgetReportField::queryFilterNot_Empty
     */
    public function testNotEmptyMethod()
    {
        $this->bean->first_name = 'testNotEmptyMethod';
        $this->bean->save();

        $query = $this->getQueryObject();
        $widget = $this->getSugarWidget();

        $query->whereRaw($widget->queryFilterNot_Empty($this->layoutDef));
        $result = $query->execute();

        $this->assertCount(1, $result);
    }

    /**
     * Check if queryOrderBy attaches the order direction properly
     *
     * @param $layoutDef
     * @param $reportDef
     * @param $expected
     *
     * @dataProvider queryOrderByDataProvider
     * @covers SugarWidgetReportField::queryOrderBy
     */
    public function testQueryOrderBy($layoutDef, $reportDef, $expected)
    {
        $report = new Report(json_encode($reportDef));
        
        $layoutManager = new LayoutManager();
        $layoutManager->setAttributePtr('reporter', $report);

        $sugarWidget = new SugarWidgetReportField($layoutManager);

        $output = $sugarWidget->queryOrderBy($layoutDef);

        $this->assertStringContainsString($expected, $output);
    }

    public static function queryOrderByDataProvider()
    {
        $reportDef = [
            'display_columns' => [
                0 => [
                    'name' => 'full_name',
                    'label' => 'Full Name',
                    'table_key' => 'Accounts:assigned_user_link',
                ],
                1 => [
                    'name' => 'name',
                    'label' => 'Name',
                    'table_key' => 'self',
                ],
            ],
            'module' => 'Accounts',
            'group_defs' => [],
            'summary_columns' => [],
            'order_by' => [
                0 => [
                    'name' => 'full_name',
                    'label' => 'Full Name',
                    'table_key' => 'Accounts:assigned_user_link',
                    'sort_dir' => 'd',
                ],
            ],
            'report_name' => 'Test',
            'do_round' => 1,
            'numerical_chart_column' => '',
            'numerical_chart_column_type' => '',
            'assigned_user_id' => '1',
            'report_type' => 'tabular',
            'full_table_list' => [
                'self' => [
                    'value' => 'Accounts',
                    'module' => 'Accounts',
                    'label' => 'Accounts',
                    'dependents' => [],
                ],
                'Accounts:assigned_user_link' => [
                    'name' => 'Accounts  >  Assigned to User',
                    'parent' => 'self',
                    'link_def' => [
                        'name' => 'assigned_user_link',
                        'relationship_name' => 'accounts_assigned_user',
                        'bean_is_lhs' => false,
                        'link_type' => 'one',
                        'label' => 'Assigned to User',
                        'module' => 'Users',
                        'table_key' => 'Accounts:assigned_user_link',
                    ],
                    'dependents' => [
                        0 => 'display_cols_row_1',
                    ],
                    'module' => 'Users',
                    'label' => 'Assigned to User',
                ],
            ],
            'filters_def' => [
                'Filter_1' => [
                    'operator' => 'AND',
                ],
            ],
            'chart_type' => 'none',
        ];

        return [
            [
                [
                    'name' => 'full_name',
                    'label' => 'Full Name',
                    'table_key' => 'Accounts:assigned_user_link',
                    'sort_dir' => 'd',
                    'table_alias' => 'l1',
                    'column_key' => 'Accounts:assigned_user_link:full_name',
                    'type' => 'fullname',
                ],
                $reportDef,
                'l1.last_name DESC, l1.first_name DESC',
            ],
            [
                [
                    'name' => 'name',
                    'label' => 'Name',
                    'table_key' => 'self',
                    'sort_dir' => 'a',
                    'table_alias' => 'accounts',
                    'column_key' => 'self:name',
                    'type' => 'name',
                ],
                $reportDef,
                'accounts_name ASC',
            ],
        ];
    }

    /**
     * @return SugarQuery
     */
    protected function getQueryObject()
    {
        $query = new SugarQuery();
        $query->select(['id']);
        $query->from($this->bean)
            ->whereRaw("id = '{$this->bean->id}'");
        return $query;
    }

    /**
     * @return SugarWidget
     */
    protected function getSugarWidget()
    {
        $lm = new LayoutManager();
        $reporter = new stdClass();
        $reporter->db = DBManagerFactory::getInstance();
        $reporter->report_def_str = '';
        $lm->setAttributePtr('reporter', $reporter);
        $widget = new SugarWidgetReportField($lm);
        return $widget;
    }
}
