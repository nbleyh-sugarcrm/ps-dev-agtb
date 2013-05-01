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

({
    plugins: ['Dashlet'],
    className: 'opportunity-metrics-widget',

    metricsCollection: {},
    chartCollection: {},
    chart: {},

    _renderHtml: function () {
        var self = this,
            chart;

        app.view.View.prototype._renderHtml.call(this);

        if (this.viewName === "config" || _.isEmpty(this.chartCollection)) {
            return;
        }

        chart = nv.models.pieChart()
            .x(function (d) {
                return d.key;
            })
            .y(function (d) {
                return d.value;
            })
            .margin({top: 5, right: 20, bottom: 20, left: 20})
            .donut(true)
            .donutLabelsOutside(true)
            .donutRatio(0.4)
            .hole(this.chartCollection.properties.label)
            .showTitle(false)
            .tooltips(false)
            .showLegend(false)
            .colorData('class')
            .colorFill('default');

        d3.select('svg#' + this.cid)
            .datum(this.chartCollection)
            .transition().duration(500)
            .call(chart);

        self.chart = chart;
        nv.utils.windowResize(self.chart.update);
    },

    /* Process data loaded from REST endpoint so that d3 chart can consume
     * and set general chart properties
     */
    evaluateResult: function (data) {
        var self = this,
            total = 0;

        _.each(data, function (value, key) {
            // parse currencies and attach the correct delimiters/symbols etc
            data[key].formattedAmount = app.currency.formatAmountLocale(value.amount_usdollar, null, 0).replace(/\.[0-9]*/, '');

            data[key].icon = key === 'won' ? 'caret-up' : (key === 'lost' ? 'caret-down' : 'minus');
            data[key].cssClass = key === 'won' ? 'won' : (key === 'lost' ? 'lost' : 'active');
            data[key].dealLabel = key;
            data[key].stageLabel = app.lang.getAppListStrings("opportunity_metrics_dom")[key];

            total += value.count;
        });

        self.metricsCollection = data;

        self.chartCollection = {
            data: _.map(self.metricsCollection, function (value, key) {
                return {
                    'key': value.stageLabel,
                    'value': value.count,
                    'class': key
                };
            }),
            properties: {
                title: app.lang.getAppString('LBL_DASHLET_OPPORTUNITY_NAME'),
                value: 3,
                label: total
            }
        };
        this.total = total;
    },

    loadData: function (options) {
        var self = this,
            url = app.api.buildURL(this.model.parentModel.module, 'opportunity_stats', {
                id: this.model.parentModel.get('id')
            });

        app.api.call('read', url, null, {
            success: function (data) {
                self.evaluateResult(data);
                if (!self.disposed) {
                    self.render();
                }
            },
            complete: (options) ? options.complete : null
        });
    },

    _dispose: function () {
        if (!_.isEmpty(this.chart)) {
            nv.utils.windowUnResize(this.chart.render);
        }
        app.view.View.prototype._dispose.call(this);
    }
})
