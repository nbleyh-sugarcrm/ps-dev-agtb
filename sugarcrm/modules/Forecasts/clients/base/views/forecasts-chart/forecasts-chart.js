/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement (“MSA”), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright  2004-2013 SugarCRM Inc.  All rights reserved.
 */
/**
 * Dashlet that displays a chart
 */
({
    plugins: ['Dashlet'],

    values: new Backbone.Model(),

    className: 'forecasts-chart-wrapper',

    /**
     * Hold the initOptions if we have to call the Forecast/init end point cause we are not on Forecasts
     */
    initOptions: null,

    /**
     * The context of the ForecastManagerWorksheet Module if one exists
     */
    forecastManagerWorksheetContext: undefined,

    /**
     * The context of the ForecastWorksheet Module if one exists
     */
    forecastWorksheetContext: undefined,

    /**
     * {@inheritdoc}
     */
    initialize: function(options) {
        this.values.clear({silent: true});
        // after we init, find and bind to the Worksheets Contexts
        this.on('init', this.findWorksheetContexts, this);
        app.view.View.prototype.initialize.call(this, options);
        if (!this.meta.config) {
            var ctx = this.context.parent,
                user = ctx.get('selectedUser') || app.user.toJSON();
            this.values.set({
                user_id: user.id,
                display_manager: user.is_manager,
                ranges: ctx.get('selectedRanges') || ['include'],
                timeperiod_id: ctx.get('selectedTimePeriod'),
                dataset: 'likely',
                group_by: 'forecast'
            });
        }
    },

    /**
     * Specific code to run after a dashlet Init Code has ran
     *
     */
    initDashlet: function() {
        var fieldOptions,
            cfg = app.metadata.getModule('Forecasts', 'config');
        fieldOptions = app.lang.getAppListStrings(this.dashletConfig.dataset.options);
        this.dashletConfig.dataset.options = {};

        if (cfg.show_worksheet_worst) {
            this.dashletConfig.dataset.options['worst'] = fieldOptions['worst'];
        }

        if (cfg.show_worksheet_likely) {
            this.dashletConfig.dataset.options['likely'] = fieldOptions['likely'];
        }

        if (cfg.show_worksheet_best) {
            this.dashletConfig.dataset.options['best'] = fieldOptions['best'];
        }
    },

    /**
     * Loop though the parent context children context to find the worksheet, if they exist
     */
    findWorksheetContexts: function() {
        // loop though the children context looking for the ForecastWorksheet and ForecastManagerWorksheet Modules
        _.filter(this.context.parent.children, function(item) {
            if (item.get('module') == 'ForecastWorksheets') {
                this.forecastWorksheetContext = item;
                return true;
            } else if (item.get('module') == 'ForecastManagerWorksheets') {
                this.forecastManagerWorksheetContext = item;
                return true;
            }
            return false;
        }, this);

        if (this.forecastWorksheetContext) {
            // listen for collection change events
            var collection = this.forecastWorksheetContext.get('collection');
            if (collection) {
                collection.on('change', this.repWorksheetChanged, this);
            }
        }

        if (this.forecastManagerWorksheetContext) {
            // listen for collection change events
            var collection = this.forecastManagerWorksheetContext.get('collection');
            if (collection) {
                collection.on('change', this.mgrWorksheetChanged, this);
            }
        }
    },

    /**
     * Handler for when the Rep Worksheet Changes
     * @param {Object} model
     */
    repWorksheetChanged: function(model) {
        // get what we are currently filtered by
        // find the item in the serverData
        var changed = model.changed,
            changedField = _.keys(changed),
            field = this.getField('paretoChart'),
            serverData = field.getServerData();

        if (_.contains(changedField, 'likely_case')) {
            changed.likely = app.currency.convertWithRate(changed.likely_case, model.get('base_rate'));
            delete changed.likely_case;
        }
        if (_.contains(changedField, 'best_case')) {
            changed.best = app.currency.convertWithRate(changed.best_case, model.get('base_rate'));
            delete changed.best_case;
        }
        if (_.contains(changedField, 'worst_case')) {
            changed.worst = app.currency.convertWithRate(changed.worst_case, model.get('base_rate'));
            delete changed.worst_case;
        }

        if (_.contains(changedField, 'commit_stage')) {
            changed.forecast = changed.commit_stage
            delete changed.commit_stage;
        }

        _.find(serverData.data, function(record, i, list) {
            if (model.get('id') == record.id) {
                list[i] = _.extend({}, record, changed);
                return true;
            }
            return false;
        });

        field.setServerData(serverData, _.contains(changedField, 'probability'));
    },


    /**
     * Handler for when the Manager Worksheet Changes
     * @param {Object} model
     */
    mgrWorksheetChanged: function(model) {
        var fieldsChanged = _.keys(model.changed),
            changed = model.changed,
            field = this.getField('paretoChart'),
            serverData = field.getServerData();


        if (_.contains(fieldsChanged, 'quota')) {
            var q = parseInt(serverData.quota, 10);
            q = app.math.add(app.math.sub(q, model.previous('quota')), model.get('quota'));
            serverData.quota = q;
        } else {
            var f = _.first(fieldsChanged),
                fieldChartName = f.replace('_case', '');

            // find the user
            _.find(serverData.data, function(record, i, list) {
                if (model.get('user_id') == record.user_id) {
                    list[i][fieldChartName] = changed[f];
                    return true;
                }
                return false;
            });
        }

        field.setServerData(serverData);
    },

    /**
     * We never want to have the loadData method do anything
     *
     * {@override}
     */
    loadData: function(options) {
        return;
    },

    /**
     * Called after _render
     */
    toggleRepOptionsVisibility: function() {
        if (this.values.get('display_manager') === true) {
            this.$el.find('div.groupByOptions').hide();
        } else {
            this.$el.find('div.groupByOptions').show();
        }
    },

    /**
     * {@inheritdoc}
     */
    bindDataChange: function() {
        // on the off chance that the init has not run yet.
        var meta = this.meta || this.initOptions.meta;
        if (meta.config) {
            return;
        }

        this.on('render', function() {
            this.toggleRepOptionsVisibility();
        }, this);

        var ctx = this.context.parent;

        ctx.on('change:selectedUser', function(context, user) {
            this.values.set({
                user_id: user.id,
                display_manager: (user.showOpps === false && user.isManager === true)
            });
            this.toggleRepOptionsVisibility();
        }, this);
        ctx.on('change:selectedTimePeriod', function(context, timePeriod) {
            this.values.set({timeperiod_id: timePeriod});
        }, this);
        ctx.on('change:selectedRanges', function(context, value) {
            this.values.set({ranges: value});
        }, this);
    },

    /**
     * {@inheritdoc}
     * Clean up!
     */
    unbindData: function() {
        var ctx = this.context.parent;
        if (ctx) {
            ctx.off(null, null, this);
        }
        if (this.forecastManagerWorksheetContext && this.forecastManagerWorksheetContext.get('collection')) {
            this.forecastManagerWorksheetContext.get('collection').off(null, null, this);
        }
        if (this.forecastWorksheetContext && this.forecastWorksheetContext.get('collection')) {
            this.forecastWorksheetContext.get('collection').off(null, null, this);
        }
        if (this.context) this.context.off(null, null, this);
        if (this.values) this.values.off(null, null, this);
        app.view.View.prototype.unbindData.call(this);
    }
})
