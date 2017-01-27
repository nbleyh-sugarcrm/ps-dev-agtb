describe('Quotes.Base.Views.Create', function() {
    var app;
    var view;
    var viewMeta;
    var model;
    var context;
    var quoteFields;
    var bundleFields;
    var productFields;
    var modMeta;

    beforeEach(function() {
        app = SugarTest.app;

        quoteFields = SugarTest.loadFixture('quote-fields', '../tests/modules/Quotes/fixtures');
        bundleFields = SugarTest.loadFixture('product-bundle-fields', '../tests/modules/ProductBundles/fixtures');
        productFields = SugarTest.loadFixture('product-fields', '../tests/modules/Products/fixtures');

        SugarTest.testMetadata.init();
        SugarTest.seedMetadata(true, './fixtures');
        SugarTest.testMetadata.updateModuleMetadata('ProductBundles', {
            fields: bundleFields
        });
        SugarTest.testMetadata.updateModuleMetadata('Products', {
            fields: productFields
        });
        SugarTest.testMetadata.updateModuleMetadata('Quotes', {
            fields: quoteFields
        });

        SugarTest.loadComponent('base', 'view', 'record');
        SugarTest.loadPlugin('VirtualCollection');
        SugarTest.testMetadata.set();
        SugarTest.app.data.declareModels();

        viewMeta = {
            panels: [
                {
                    fields: [
                        {
                            name: 'name'
                        }, {
                            name: 'total',
                            calculated: true
                        }, {
                            name: 'bundles',
                            fields: [
                                'id',
                                'name',
                                {
                                    name: 'product_bundle_items',
                                    fields: [
                                        'name',
                                        'id'
                                    ]
                                }
                            ]
                        }
                    ]
                }
            ]
        };

        modMeta = {
            fields: [
                {
                    name: 'name'
                }
            ]
        };

        sinon.collection.stub(app.data, 'getRelatedModule');
        sinon.collection.stub(app.metadata, 'getView', function() {
            return viewMeta;
        });
        sinon.collection.stub(app.metadata, 'getModule', function() {
            return modMeta;
        });
        app.data.getRelatedModule.withArgs('Quotes', 'product_bundles').returns('ProductBundles');
        app.data.getRelatedModule.withArgs('ProductBundles', 'products').returns('Products');
        app.data.getRelatedModule.withArgs('ProductBundles', 'product_bundle_notes').returns('ProductBundleNotes');

        context = app.context.getContext();
        model = app.data.createBean('Quotes');
        context.set('model', model);
        view = SugarTest.createView('base', 'Quotes', 'create', viewMeta, context, true);
    });

    afterEach(function() {
        sinon.collection.restore();
        view.dispose();
        view = null;
        context = null;
        model.dispose();
        model = null;
    });

    describe('initialize()', function() {
        var options;
        beforeEach(function() {
            options = {
                context: context
            };

            sinon.collection.stub(view, '_prepopulateQuote', function() {});
            sinon.collection.stub(view, '_buildMeta', function() {});
        });

        afterEach(function() {
            options = null;
        });

        it('should call _prepopulateQuote if convert is on the context', function() {
            options.context.set('convert', true);
            view.initialize(options);

            expect(view._prepopulateQuote).toHaveBeenCalledWith(options);

        });

        it('should not call _prepopulateQuote if convert is not on the context', function() {
            view.initialize(options);

            expect(view._prepopulateQuote).not.toHaveBeenCalled();
        });

        it('should call _buildMeta with ProductBundleNotes params', function() {
            view.initialize(options);

            expect(view._buildMeta).toHaveBeenCalledWith('ProductBundleNotes', 'quote-data-group-list');
        });

        it('should call _buildMeta with ProductBundles params', function() {
            view.initialize(options);

            expect(view._buildMeta).toHaveBeenCalledWith('ProductBundles', 'quote-data-group-header');
        });

        it('should call _buildMeta with Products params', function() {
            view.initialize(options);

            expect(view._buildMeta).toHaveBeenCalledWith('Products', 'quote-data-group-list');
        });
    });

    describe('_prepopulateQuote()', function() {
        var options;
        var quoteModel;
        var otherModuleModel;

        beforeEach(function() {
            quoteModel = new Backbone.Model();
            otherModuleModel = new Backbone.Model({
                account_id: 'acctId1',
                account_name: 'acctName1'
            });
            sinon.collection.stub(app.api, 'call', function() {});
            sinon.collection.stub(app.api, 'buildURL', function(params) {
                return params;
            });
            sinon.collection.stub(view, 'createLinkModel', function(model, linkName) {
                var attribs;
                if (linkName === 'quotes_shipto') {
                    attribs = {
                        shipping_account_id: 'acctId1',
                        shipping_account_name: 'acctName1'
                    };
                } else if (linkName === 'quotes') {
                    attribs = {
                        billing_account_id: 'acctId1',
                        billing_account_name: 'acctName1'
                    };
                } else {
                    attribs = {
                        opportunity_id: 'oppId1',
                        opportunity_name: 'oppName1'
                    };
                }

                return new Backbone.Model(attribs);
            });
        });

        afterEach(function() {
            options = null;
            otherModuleModel = null;
            quoteModel = null;
        });

        describe('from Opportunity', function() {
            beforeEach(function() {
                otherModuleModel.set({
                    id: 'oppId1',
                    name: 'oppName1'
                });
                otherModuleModel.module = 'Opportunities';
                context.set({
                    model: quoteModel,
                    parentModel: otherModuleModel,
                    fromLink: 'revenuelineitems'
                });
                options = {
                    context: context
                };

                view._prepopulateQuote(options);
            });

            it('should set isConvertFromShippingOrBilling to undefined', function() {
                expect(view.isConvertFromShippingOrBilling).toBeUndefined();
            });

            it('should map fields and prepopulate the Quote context', function() {
                expect(quoteModel.get('opportunity_id')).toBe('oppId1');
                expect(quoteModel.get('opportunity_name')).toBe('oppName1');
                expect(quoteModel.get('billing_account_id')).toBe('acctId1');
                expect(quoteModel.get('billing_account_name')).toBe('acctName1');
            });

            it('should call app.api.call to get the account', function() {
                expect(app.api.call).toHaveBeenCalledWith('read', 'Accounts/acctId1');
            });
        });

        describe('from Revenue Line Item', function() {
            beforeEach(function() {
                otherModuleModel.set({
                    id: 'rliId1',
                    name: 'rliName1',
                    opportunity_id: 'oppId1',
                    opportunity_name: 'oppName1'
                });
                otherModuleModel.module = 'RevenueLineItems';
                context.set({
                    model: quoteModel,
                    parentModel: otherModuleModel
                });
                options = {
                    context: context
                };

                view._prepopulateQuote(options);
            });

            it('should set isConvertFromShippingOrBilling to undefined', function() {
                expect(view.isConvertFromShippingOrBilling).toBeUndefined();
            });

            it('should map fields and prepopulate the Quote context', function() {
                expect(quoteModel.get('name')).toBe('rliName1');
                expect(quoteModel.get('opportunity_id')).toBe('oppId1');
                expect(quoteModel.get('opportunity_name')).toBe('oppName1');
                expect(quoteModel.get('billing_account_id')).toBe('acctId1');
                expect(quoteModel.get('billing_account_name')).toBe('acctName1');
            });

            it('should call app.api.call to get the account', function() {
                expect(app.api.call).toHaveBeenCalledWith('read', 'Accounts/acctId1');
            });
        });

        describe('from Accounts', function() {
            beforeEach(function() {
                otherModuleModel.unset('account_id');
                otherModuleModel.unset('account_name');
                otherModuleModel.set({
                    id: 'acctId1',
                    name: 'acctName1'
                });
                otherModuleModel.module = 'Accounts';
                context.set({
                    model: quoteModel,
                    parentModel: otherModuleModel
                });
                options = {
                    context: context
                };
            });

            describe('from shipping', function() {
                beforeEach(function() {
                    options.context.set('fromLink', 'quotes_shipto');

                    view._prepopulateQuote(options);
                });

                it('should set isConvertFromShippingOrBilling', function() {
                    expect(view.isConvertFromShippingOrBilling).toBe('shipping');
                });

                it('should map fields and prepopulate the Quote context', function() {
                    expect(quoteModel.get('billing_account_id')).toBeUndefined();
                    expect(quoteModel.get('billing_account_name')).toBeUndefined();
                    expect(quoteModel.get('shipping_account_id')).toBe('acctId1');
                    expect(quoteModel.get('shipping_account_name')).toBe('acctName1');
                });

                it('should call app.api.call to get the account', function() {
                    expect(app.api.call).toHaveBeenCalledWith('read', 'Accounts/acctId1');
                });
            });

            describe('from billing', function() {
                beforeEach(function() {
                    options.context.set('fromLink', 'quotes');

                    view._prepopulateQuote(options);
                });

                it('should set isConvertFromShippingOrBilling', function() {
                    expect(view.isConvertFromShippingOrBilling).toBe('billing');
                });

                it('should map fields and prepopulate the Quote context', function() {
                    expect(quoteModel.get('billing_account_id')).toBe('acctId1');
                    expect(quoteModel.get('billing_account_name')).toBe('acctName1');
                    expect(quoteModel.get('shipping_account_id')).toBeUndefined();
                    expect(quoteModel.get('shipping_account_name')).toBeUndefined();
                });
            });
        });
    });

    describe('_setAccountInfo()', function() {
        var accountInfoData;
        var viewModel;

        beforeEach(function() {
            accountInfoData = {
                billing_address_city: 'billingCity',
                billing_address_country: 'billingCountry',
                billing_address_postalcode: 'billingZip',
                billing_address_state: 'billingState',
                billing_address_street: 'billingStreet',
                shipping_address_city: 'shippingCity',
                shipping_address_country: 'shippingCountry',
                shipping_address_postalcode: 'shippingZip',
                shipping_address_state: 'shippingState',
                shipping_address_street: 'shippingStreet'
            };

            viewModel = app.data.createBean('Quotes');
            view.model = viewModel;
        });

        afterEach(function() {
            viewModel.dispose();
            viewModel = null;
            accountInfoData = null;
        });

        it('should only set shipping fields when isConvertFromShippingOrBilling is `shipping`', function() {
            view.isConvertFromShippingOrBilling = 'shipping';
            view._setAccountInfo(accountInfoData);

            expect(viewModel.get('billing_address_city')).toBeUndefined();
            expect(viewModel.get('billing_address_country')).toBeUndefined();
            expect(viewModel.get('billing_address_postalcode')).toBeUndefined();
            expect(viewModel.get('billing_address_state')).toBeUndefined();
            expect(viewModel.get('billing_address_street')).toBeUndefined();
            expect(viewModel.get('shipping_address_city')).toBe('shippingCity');
            expect(viewModel.get('shipping_address_country')).toBe('shippingCountry');
            expect(viewModel.get('shipping_address_postalcode')).toBe('shippingZip');
            expect(viewModel.get('shipping_address_state')).toBe('shippingState');
            expect(viewModel.get('shipping_address_street')).toBe('shippingStreet');
        });

        it('should only set billing fields when isConvertFromShippingOrBilling is `billing`', function() {
            view.isConvertFromShippingOrBilling = 'billing';
            view._setAccountInfo(accountInfoData);

            expect(viewModel.get('billing_address_city')).toBe('billingCity');
            expect(viewModel.get('billing_address_country')).toBe('billingCountry');
            expect(viewModel.get('billing_address_postalcode')).toBe('billingZip');
            expect(viewModel.get('billing_address_state')).toBe('billingState');
            expect(viewModel.get('billing_address_street')).toBe('billingStreet');
            expect(viewModel.get('shipping_address_city')).toBeUndefined();
            expect(viewModel.get('shipping_address_country')).toBeUndefined();
            expect(viewModel.get('shipping_address_postalcode')).toBeUndefined();
            expect(viewModel.get('shipping_address_state')).toBeUndefined();
            expect(viewModel.get('shipping_address_street')).toBeUndefined();
        });

        it('should only set shipping fields when isConvertFromShippingOrBilling is not set', function() {
            view.isConvertFromShippingOrBilling = undefined;
            view._setAccountInfo(accountInfoData);

            expect(viewModel.get('billing_address_city')).toBe('billingCity');
            expect(viewModel.get('billing_address_country')).toBe('billingCountry');
            expect(viewModel.get('billing_address_postalcode')).toBe('billingZip');
            expect(viewModel.get('billing_address_state')).toBe('billingState');
            expect(viewModel.get('billing_address_street')).toBe('billingStreet');
            expect(viewModel.get('shipping_address_city')).toBe('shippingCity');
            expect(viewModel.get('shipping_address_country')).toBe('shippingCountry');
            expect(viewModel.get('shipping_address_postalcode')).toBe('shippingZip');
            expect(viewModel.get('shipping_address_state')).toBe('shippingState');
            expect(viewModel.get('shipping_address_street')).toBe('shippingStreet');
        });
    });

    describe('hasUnsavedChanges()', function() {
        beforeEach(function() {
            view.hasUnsavedQuoteChanges = function() {};
            sinon.collection.stub(view, 'hasUnsavedQuoteChanges', function() {});
        });

        it('should call hasUnsavedQuoteChanges', function() {
            view.hasUnsavedChanges();

            expect(view.hasUnsavedQuoteChanges).toHaveBeenCalled();
        });
    });

    describe('validateBundleModels', function() {
        var bundleModel;
        var callback;
        var prodModel;

        beforeEach(function() {
            callback = sinon.collection.spy();
        });

        afterEach(function() {
            callback = null;
        });

        describe('with no bundles', function() {
            it('should use callback with same empty fields, errors params passed in', function() {
                view.validateBundleModels({}, {}, callback);

                expect(callback).toHaveBeenCalledWith(null, {}, {});
            });
        });

        describe('with one empty bundle', function() {
            it('should use callback with same empty fields, errors params passed in', function() {
                bundleModel = new Backbone.Model({
                    product_bundle_items: []
                });
                view.model.set('bundles', bundleModel);
                view.validateBundleModels({}, {}, callback);

                expect(callback).toHaveBeenCalledWith(null, {}, {});
            });
        });

        describe('with items in a bundle', function() {
            it('should use call isValidAsync on bundle model', function() {
                prodModel = app.data.createBean('Products', {
                    id: 'prodId1'
                });
                bundleModel = new Backbone.Model({
                    product_bundle_items: [prodModel]
                });
                bundleModel.isValidAsync = sinon.collection.stub();
                view.model.set('bundles', bundleModel);
                view.validateBundleModels({}, {}, callback);

                expect(bundleModel.isValidAsync).toHaveBeenCalled();
            });
        });
    });
});
