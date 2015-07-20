<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

require_once 'include/Expressions/Expression/Numeric/NumericExpression.php';

/**
 * <b>countConditional(Relate <i>link</i>, Field <i>string</i>, Values <i>list</i>)</b><br>
 * Returns the number of records related to this record by <i>link</i> and that matches the values for a specific field
 * ex: <i>count($contacts, 'first_name', array('Joe'))</i> in Accounts would return the <br/>
 * number of contacts related to this account with the first name of 'Joe'
 */
class CountConditionalRelatedExpression extends NumericExpression
{
    /**
     * Returns the entire enumeration bare.
     */
    public function evaluate()
    {
        $params = $this->getParameters();
        //This should be of relate type, which means an array of SugarBean objects
        $linkField = $params[0]->evaluate();
        $field = $params[1]->evaluate();
        $values = $params[2]->evaluate();
        //This should be of relate type, which means an array of SugarBean objects
        if (!is_array($linkField)) {
            return false;
        }

        if (!is_array($values)) {
            $values = array($values);
        }

        $count = 0;
        foreach ($linkField as $link) {
            if (in_array($link->$field, $values)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Returns the JS Equivalent of the evaluate function.
     *
     * Currently there is no JS Equivalent as this is a server side only method
     */
    public static function getJSEvaluate()
    {
        return <<<JS

        // this is only supported in Sidecar
        if (App === undefined) {
            return SUGAR.expressions.Expression.FALSE;
        }

        var params = this.params,
            view = this.context.view,
            target = this.context.target,
            relationship = params[0].evaluate(),
            condition_field = params[1].evaluate(),
            condition_values = params[2].evaluate();

        var model = this.context.relatedModel || this.context.model,  // the model
            // has the model been removed from it's collection
            hasModelBeenRemoved = this.context.isRemoveEvent || false,
            // is this being fired for the condition field or the rel_field?
            currentFieldIsConditionField = (this.context.changingField === condition_field),
            // did the condition field change at some point?
            conditionChanged = _.has(model.changed, condition_field),
            // is the condition field valid?
            conditionValid = _.contains(condition_values, model.get(condition_field));

        // if we have a model with out an id, ignore it
        if (!model.has('id')) {
            return;
        }
        if (conditionValid || conditionChanged) {
            var current_value = this.context.getRelatedField(relationship, 'countConditional', target) || '0',
                context_previous_values = this.context.previous_values || {},
                previous_value = context_previous_values[target + '--' + model.get('id')] || '',
                new_value = model.get(condition_field);
                rollup_value = undefined;

            // when the model is not new, and the previous_value is empty, lets try and fetch it from the
            // relatedModel just to make sure, as it might have a previous value
            if (!this.context.model.isNew() && _.isEmpty(previous_value)) {
                previous_value = model.previous(condition_field);
            }

            // store the new_value on the context for the rel_field
            // this allows multiple different formulas to change the rel_field while
            // maintaining the correct previous_value since it's not updated on the models previous_attributes
            // every time the model.set() is called before the initial set() completes
            this.context.previous_values = this.context.previous_values || {};
            this.context.previous_values[target + '--' + model.get('id')] = new_value;

            if (new_value == previous_value && !hasModelBeenRemoved) {
                return;
            }

            if (conditionValid && !hasModelBeenRemoved) {
                // if the condition is valid and the condition field changed, check if the previous value
                // was an invalid condition, if it was, the `new_value` just needs to be added back
                if (!_.contains(condition_values, previous_value)) {
                    rollup_value = App.math.add(current_value, 1, 0, true);
                }
            } else if ((!conditionValid && !hasModelBeenRemoved) || (hasModelBeenRemoved && conditionValid)) {
                rollup_value = App.math.sub(current_value, 1, 0, true);
            }

            // rollup_value won't exist if we didn't do any math, so just ignore this
            if (!_.isUndefined(rollup_value) && _.isFinite(rollup_value)) {
                // update the model
                this.context.model.set(target, rollup_value);
                // update the relationship defs on the model
                this.context.updateRelatedFieldValue(
                    relationship,
                    'countConditional',
                    target,
                    rollup_value,
                    this.context.model.isNew()
                );
            }
        }
JS;
    }

    /**
     * Returns the operation name that this Expression should be
     * called by.
     */
    public static function getOperationName()
    {
        return array("countConditional");
    }

    /**
     * The first parameter is a number and the second is the list.
     */
    public static function getParameterTypes()
    {
        return array(
            AbstractExpression::$RELATE_TYPE,
            AbstractExpression::$STRING_TYPE,
            AbstractExpression::$GENERIC_TYPE
        );
    }

    /**
     * Returns the maximum number of parameters needed.
     */
    public static function getParamCount()
    {
        return 3;
    }

    /**
     * Returns the String representation of this Expression.
     */
    public function toString()
    {
    }
}
