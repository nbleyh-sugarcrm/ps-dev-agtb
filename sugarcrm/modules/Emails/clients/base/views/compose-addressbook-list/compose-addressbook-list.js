({
    extendsFrom: "ListView",

    initialize: function(options) {
        app.view.views.ListView.prototype.initialize.call(this, options);
        this.collection.sync = this.sync;
        this.context.on("compose:addressbook:search", this._search, this);
    },

    sync: function (method, model, options) {
        var callbacks,
            url;

        options             = options || {};
        options.module_list = options.module_list || ["all"];

        //TODO:
        // this is a hack to make pagination work while trying to minimize the affect on existing configurations
        // there is a bug that needs to be fixed before the correct approach (config.maxQueryResult vs. options.limit)
        // can be determined
        app.config.maxQueryResult = app.config.maxQueryResult || 20;
        options.limit = options.limit || app.config.maxQueryResult;

        options = app.data.parseOptionsForSync(method, model, options);

        callbacks = app.data.getSyncCallbacks(method, model, options);
        this.trigger("data:sync:start", method, model, options);

        url = app.api.buildURL("MailRecipient", null, null, options.params);
        app.api.call("read", url, null, callbacks);
    },

    _render: function() {
        var self = this;

        // need to destroy the mass_collection so that mass_collection's event listeners are created appropriately
        // by actionmenu::bindDataChange
        // must do this before rendering the view, which renders the actionmenu field, which creates the listeners
        this.context.unset("mass_collection");

        app.view.views.ListView.prototype._render.call(this);

        var massCollection = this.context.get("mass_collection");

        if (!_.isEmpty(massCollection)) {
            // get rid of any old event listeners on the mass collection
            massCollection.off("add", null, this);
            massCollection.off("remove", null, this);
            massCollection.off("reset", null, this);

            // add the new event listeners
            massCollection.on("add", function(model) {
                if (model.id) {
                    self.context.trigger("recipients:compose_addressbook_selected_recipients:add", model);
                }
            }, this);

            massCollection.on("remove", function(model) {
                if (model.id) {
                    self.context.trigger("recipients:compose_addressbook_selected_recipients:remove", model);
                }
            }, this);

            massCollection.on("reset", function() {
                self.context.trigger("recipients:compose_addressbook_selected_recipients:replace");
            }, this);

            // find any currently selected recipients and add them to mass_collection so the checkboxes on the
            // corresponding rows are pre-selected
            var recipients = this.model.get("compose_addressbook_selected_recipients_collection");

            if (!_.isEmpty(recipients) && recipients.length > 0) {
                /**
                 * The following loop will fail to add recipients because the models don't contain an ID. This means
                 * that checkboxes will not be pre-selected. However, this bug will be fixed once we move to storing
                 * the recipients in the recipients field as a collection, instead of as a string. While the recipients
                 * string can be split into an array of Backbone.Model objects, the ID's are not persisted within the
                 * string, and therefore cannot be parsed out of the string and tied to the constructed Backbone.Model
                 * objects.
                 */
                _.each(recipients, function(model) {
                    massCollection.add(model);
                });
            }
        }
    },

    _search: function(module_list, term) {
        this.collection.fetch({query: term, module_list: [module_list], offset: 0}); // reset offset to 0 on a search
    }
})
