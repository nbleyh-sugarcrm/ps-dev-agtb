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
import HeaderView from '../views/dashboard-header-view';
import DashboardView from '../views/dashboard-view';
import DashletView from '../views/dashlet-view';
import ProductCatalogQuickPicksDashlet from '../views/product-catalog-quick-picks-dashlet-view';
import RecordsMarkedForErasureDashlet from '../views/records-marked-for-erasure-dashlet';

/**
 * Represents a Sugar Dashboard layout.
 *
 * @class DashboardLayout
 * @extends BaseView
 */
export default class DashboardLayout extends BaseView {

    public HeaderView: HeaderView;
    public defaultView: DashboardView;
    public DashboardView: DashboardView;
    public ProductCatalogQuickPicksDashlet: DashletView;
    public RecordsMarkedForErasureDashlet: DashletView;

    protected type: string;

    constructor(options) {

        super(options);

        this.selectors = this.mergeSelectors({
            $: '.side.sidebar-content',
        });

        this.type = 'dashboard';

        this.defaultView = this.DashboardView = this.createComponent<DashboardView>(DashboardView, {
            module: options.module,
            default: true
        });

        this.HeaderView = this.createComponent<HeaderView>(HeaderView, {
            module: options.module,
        });

        this.ProductCatalogQuickPicksDashlet = this.createComponent<ProductCatalogQuickPicksDashlet>(ProductCatalogQuickPicksDashlet, {
            module: options.module,
        });

        this.RecordsMarkedForErasureDashlet = this.createComponent<RecordsMarkedForErasureDashlet>(RecordsMarkedForErasureDashlet, {
            module: options.module,
        });
    }
}
