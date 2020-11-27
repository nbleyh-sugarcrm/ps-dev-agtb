({
    extendsFrom: 'DashablelistView',
    /**
     * @inheritdoc
     * Don't load data if dashlet filter is not accessible.
     */
    loadData: function(options) {
        if(!this.already) {
            this.model.on('change:gtb_function_match_c', this.loadData, this);
            this.model.on('change:gtb_country_match_c', this.loadData, this);
            this.model.on('change:gtb_cluster_match_c', this.loadData, this);
            this.already = true;
        }
        this._super('loadData', [options]);
        filterDef = [];
        if(typeof this.context.get('collection') !== 'undefined') {
            this._displayDashlet(filterDef);
        }
    },

    _buildFilterDef: function(fieldName, operator, searchTerm) {
        var def = {};
        var filter = {};
        filter[operator] = searchTerm;
        def[fieldName] = filter;
        return def;
    },

    _displayDashlet: function(filterDef) {
        var candidateFunction = this.model.get('gtb_function_match_c');
        var candidateCountry = this.model.get('gtb_country_match_c');
        var candidateCluster = this.model.get('gtb_cluster_match_c');
        var filterOptions1 = this._buildFilterDef('pos_function', '$in', candidateFunction);
        var filterOptions2 = this._buildFilterDef('country', '$in', candidateCountry);
        var filterOptions3 = this._buildFilterDef('gtb_cluster', '$equals', candidateCluster);
        filterDef = _.extend({}, filterDef, filterOptions1);
        filterDef = _.extend({}, filterDef, filterOptions2);
        filterDef = _.extend({}, filterDef, filterOptions3);
        this._super('_displayDashlet', [filterDef]);
    },

    _dispose: function() {
        this.model.on('change:gtb_function_match_c', this.loadData);
        this.model.on('change:gtb_country_match_c', this.loadData);
        this.model.on('change:gtb_cluster_match_c', this.loadData);

        this._super('_dispose');
    },
})
