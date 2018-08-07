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
'use strict';

import {givenStepsHelper, whenStepsHelper, stepsHelper, Given} from '@sugarcrm/seedbed';
import {TableDefinition} from 'cucumber';
import ModuleMenuCmp from '../components/module-menu-cmp';
import ListView from '../views/list-view';
import RecordView from '../views/record-view';
import {Utils, When, Then, seedbed} from '@sugarcrm/seedbed';
import BaseView from '../views/base-view';
import * as _ from 'lodash';
import AlertCmp from '../components/alert-cmp';

Given(/^I am logged in$/,
    async function () {
        await useDefaultAcct('default', 'admin', 'asdf');
        await launchApp('launch', '');
        await whenStepsHelper.setUrlHashAndLogin('about');
    }, {waitForApp: true}
);

const useDefaultAcct = async function (isDefaultAccount: string, username: string, password: string): Promise<void> {
    await givenStepsHelper.useAccount(isDefaultAccount, username, password);
};

const launchApp = async function (launch: string, schemesList: string): Promise<void> {
    await givenStepsHelper.launchOrUpdate(launch, schemesList);
};

When(/^I update (\w+) \*(\w+) with the following values:$/,
    async function (module, name: string, table: TableDefinition) {
        // TODO: In the future we should check the current route and if we are already on the correct module/record
        await chooseModule(module);
        let view = await seedbed.components[`${module}List`].ListView;
        let record = await seedbed.cachedRecords.get(name);
        await chooseRecord({id: record.id}, view);
        let rec_view = await seedbed.components[`${name}Record`];
        await buttonClicks('Edit', rec_view);
        await buttonClicks('show more', rec_view);
        await provideInput(rec_view.RecordView, table);
        await buttonClicks('Save', rec_view);
        await closeAlert();
    }, {waitForApp: true}
);

// TODO check if we're already on desired rec. view instead of navigating to the list view.
Then(/^(\w+) \*(\w+) should have the following values:$/,
    async function (module, name: string, table: TableDefinition) {
        let record = await seedbed.cachedRecords.get(name);
        let rec_view = await seedbed.components[`${name}Record`];

        await goToUrl(module + '/' + record.id);
        await buttonClicks('show more', rec_view);
        if (module === 'Quotes') {
            let rec = await seedbed.components[`${name}Record`].RecordView;
            await rec.expandQuotePanel('Billing_and_Shipping');
            await rec.expandQuotePanel('Quote_Settings');
            await rec.expandQuotePanel('Show_More');
        }

        await checkValues(rec_view, table);
    }, {waitForApp: true}
);

export const chooseModule = async function (itemName) {
    await seedbed.client.driver.waitForApp();

    let moduleMenuCmp = new ModuleMenuCmp({});
    let isVisible = await moduleMenuCmp.isVisible(itemName);

    if (isVisible) {
        await moduleMenuCmp.clickItem(itemName);
        await seedbed.client.driver.waitForApp();
    } else {
        await moduleMenuCmp.showAllModules();
        isVisible = await moduleMenuCmp.isVisible(itemName);
        if (isVisible) {
            await moduleMenuCmp.clickItem(itemName, true);
            await seedbed.client.driver.waitForApp();
        } else {
            await goToUrl(itemName);
        }
    }
    // TODO: it's a temporary solution, need to remove this 'pause' after SBD-349 is fixed
    await seedbed.client.driver.pause(1000);
};

export const chooseRecord = async function (record: { id: string }, view: ListView) {
    let listItem = view.getListItem({id: record.id});
    await listItem.clickListItem();
    await seedbed.client.driver.waitForApp();
};

export const toggleRecord = async function (record: { id: string }, view: ListView) {
    let listItem = view.getListItem({id: record.id});
    await listItem.clickItem('checkbox');
    await seedbed.client.driver.waitForApp();
};

