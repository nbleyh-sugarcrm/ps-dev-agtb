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
describe('Products.Base.Field.Relate', function() {
    var field;
    var fieldDef;
    var fieldModel;
    var app;

    beforeEach(function() {
        app = SugarTest.app;
    });

    afterEach(function() {
        app.cache.cutAll();
        app.view.reset();
    });

    // BEGIN SUGARCRM flav=ent ONLY
    describe('getFilterOptions', function() {
        beforeEach(function() {
            app.drawer = app.drawer || {};
            app.drawer.open = app.drawer.open || $.noop;
            fieldDef = {
                'name': 'add_on_to_name',
                'rname': 'name',
                'id_name': 'add_on_to_id',
                'vname': 'LBL_ADD_ON_TO',
                'type': 'relate',
                'save': true,
                'link': 'pli_addons_link',
                'isnull': 'true',
                'table': 'purchased_line_items',
                'module': 'PurchasedLineItems',
                'source': 'non-db',
            },
            fieldModel = app.data.createBean('Products');
            field = SugarTest.createField('base', 'add_on_to_name', 'relate',
                'edit', fieldDef, 'Products', fieldModel, null, true);
            openStub = sinon.collection.stub(app.drawer, 'open');

            field.model.fields = {
                account_id: {
                    name: 'account_id'
                },
                account_name: {
                    name: 'account_name',
                    id_name: 'account_id'
                }
            };

            field.model.set('account_id', '1234-5678');
            field.model.set('account_name', 'The related Account');
        });

        afterEach(function() {
            field.dispose();
        });

        it('should return the proper add_on_to_name filter options', function() {
            var filterOptions = field.getFilterOptions();
            expect(filterOptions).toBeDefined();
            expect(filterOptions.initial_filter).toEqual('add_on_plis');
            expect(filterOptions.initial_filter_label).toEqual('LBL_PLI_ADDONS');
            expect(filterOptions.filter_populate).toEqual(
                {
                    account_id: ['1234-5678']
                }
            );
        });
    });
    // END SUGARCRM flav=ent ONLY
});
