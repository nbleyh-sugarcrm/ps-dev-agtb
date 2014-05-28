/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement ("MSA"), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright (C) 2004-2014 SugarCRM Inc. All rights reserved.
 */
/**
 * @class View.Fields.Base.Audit.FieldtypeField
 * @alias SUGAR.App.view.fields.BaseAuditFieldtypeField
 * @extends View.Field
 */
({
    /**
     * {@inheritDoc}
     * Convert the raw field type name
     * into the label of the field of the parent model.
     */
    format: function(value) {
        if (this.context && this.context.parent) {
            var parentModel = this.context.parent.get('model'),
                field = parentModel.fields[value];
            if (field) {
                value = app.lang.get(field.label || field.vname, parentModel.module);
            }
        }
        return value;
    }
})
