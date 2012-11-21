describe('currency field', function() {

    var app;
    var model;

    var moduleName;
    var metadata;

    beforeEach(function() {
        moduleName = 'Opportunities';
        metadata = {
            fields: {
                "amount": {
                    "name": "amount",
                    "vname": "LBL_AMOUNT",
                    "type": "currency",
                    "dbType": "currency",
                    "comment": "Unconverted amount of the opportunity",
                    "importable": "required",
                    "duplicate_merge": "1",
                    "required": true,
                    "options": "numeric_range_search_dom",
                    "enable_range_search": true,
                    "validation": {
                        "type": "range",
                        "min": 0
                    }
                },
                "currency_id": {
                    "name": "currency_id",
                    "type": "id",
                    "group": "currency_id",
                    "vname": "LBL_CURRENCY",
                    "function": {
                        "name": "getCurrencyDropDown",
                        "returns": "html"
                    },
                    "reportable": false,
                    "comment": "Currency used for display purposes"
                },
                "base_rate": {
                    "name": "base_rate",
                    "vname": "LBL_CURRENCY_RATE",
                    "type": "double",
                    "required": true
                }
            },
            views: [],
            layouts: [],
            _hash: "d7e699e7cf748d05ac311b0165e7591a"
        };

        app = SugarTest.app;
        SugarTest.seedMetadata(true);

        app.data.declareModel(moduleName, metadata);

        model = app.data.createBean(moduleName, {
            amount: 123456789.12,
            currency_id: '-99',
            base_rate: 1
        });
    });

    afterEach(function() {
        app.cache.cutAll();
        app.view.reset();
        delete Handlebars.templates;
        model = null;

        moduleName = null;
        metadata = null;
    });

    describe('edit view', function() {

        var field;

        beforeEach(function() {
            field = SugarTest.createField('base', 'amount', 'currency', 'edit', {
                related_fields: ['currency_id', 'base_rate'],
                currency_field: 'currency_id',
                base_rate_field: 'base_rate'
            });
            field.model = model;
            field._loadTemplate();
        });

        afterEach(function() {
            field = null;
        });

        it('should make use of app.utils to format the value', function() {

            var formatNumberLocale = sinon.spy(app.utils, 'formatNumberLocale');

            field.format(123456789.98);
            expect(formatNumberLocale.calledOnce).toBeTruthy();

            formatNumberLocale.restore();
        });

        it('should make use of app.utils to unformat the value', function() {

            var unformatNumberStringLocale = sinon.spy(app.utils, 'unformatNumberStringLocale');

            field.unformat('123456789.98');
            expect(unformatNumberStringLocale.calledOnce).toBeTruthy();

            unformatNumberStringLocale.restore();
        });

        it("should render with currencies selector", function() {

            var currencyRender;
            var getCurrencyField = sinon.stub(field, 'getCurrencyField', function() {
                var currencyField = SugarTest.createField('base', 'amount', 'enum', 'edit', {
                    options: {'-99': '$ USD' }
                });
                currencyField.model = model;
                currencyRender = sinon.stub(currencyField, 'render', function() {
                    return null;
                });

                return currencyField;
            });

            field.render();
            expect(currencyRender).toHaveBeenCalled();

            currencyRender.restore();
            getCurrencyField.restore();
        });
    });
});
