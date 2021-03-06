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

import DashletView from './dashlet-view';
import PlannedActivitiesListView from './planned-activities-list-view';

/**
 * Represents Inactive Tasks dashlet
 *
 * @class HistoryDashlet
 * @extends DashletView
 */
export default class HistoryDashlet extends DashletView {

    public ActivitiesList: PlannedActivitiesListView;

    constructor(options) {
        super(options);

        this.selectors = this.mergeSelectors({
            $: `.dashlet-container[name=dashlet_${options.position}]`,
        });

        // Active Tasks shares PlannedActivitiesListView with Planned Activities dashlet
        this.ActivitiesList = this.createComponent<PlannedActivitiesListView>(PlannedActivitiesListView);
        }
    }
