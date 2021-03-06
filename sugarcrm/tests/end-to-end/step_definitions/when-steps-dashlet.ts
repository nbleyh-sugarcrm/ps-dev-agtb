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

import {stepsHelper, When, seedbed} from '@sugarcrm/seedbed';
import DashletView from '../views/dashlet-view';
import PlannedActivitiesListView from '../views/planned-activities-list-view';
import PlannedActivitiesDashlet from '../views/planned-activities-dashlet-view';
import ActiveTasksDashlet from '../views/active-tasks-dashlet-view';
import InactiveTasksDashlet from '../views/inactive-tasks-dashlet-view';
import {TableDefinition} from 'cucumber';
import PipelineDashlet from '../views/pipeline-dashlet-view';
import Top10RlisDashlet from '../views/top-10-rlis-dashlet';
import HistoryDashlet from '../views/history-dashlet-view';
import ActiveSubscriptionsListView from '../views/active-subscriptions-list-view';

/**
 * Click dashlet's cog button
 *
 * @example I click Cog in #AccountsDashboard.DashletView
 */
When(/^I click (Cog) in (#\S+)$/,
    async function (btn: string, view: DashletView): Promise<void> {
        await view.clickCog();
    }, {waitForApp: true});


/**
 *  Navigate to specific tab in Planned Activities dashlet
 *
 *      @example
 *      When I navigate to Calls tab in #ServiceConsoleView.PlannedActivitiesDashlet
 */
When(/^I navigate to (Calls|Meetings|Due Now|Upcoming|To Do|Emails|Deferred|Completed) tab in (#\S+)$/,
    async function(tabName: string, view: DashletView) {

        // check tab name as well as dashlet type
        if ((tabName === 'Meetings' && view instanceof PlannedActivitiesDashlet) ||
            (tabName === 'Due Now' && view instanceof ActiveTasksDashlet) ||
            (tabName === 'Deferred' && view instanceof InactiveTasksDashlet) ||
            (tabName === 'Meetings' && view instanceof HistoryDashlet)) {
            await view.navigateToTab('0');
        } else if ((tabName === 'Calls' && view instanceof PlannedActivitiesDashlet) ||
            (tabName === 'Upcoming' && view instanceof ActiveTasksDashlet) ||
            (tabName === 'Completed' && view instanceof InactiveTasksDashlet) ||
            (tabName === 'Emails' && view instanceof HistoryDashlet)) {
            await view.navigateToTab('1');
        } else if ((tabName === 'To Do' && view instanceof ActiveTasksDashlet) ||
            (tabName === 'Calls' && view instanceof HistoryDashlet)) {
            await view.navigateToTab('2');
        } else {
            throw new Error('Invalid module specified!');
        }
    }, {waitForApp: true});

/**
 *  When I set time filter (today vs future) in the dashlet
 *
 *      @example
 *      When I set filter as Today in #ServiceConsoleView.PlannedActivitiesDashlet
 */
When(/^I set filter as (Today|Future) in (#\S+)$/,
    async function(filterName: string, view: DashletView) {
        await view.setFilter(filterName.toLowerCase());
    }, {waitForApp: true});

/**
 *  When I set visibility (user vs group) in the dashlet
 *
 *      @example
 *      When I set visibility as 'group' in #ServiceConsoleView.PlannedActivitiesDashlet
 */
When(/^I set visibility as '(user|group)' in (#\S+)$/,
    async function(visibility: string, view: DashletView) {
        await view.setVisibility(visibility);
    }, {waitForApp: true});

/**
 *  Mark meeting or Call record as held, accepted, tentative or declined
 *
 *      @example
 *      When I mark record *M_1 as Tentative in #ServiceConsoleView.PlannedActivitiesDashlet.ActivitiesList
 */
When(/^I mark record (\*[a-zA-Z](?:\w|\S)*) as (Held|Accepted|Tentative|Declined|Completed) in (#\S+)$/,
    async function(record: { id: string }, action: string, view: PlannedActivitiesListView) {
        let listItem = view.getListItem({id: record.id});
        await listItem.selectAction(action.toLowerCase());
    }, {waitForApp: true});

/**
 * Click "more tasks" in dashlet to display more records
 *
 *      @example
 *      When I display more records in #Dashboard.InactiveTasksDashlet view
 */
When(/^I display more records in (#\S+) view$/, async function (view: DashletView) {
    await view.clickMoreRecordsBtn();
}, {waitForApp: true});

/**
 * Click configure and select edit to update dashlet setting
 *
 *      @example
 *      When I edit dashlet settings of #Dashboard.InactiveTasksDashlet with the following values:
 *            | label                 | limit |
 *            | Inactive Tasks Update | 5     |
 */
When(/^I edit dashlet settings of (#\S+) with the following values:$/,
    async function (view: DashletView, data?: TableDefinition): Promise<void> {

        if (data.hashes.length > 1) {
            throw new Error('One line data table entry is expected');
        }
        let inputData = stepsHelper.getArrayOfHashmaps(data)[0];

        await view.performAction('edit');
        await this.driver.waitForApp();

        // Update dashlet settings with new values
        await seedbed.components.AddSugarDashletDrawer.setFieldsValue(inputData);
        await this.driver.waitForApp();

        // Save a new dashlet
        await seedbed.components.AddSugarDashletDrawer.HeaderView.clickButton('save');
        await this.driver.waitForApp();

    }, {waitForApp: true});

/**
 *  Select specified item from Time Period dropdown control
 *
 *  @example
 *  When I select "2021 Q3" in #Dashboard.RcPipelineDashlet
 */
When(/^I select "([a-zA-Z0-9 ]+)" in (#\S+)$/,
    async function (itemToSelect: string, view: DashletView): Promise<void> {

        let filterName: string;

        if (view instanceof HistoryDashlet) {
            filterName = 'filter';
        } else if (view instanceof PipelineDashlet) {
            filterName = 'selectedTimePeriod';
        } else if (view instanceof Top10RlisDashlet) {
            filterName = 'filter_duration';
        } else {
            throw new Error('Error. This method is not applicable for specified dashlet type.');
        }
        await view.selectFromDropdown(filterName, itemToSelect);
        await this.driver.waitForApp();

    }, {waitForApp: true});


/**
 *  Click on the Purchase link in the Active Subscriptions dashlet
 *
 *  @example
 *  When I click on *Pur_1 record in #RenewalsConsoleView.ActiveSubscriptionsDashlet.ListView
 *
 */
When(/^I click on (\*[a-zA-Z](?:\w|\S)*) record in (#\S+)$/,
    async function(record: { id: string }, view: ActiveSubscriptionsListView) {
        let listItem = view.getListItem({id: record.id});
        await listItem.selectRecord();
    }, {waitForApp: true});
