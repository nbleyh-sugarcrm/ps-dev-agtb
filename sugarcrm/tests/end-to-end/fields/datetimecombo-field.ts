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
import DateField from "./date-field";

/**
 * @class DatetimecomboField
 * @extends DateField
 */
export default class DatetimecomboField extends DateField {

    constructor(options) {
        super(options);

        this.selectors = this.mergeSelectors({
            $: '[field-name={{name}}]',
            field: {
                selector: 'input[data-type="date"]',
                date: 'input[data-type="date"]',
                time: 'input[data-type="time"]',
            }
        });
    }

    public async setValue(val: any): Promise<void> {

        let date;
        let time;

        if (val) {
            let datetime = val.trim().split('-');
            date = datetime[0];
            time = datetime[1];
        }
        if (date) {
            await this.driver.setValue(this.$('field.date').trim(), date);
        }
        if (time) {
            await this.driver.click(this.$('field.time'));
            await this.driver.scroll('li=' + time.trim());
            await this.driver.click('li=' + time.trim());
        }
        await this.driver.click('body');
    }

    protected getDateTimePref(): string {
        let datePref = this.getAdminPreference('datepref');
        let timePref = this.getAdminPreference('timepref');
        return datePref + ' ' + timePref;
    }
}

export class Detail extends DatetimecomboField {

    protected itemSelector: String;

    constructor(options) {
        super(options);

        this.selectors = this.mergeSelectors({
            field: {
                selector: 'div'
            }
        });
    }


    public async getText(selector: string): Promise<string> {
        let value: string | string[] = await this.driver.getText(selector);
        return value.toString().trim();
    }
}

export const Preview = Detail;