export const buttonClicks = async function (btnName: string, layout: any) {
    if (btnName.toLowerCase() === 'show more') {
        return layout.showMore(btnName);
    }
    await layout.HeaderView.clickButton(btnName.toLowerCase());
    return seedbed.client.driver.waitForApp();
};

const provideInput = async function (view: RecordView, data: TableDefinition): Promise<void> {
    if (data.hashes.length > 1) {
        throw new Error('One line data table entry is expected');
    }

    let inputData = stepsHelper.getArrayOfHashmaps(data)[0];
    // check for * marked column and cache the record and view if needed
    let uidInfo = Utils.computeRecordUID(inputData);

    seedbed.cucumber.scenario.recordsInfo[uidInfo.uid] = {
        uid: uidInfo.uid,
        originInput: JSON.parse(JSON.stringify(inputData)),
        input: inputData,
        module: view.module,
    };

    await view.setFieldsValue(inputData);
};

const checkValues = async function (view: BaseView, data: TableDefinition) {
    const attrRefRegex = RegExp(/\{\*([a-zA-Z](?:\w|\S)*)\.((?:\w|\s)*)}/g);

    /**
     * Replaces references to dynamic values with their value from the
     * cached API response for the specified record.
     *
     * @example "{*Case_1.case_number}" is replaced with "237".
     * | fieldName | value                                |
     * | name      | [CASE:{*Case_1.case_number}] My Case |
     *
     * @param {string} value
     * @return {{}}
     */
    function getReplacementsForAttributeReferences(value: string) {
        let replacements = {};
        let match;
        let recordIdOfReference;
        let apiResponseForRecord;

        // Find the substitutions for every match captured.
        while ((match = attrRefRegex.exec(value)) !== null) {
            recordIdOfReference = seedbed.cachedRecords.get(match[1]).id;
            apiResponseForRecord = seedbed.api.created.find((rec) => {
                return recordIdOfReference === rec.id;
            });

            if (apiResponseForRecord) {
                replacements[match[0]] = apiResponseForRecord[match[2]];
            }
        }
        return replacements;
    }

    // Substitute the references in the values for all fields where one or
    // more references are found.
    const fieldsData: any = _.map(data.hashes() || [], (field) => {
        const replacements = getReplacementsForAttributeReferences(field.value);

        _.each(replacements, (value: string, key: string) => {
            field.value = field.value.replace(RegExp(_.escapeRegExp(key), 'g'), value);
        });

        return field;
    });

    let errors = await view.checkFields(fieldsData);
    let message = '';
    _.each(errors, (item) => {
        message += item;
    });

    if (message) {
        throw new Error(message);
    }
};

export const closeAlert = async function () {
    let alert = new AlertCmp({});
    await alert.close();
};

export const closeWarning  = async function(actionName) {
    let alert = new AlertCmp({});
    await alert.clickButton(actionName);
};

export const goToUrl = async function (urlHash): Promise<void> {
    await seedbed.client.driver.setUrlHash(urlHash);
    // TODO: it's a temporary solution, need to remove this 'pause' after SBD-349 is fixed
    await seedbed.client.driver.pause(1500);
};

export const parseInputArray = async function (arg: string): Promise<any[]> {
    arg = arg.slice(arg.indexOf('[') + 1, arg.indexOf(']'));

    if (arg.startsWith('*')) {
        if (arg.indexOf(',') === -1) {
            let record = seedbed.cachedRecords.get(arg.replace('*', ''));

            if (!record) {
                throw new Error(`Record '${arg}' doesn't exist`);
            }
            return record;
        } else {
            let records = [];
            let sRecord = arg.split(',');
            for (let i = 0; i < sRecord.length; i++) {

                let record = seedbed.cachedRecords.get(sRecord[i].trim().replace('*', ''));

                if (!record) {
                    throw new Error(`Record '${arg}' doesn't exist`);
                }
                records.push(record);
            }
            return records;
        }
    }
};
