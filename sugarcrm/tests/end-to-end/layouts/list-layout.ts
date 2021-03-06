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

import BaseView from '../views/base-view';
import ListView from '../views/list-view';
import FilterView from '../views/filter-view';
import MassUpdateView from '../views/massupdate-view';
import HeaderView from '../views/record-header-view';
import MultilineListView from '../views/multiline-list-view';

/**
 * Represents List page layout.
 *
 * @class ListLayout
 * @extends BaseView
 */
export default class ListLayout extends BaseView {

    public type = 'list';
    public FilterView: FilterView;
    public MassUpdateView: MassUpdateView;
    public ListView: ListView;
    public MultilineListView: MultilineListView;
    public defaultView: ListView;
    public HeaderView: HeaderView;

    constructor(options) {
        super(options);

        this.selectors = this.mergeSelectors({
            $: '.main-pane:not([style*="display: none"])',
            'show more': '.btn.btn-link.btn-invisible.more',
        });

        this.FilterView = this.createComponent<FilterView>(FilterView, { module: options.module });
        this.MassUpdateView = this.createComponent<MassUpdateView>(MassUpdateView, { module: options.module });
        this.HeaderView = this.createComponent<HeaderView>(HeaderView);
        this.defaultView = this.ListView = this.createComponent<ListView>(ListView, { module: options.module, default: true });
        this.MultilineListView = this.createComponent<MultilineListView>(MultilineListView, { module: options.module });
    }
}
