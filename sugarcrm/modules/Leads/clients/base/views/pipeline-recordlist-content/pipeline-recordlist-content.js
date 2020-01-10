// FILE SUGARCRM flav=ent ONLY
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
/**
 * @class View.Views.Base.Leads.PipelineRecordlistContentView
 * @alias App.view.views.BaseLeadsPipelineRecordlistContentView
 * @extends View.Views.Base.PipelineRecordlistContentView
 */
({
    extendsFrom: 'PipelineRecordlistContentView',

    /**
     * Overrides the base function to account for the lead conversion functionality
     * @override
     */
    saveModel: function(model, pipelineData) {
        if (this.headerField === 'status') {
            // Get the name of the status representing 'Converted'
            var statusOptions = app.lang.getAppListStrings('lead_status_dom');
            var convertedStatus = statusOptions.Converted;

            // If the lead has already been converted, don't allow the user to change
            // its status. If the lead status is being changed to from non-converted
            // to converted, open the lead conversion layout in a drawer instead of
            // the normal change saving process
            if (model.get('converted')) {
                this._postChange(true, model, pipelineData);
                var moduleName = app.lang.getModuleName(this.module, {plural: false});
                app.alert.show('error_converted', {
                    level: 'error',
                    messages: app.lang.get('LBL_PIPELINE_ERR_CONVERTED', this.module, {moduleSingular: moduleName})
                });
                return;
            } else if (convertedStatus &&
                _.isObject(pipelineData.newCollection) &&
                pipelineData.newCollection.headerKey === convertedStatus) {
                app.drawer.open({
                    layout: 'convert',
                    context: {
                        forceNew: true,
                        skipFetch: true,
                        module: 'Leads',
                        leadsModel: model
                    }
                }, _.bind(function(success) {
                    this._postChange(!success, model, pipelineData);
                }, this));
                return;
            }
        }

        this._super('saveModel', [model, pipelineData]);
    }
});
