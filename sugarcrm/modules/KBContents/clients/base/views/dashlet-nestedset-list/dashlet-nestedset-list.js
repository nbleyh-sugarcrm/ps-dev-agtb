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
({

    plugins: ['Dashlet', 'NestedSetCollection', 'JSTree'],

    /**
     * Module name that provides an netedset data.
     *
     * @property {String}
     */
    moduleRoot: null,

    /**
     * Root ID of a shown NestedSet.
     * @property {String}
     */
    categoryRoot: null,

    /**
     * Module to load additional data into nested set.
     * @property {Object}
     * @property {String} extraModule.module Module to load additional data from.
     * @property {String} extraModule.field Linked field of provided module.
     */
    extraModule: null,

    /**
     * Cache to store loaded leafs to prevent extra loading.
     * @property {Object}
     */
    loadedLeafs: null,

    /**
     * Lifetime for data cache in ms.
     * @property {Number}
     */
    cacheLifetime: 300000,

    /**
     * Flag which indicate, if we need to use saved states.
     * @property {Boolean}
     */
    useStates: true,

    /**
     * Value of extraModule.field.
     * @property {String}
     */
    currentFieldValue: null,

    /**
     * Flag indicates should we hide tree.
     */
    hidden: null,

    /**
     * Initialize dashlet properties.
     */
    initDashlet: function() {
        var config = app.metadata.getModule(
            this.meta.config_provider,
            'config'
            ),
            currentContext = this.context.parent || this.context,
            currentModule = currentContext.get('module'),
            currentAction = currentContext.get('action');
        this.moduleRoot = this.settings.get('data_provider');
        this.categoryRoot = !_.isUndefined(config.category_root) ?
            config.category_root :
            null;
        this.extraModule = this.meta.extra_provider || {};
        if (currentModule === this.extraModule.module &&
            (currentAction === 'detail' || currentAction === 'edit')
        ) {
            this.useStates = false;
            this.changedCallback = _.bind(this.modelFieldChanged, this);
            this.savedCallback = _.bind(this.modelSaved, this);

            this.context.get('model').on('change:' + this.extraModule.field, this.modelFieldChanged, this);
            this.context.get('model').on('data:sync:complete', this.modelSaved, this);

            this.currentFieldValue = this.context.get('model').get(this.extraModule.field);
            this.on('openCurrentParent', this.hideTree, this);
        } else {
            this.on('stateLoaded', this.hideTree, this);
        }
        currentContext.on('subpanel:reload', function(args) {
            if (!_.isUndefined(args) &&
                _.isArray(args.links) &&
                (_.contains(args.links, 'revisions') || _.contains(args.links, 'localizations'))
            ) {
                this.layout.reloadDashlet({complete: function() {}, saveLeafs: false});
            }
        }, this);
        if (currentContext.get('collection') !== undefined) {
            this.listenTo(currentContext.get('collection'), 'data:sync:complete', function() {
                _.defer(function(self) {
                    if (self.layout.disposed === true) {
                        return;
                    }
                    if (!_.isUndefined(self.layout.reloadDashlet)) {
                        self.layout.reloadDashlet({complete: function() {}, saveLeafs: false});
                    }
                }, this);
            }, this);
        }

    },

    /**
     * The view doesn't need standard handlers for data change because it use own events and handlers.
     *
     * @override.
     */
    bindDataChange: function() {},

    /**
     * @inheritdoc
     */
    _render: function() {
        this._super('_render');
        if (this.meta.config) {
            return;
        }
        this.hideTree(this.hidden);
        var treeOptions = {
            settings: {
                category_root: this.categoryRoot,
                module_root: this.moduleRoot,
                plugins: [],
                liHeight: 14
            },
            options: {
            }},
            callbacks = {
                onLeaf: _.bind(this.leafClicked, this),
                onToggle: _.bind(this.folderToggled, this),
                onLoad: _.bind(this.treeLoaded, this),
                onSelect: _.bind(this.openRecord, this),
                onLoadState:  _.bind(this.stateLoaded, this)
            };
        if (this.useStates === true) {
            treeOptions.settings.plugins.push('state');
            treeOptions.options.state = {
                save_selected: false,
                auto_save: false,
                save_opened: 'jstree_open',
                options: {},
                storage: this._getStorage()
            };
        }
        this._renderTree(this.$('[data-place=dashlet-tree]'), treeOptions, callbacks);
    },

    /**
     * Return storage for tree state.
     * @return {Function}
     * @private
     */
    _getStorage: function () {
        var self = this;
        return function(key, value, options) {
            var intKey = app.user.lastState.buildKey(self.categoryRoot, self.moduleRoot, self.module);
            if (!_.isUndefined(value)) {
                app.user.lastState.set(intKey, value);
            }
            return app.user.lastState.get(intKey);
        };
    },

    /**
     * Handle tree selection.
     * @param data {Object} Selected item.
     */
    openRecord: function(data) {
        switch (data.type) {
            case 'document':
                if (_.isEmpty(this.extraModule.module)) {
                    break;
                }
                if (!this.$el.find('[data-id=' + data.id +']').data('disabled')) {
                    var route = app.router.buildRoute(this.extraModule.module, data.id);
                    app.router.navigate(route, {trigger: true});
                }
                break;
            case 'folder':
                if (this.$el.find('[data-id=' + data.id +']').hasClass('jstree-closed')) {
                    this.openNode(data.id);
                } else {
                    this.closeNode(data.id);
                }
                break;
        }
    },

    /**
     * Handle tree loaded. Load additional leafs for the tree.
     * @return {Boolean} Always true.
     */
    treeLoaded: function() {
        var self = this;
        async.forEach(
            this.collection.models,
            function(model, callback) {
                self.loadAdditionalLeaf(model.id, callback);
            },
            function() {
                if (self.useStates) {
                    self.loadJSTreeState();
                } else {
                    self.openCurrentParent();
                }
            }
        );
        return true;
    },

    /**
     * Open category, which is parent to current record.
     */
    openCurrentParent: function() {
        if (_.isEmpty(this.extraModule)
            || _.isEmpty(this.extraModule.module)
            || _.isEmpty(this.extraModule.field)
            ) {
            return;
        }
        var currentContext = this.context.parent || this.context,
            currentModel = currentContext.get('model'),
            id = currentModel.get(this.extraModule.field),
            self = this;
        this.loadAdditionalLeaf(id, function() {
            if (self.disposed) {
                return;
            }
            var nestedBean = self.collection.getChild(id);
            if (!_.isUndefined(nestedBean)) {
                nestedBean.getPath({
                    success: function(data) {
                        var path = [];
                        _.each(data, function(cat) {
                            if (cat.id == this.categoryRoot) {
                                return;
                            }
                            path.push({
                                id: cat.id,
                                name: cat.name
                            });
                        }, self);
                        path.push({
                            id: nestedBean.id,
                            name: nestedBean.get('name')
                        });
                        async.forEach(
                            path,
                            function(item, c) {
                                self.folderToggled({
                                    id: item.id,
                                    name: item.name,
                                    type: 'folder',
                                    open: true
                                }, c);
                            },
                            function() {
                                self.selectNode(currentModel.id);
                                self.trigger('openCurrentParent', false);
                            }
                        );
                    }
                });
            } else {
                self.trigger('openCurrentParent', false);
            }
        });
    },

    /**
     * Handle load state of tree.
     * Always returns true to process the code, which called the method.
     * @param {Object} data Data of loaded tree.
     * @return {Boolean} Always returns `true`.
     */
    stateLoaded: function(data) {
        var originalUseState = this.useStates,
            self = this;
        async.forEach(
            data.open,
            function(value, callback) {
                self.useStates = false;
                value.open = true;
                self.folderToggled(value, callback);
            },
            function() {
                _.each(data.open, function(value) {
                    self.openNode(value.id);
                });
                self.useStates = originalUseState;
                self.trigger('stateLoaded', false);
            }
        );
        return true;
    },

    /**
     * Handle toggle of tree folder.
     * Always returns true to process the code, which called the method.
     * @param {Object} data Toggled folder.
     * @param {Function} callback Async callback to use with async.js
     * @return {Boolean} Return `true` to continue execution, `false` otherwise..
     */
    folderToggled: function (data, callback) {
        var triggeredCallback = false,
            self = this;
        if (data.open) {
            var model = this.collection.getChild(data.id),
                items = [];
            //isModelUndefined = _.isUndefined(model) || _.isUndefined(model.id);
            //if (!isModelUndefined) {
            if (model.id) {
                items = model.children.models;
                if (items.length !== 0) {
                    triggeredCallback = true;
                    async.forEach(
                        items,
                        function(item, c) {
                            self.loadAdditionalLeaf(item.id, c);
                        },
                        function() {
                            if (_.isFunction(callback)) {
                                callback.call();
                                return false;
                            } else if (self.useStates) {
                                self.saveJSTreeState();
                            }
                        }
                    );
                }
            }
        }
        if (triggeredCallback === false && _.isFunction(callback)) {
            callback.call();
            return false;
        }
        if (this.useStates && triggeredCallback === false) {
            this.saveJSTreeState();
        }
        return true;
    },

    /**
     * Handle leaf click for tree.
     * @param {Object} data Clicked leaf.
     */
    leafClicked: function (data) {
        if (data.type !== 'folder') {
            if (_.isEmpty(this.extraModule.module)) {
                return;
            }
            if (!this.$el.find('[data-id=' + data.id +']').data('disabled')) {
                var route = app.router.buildRoute(this.extraModule.module, data.id);
                app.router.navigate(route, {trigger: true});
            }
            return;
        }
        this.loadAdditionalLeaf(data.id);
    },

    /**
     * Load extra data for tree.
     * @param {String} id Id of a leaf to load data in.
     * @param {Function} callback Async callback to use with async.js
     */
    loadAdditionalLeaf: function(id, callback) {
        var collection = app.data.createBeanCollection(this.extraModule.module),
            self = this;
        if (!_.isUndefined(this.loadedLeafs[id]) && this.loadedLeafs[id].timestamp < Date.now() - this.cacheLifetime) {
            delete this.loadedLeafs[id];
        }
        if (_.isEmpty(this.extraModule)
            || id === undefined
            || _.isEmpty(id)
            || _.isEmpty(this.extraModule.module)
            || _.isEmpty(this.extraModule.field)
            || !_.isUndefined(this.loadedLeafs[id])
        ) {
            if (!_.isUndefined(this.loadedLeafs[id])) {
                this.addLeafs(this.loadedLeafs[id].models, id);
            }
            if (_.isFunction(callback)) {
                callback.call();
            }
            return;
        }
        collection.options = {
            params: {
                order_by: 'date_entered:desc'
            },
            fields: [
                'id',
                'name'
            ]
        };

        collection.filterDef = [{}];
        collection.filterDef[0][this.extraModule.field] = {$equals: id};
        collection.filterDef[0]['status'] = {$equals: 'published'};
        collection.filterDef[0]['active_rev'] = {$equals: 1};
        collection.fetch({
            success: function(data) {
                self.addLeafs(data.models || [], id);
                if (_.isFunction(callback)) {
                    _.defer(function(f) {
                        f.call();
                    }, callback);

                }
            }
        });
    },

    /**
     * @inheritdoc
     *
     * Need additional check for tree leafs.
     *
     * @override
     */
    loadData: function(options) {
        this.hideTree(true);
        if (!options || _.isUndefined(options.saveLeafs) || options.saveLeafs === false) {
            this.loadedLeafs = {};
        }

        if (options && options.complete) {
            this._render();
            options.complete();
        }
    },

    /**
     * Override behavior of JSTree plugin.
     * @param {Data,BeanCollection} collection synced collection.
     */
    onNestedSetSyncComplete: function(collection) {
        if (this.disposed || this.collection.root !== collection.root) {
            return;
        }
        this.layout.reloadDashlet({complete: function() {}, saveLeafs: true});
    },

    /**
     * Handle change of this.extraModule.field.
     * @param {Data.Bean} model Changed model.
     * @param {String} value Current field value of the field.
     */
    modelFieldChanged: function(model, value) {
        delete this.loadedLeafs[this.currentFieldValue];
        this.currentFieldValue = value;
    },

    /**
     * Handle save of context model.
     */
    modelSaved: function() {
        delete this.loadedLeafs[this.currentFieldValue];
        this.onNestedSetSyncComplete(this.collection);
    },

    /**
     * @inheritdoc
     */
    _dispose: function() {
        var model;
        if (this.useStates === false && (model = this.context.get('model'))) {
            model.off('change:' + this.extraModule.field, this.changedCallback);
            model.off('data:sync:complete', this.savedCallback);
        }
        this._super('_dispose');
    },

    /**
     * Add documents as leafs for categories.
     * @param {Array} models Documents which should be added into the tree.
     * @param {String} id ID of category leaf to insert documents in.
     */
    addLeafs: function(models, id) {
        if (models.length !== 0) {
            this.removeChildrens(id, 'document');
            _.each(models, function(value) {
                var insData = {
                    id: value.id,
                    name: value.get('name'),
                    isViewable: app.acl.hasAccessToModel('view', value)
                };
                this.insertNode(insData, id, 'document');
            }, this);
            this.loadedLeafs[id] = {
                timestamp: Date.now(),
                models: models
            };
        }
    },

    /**
     * Hide or show tree,
     * @param {boolean} hide Whether we need to hide tree.
     */
    hideTree: function(hide) {
        hide = hide || false;
        if (!hide) {
            this.hidden = false;
            this.$('[data-place=dashlet-tree]').removeClass('hide').show();
            this.$('[data-place=loading]').addClass('hide').hide();
        } else {
            this.hidden = true;
            this.$('[data-place=dashlet-tree]').addClass('hide').hide();
            this.$('[data-place=loading]').removeClass('hide').show();
        }
    }
})
