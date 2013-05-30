({
    /**
     * Layout for filtering a collection.
     * Composed of a module dropdown(optional), a filter dropdown and an input
     *
     * @class BaseFilterLayout
     * @extends Layout
     */
    className: 'filter-view search',

    /**
     * @override
     * @param {Object} opts
     */
    initialize: function(opts) {
        var filterLayout = app.view._getController({type:'layout',name:'filter'});
        filterLayout.loadedModules = filterLayout.loadedModules || {};
        app.view.Layout.prototype.initialize.call(this, opts);

        this.layoutType = this.context.get('layout') || this.context.get('layoutName') || app.controller.context.get('layout');

        this.aclToCheck = (this.layoutType === 'record')? 'view' : 'list';
        this.filters = app.data.createBeanCollection('Filters');

        this.emptyFilter = app.data.createBean('Filters', {
            id: 'all_records',
            name: app.lang.get('LBL_FILTER_ALL_RECORDS'),
            filter_definition: {},
            editable: false
        });

        // Can't use getRelevantContextList here, because the context may not
        // have all the children we need.
        if (this.layoutType === 'records' && this.module !== 'Home') {
            this.context.set('skipFetch', true);
        } else {
            if(this.context.parent) {
                this.context.parent.set('skipFetch', true);
            }
            this.context.on('context:child:add', function(childCtx) {
                if (childCtx.get('link')) {
                    // We're in a subpanel.
                    childCtx.set('skipFetch', true);
                }
            }, this);
        }

        this.layout.on('filter:change:quicksearch', function(query, def) {
            this.trigger('filter:change:quicksearch', query, def);
        }, this);

        this.on('filter:change:quicksearch', function(query, dynamicFilterDef) {
            var self = this,
                ctxList = this.getRelevantContextList();

            _.each(ctxList, function(ctx) {
                var ctxCollection = ctx.get('collection'),
                    origFilterDef = dynamicFilterDef || ctxCollection.origFilterDef || [],
                    filterDef = self.getFilterDef(origFilterDef, query, ctx),
                    options = {
                        //Show alerts for this request
                        showAlerts: true,
                        success: function() {
                            // Close the preview pane to ensure that the preview
                            // collection is in sync with the list collection.
                            app.events.trigger('preview:close');
                    }};

                ctxCollection.filterDef = filterDef;
                ctxCollection.origFilterDef = origFilterDef;

                options = _.extend(options, ctx.get('collectionOptions'));

                ctx.resetLoadFlag(false);
                ctx.set('skipFetch', false);
                ctx.loadData(options);
            });
        }, this);

        this.on('filter:create:close', function() {

            this.layout.editingFilter = null;
            this.layout.trigger('filter:create:close');
        }, this);

        this.on('filter:create:open', function(filterModel) {
            this.layout.editingFilter = filterModel;
            this.layout.trigger('filter:create:open', filterModel);
        }, this);

        this.on('subpanel:change', function(linkName) {
            this.layout.trigger('subpanel:change', linkName);
        }, this);

        this.on('filter:get', this.initializeFilterState, this);

        this.on('filter:change:filter', function(id, preventCache) {
            if (id && id != 'create' && !preventCache) {
                app.cache.set("filters:last:" + this.layout.currentModule + ":" + this.layoutType, id);
            }
            var filter = this.filters.get(id) || this.emptyFilter,
                ctxList = this.getRelevantContextList();


            _.each(ctxList, function(ctx) {
                ctx.get('collection').origFilterDef = filter.get('filter_definition');
            });
            this.trigger('filter:clear:quicksearch');
        }, this);

        this.layout.on('filterpanel:change', function(name, silent) {
            this.showingActivities = name === 'activitystream';
            var module = this.showingActivities ? "Activities" : this.module;
            var link;

            if(this.layoutType === 'record' && !this.showingActivities) {
                module = link = app.cache.get("subpanels:last:" + module) || 'all_modules';
                if (link !== 'all_modules') {
                    module = app.data.getRelatedModule(this.module, link);
                }
            } else {
                link = null;
            }
            if (!silent) {
                this.trigger("filter:render:module");
                this.trigger("filter:change:module", module, link);
            }
        }, this);

        //When a filter is saved, update the cache and set the filter to be the currently used filter
        this.layout.on('filter:add', function(model){
            this.filters.add(model);
            app.cache.set("filters:" + this.layout.currentModule, this.filters.toJSON());
            app.cache.set("filters:last:" + this.layout.currentModule + ":" + this.layoutType, model.get("id"));
            this.layout.trigger('filter:reinitialize');
        }, this);

        // When a filter is deleted, update the cache and set the default filter
        // to be the currently used filter.
        this.layout.on('filter:remove', function(model){
            this.filters.remove(model);
            app.cache.set("filters:" + this.layout.currentModule, this.filters.toJSON());
            this.layout.trigger('filter:reinitialize');
        }, this);

        this.layout.on('filter:reinitialize', function() {
            this.initializeFilterState(this.layout.currentModule, this.layout.currentLink);
        }, this);
    },

    /**
     * Look for the relevant contexts. It can be
     * - the activity stream context
     * - the list view context on records layout
     * - the selection list view context on records layout
     * - the contexts of the subpanels on record layout
     * @returns {Array} array of contexts
     */
    getRelevantContextList: function() {
        var contextList = [], context;
        if (this.showingActivities) {
            context = this.layout.getActivityContext();
            if (context) {
                contextList.push(context);
            }
        } else {
            if (this.layoutType === 'records' && this.module !== "Home") {
                if (this.context.parent) {
                    contextList.push(this.context.parent);
                } else {
                    contextList.push(this.context);
                }
            } else {
                //Locate and add subpanel contexts
                _.each(this.context.children, function(childCtx) {
                    if (childCtx.get('link') && !childCtx.get('hidden')) {
                        contextList.push(childCtx);
                    }
                });
            }
        }
        return contextList;
    },

    /**
     * Builds the filter definition based on preselected filter and module quick search fields
     * @param {Object} origfilterDef
     * @param {String} searchTerm
     * @param {Context} context
     * @returns {Array} array containing filter def
     */
    getFilterDef: function(origfilterDef, searchTerm, context) {
        var searchFilter,
            filterDef = app.utils.deepCopy(origfilterDef),
            moduleQuickSearchFields = this.getModuleQuickSearchFields(context.get('module'));

        if (searchTerm) {
            searchFilter = [];
            _.each(moduleQuickSearchFields, function(fieldName) {
                var obj = {};
                obj[fieldName] = {'$starts': searchTerm};
                searchFilter.push(obj);
            });

            if (searchFilter.length > 1) {
                searchFilter = {'$or' : searchFilter};
            } else {
                searchFilter = searchFilter[0];
            }

            if (_.size(filterDef) === 0) {
                // Searching on 'all records'.
                filterDef = [searchFilter];
            } else {
                // We have some filter being applied already.
                // If it's an array, push the searchFilter into the $and filterDef.
                if (_.isArray(filterDef)) {
                    filterDef.push(searchFilter);
                } else {
                    filterDef = [filterDef, searchFilter];
                }
                filterDef = {'$and': filterDef};
            }
        }

        if(_.isArray(filterDef)) {
            return filterDef;
        }

        return [filterDef];
    },

    /**
     * Reset the filter to the previous state
     * @param {String} moduleName
     * @param {String} linkName
     */
    initializeFilterState: function(moduleName, linkName) {
        var self = this,
            callback = function(data) {
                var module = moduleName || (self.showingActivities? "Activities" : self.module),
                    link = linkName || data.link;

                if (!moduleName && self.layoutType === 'record' && link !== 'all_modules' && !self.showingActivities) {
                    module = app.data.getRelatedModule(module, data.link);
                }

                self.trigger('filter:change:module', module, link, true);
                self.getFilters(module, data.filter);
            };

        this.getPreviouslyUsedFilter(moduleName || this.module, callback);
    },

    /**
     * Gets previously used filters for a given module from the endpoint.
     * @param  {String}   moduleName
     * @param  {Function} callback
     */
    getPreviouslyUsedFilter: function(moduleName, callback) {
        var lastFilter = app.cache.get("filters:last:" + moduleName + ":" + this.layoutType);
        if (!(this.filters.get(lastFilter)))
            lastFilter = null;
        // TODO: This is temporary. We need to hook this up to the PreviouslyUsed API.
        if (this.layoutType === 'record' && !this.showingActivities) {
            callback({
                link: lastFilter || 'all_modules',
                filter: lastFilter || 'all_records'
            });
        } else {
            callback({
                filter: lastFilter || null
            });
        }
    },

    /**
     * Retrieves the appropriate list of filters from the server.
     * @param  {String} moduleName
     * @param  {String} defaultId
     */
    getFilters: function(moduleName, defaultId) {
        var lastFilter = app.cache.get("filters:last:" + moduleName + ":" + this.layoutType);
        var filter = [
            {'created_by': app.user.id},
            {'module_name': moduleName}
        ], self = this,
            callback = function() {
                var defaultFilterFromMeta,
                    possibleFilters = [],
                    filterMeta = self.getModuleFilterMeta(moduleName);

                if (filterMeta) {
                    _.each(filterMeta, function(value) {
                        if (_.isObject(value)) {
                            if (_.isObject(value.meta.filters)) {
                                self.filters.add(value.meta.filters);
                            }
                            if (value.meta.default_filter) {
                                defaultFilterFromMeta = value.meta.default_filter;
                            }
                        }
                    });

                    possibleFilters = [defaultId, defaultFilterFromMeta, 'all_records'];
                    possibleFilters = _.filter(possibleFilters, self.filters.get, self.filters);
                }

                if (lastFilter && !(self.filters.get(lastFilter))){
                    app.cache.cut("filters:last:" + moduleName + ":" + self.layoutType);
                }
                self.trigger('filter:render:filter');
                self.trigger('filter:change:filter', app.cache.get("filters:last:" + moduleName + ":" + self.layoutType) ||  _.first(possibleFilters) || 'all_records', true);
            };
        // TODO: Add filtering on subpanel vs. non-subpanel filters here.
        var filterLayout = app.view._getController({type:'layout',name:'filter'});
        if (filterLayout.loadedModules[moduleName] && !_.isEmpty(app.cache.get("filters:" + moduleName)))
        {
            this.filters.reset();
            var filters = app.cache.get("filters:" + moduleName);
            _.each(filters, function(f){
                self.filters.add(app.data.createBean("Filters", f));
            });
            callback();
        }
        else {
            this.filters.fetch({
                //Don't show alerts for this request
                showAlerts: false,
                filter: filter,
                success:function(){
                    filterLayout.loadedModules[moduleName] = true;
                    // app.view.layouts.FilterLayout.loadedModules[moduleName] = true;
                    app.cache.set("filters:" + moduleName, self.filters.toJSON());
                    callback();
                }
            });
        }
    },

    /**
     * Utility function to know if the create filter panel is opened.
     * @returns {Boolean} true if opened
     */
    createPanelIsOpen: function() {
        return !this.layout.$(".filter-options").is(":hidden");
    },

    /**
     * Determines whether a user can create a filter for the current module.
     * @return {Boolean} true if creatable
     */
    canCreateFilter: function() {
        // Check for create in meta and make sure that we're only showing one
        // module, then return false if any is false.
        var contexts = this.getRelevantContextList(),
            creatable = app.acl.hasAccess("create", "Filters"),
            meta;

        // Short circuit if we don't have the ACLs to create Filter beans.
        if (creatable && contexts.length === 1) {
            meta = app.metadata.getModule(contexts[0].get("module"));
            if (_.isObject(meta.filters)) {
                _.each(meta.filters, function(value) {
                    if (_.isObject(value)) {
                        creatable = creatable && value.meta.create !== false;
                    }
                });
            }
        }

        return creatable;
    },

    /**
     * Get filters metadata from module metadata for a module
     * @param {String} moduleName
     * @returns {Object} filters metadata
     */
    getModuleFilterMeta: function(moduleName) {
        var meta;
        if (moduleName !== 'all_modules') {
            meta = app.metadata.getModule(moduleName);
            if (_.isObject(meta)) {
                meta = meta.filters;
            }
        }

        return meta;
    },

    /**
     * Get list of quick search fields from filters metadata.
     * @param {String} moduleName
     * @returns {Array} array of field names
     */
    getModuleQuickSearchFields: function(moduleName) {
        var meta = this.getModuleFilterMeta(moduleName),
            fields,
            priority = 0;

        if (moduleName !== 'all_modules') {
            _.each(meta, function(value, key) {
                if (_.isObject(value) && value.meta.quicksearch_field && priority < value.meta.quicksearch_priority) {
                    fields = value.meta.quicksearch_field;
                    priority = value.meta.quicksearch_priority;
                }
            });
        }

        return fields;
    },

    /**
     * @override
     * @private
     */
    _render: function() {
        if (app.acl.hasAccess(this.aclToCheck, this.module)) {
            app.view.Layout.prototype._render.call(this);
            this.initializeFilterState();
        }
    },

    /**
     * @override
     */
    unbind: function() {
        this.filters.off();
        this.filters = null;
        app.view.Layout.prototype.unbind.call(this);
    }

})
