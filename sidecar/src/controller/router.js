(function(app) {
    /**
     * @class Router
     * @singleton
     */
    var Router = Backbone.Router.extend({
        /**
         * @property {Object}
         */
        routes: {
            "": "index",
            "test": "test"
        },

        initialize: function(options) {
            _.bindAll(this);

            this.controller = options.controller || null;

            if (!this.controller) {
                throw "No Controller Specified";
            }

            // Start monitoring hash changes
            // Right now backbone doesn't support checking to see
            // if the history has been started.
            try {
                Backbone.history.start();
            } catch (e) {}
        },

        // Route functions
        index: function() {
            this.controller.loadView();
        },

        test: function() {
            this.controller.loadView();
        }
    });

    /**
     * @private
     */
    var module = {
        /**
         * Initializes the router when an instance is created
         * @method
         * @param {Object} instance
         */
        init: function(instance) {
            if (!instance.router && instance.controller) {
                _.extend(module, new Router({controller: instance.controller}));
            } else {
                throw "app.controller does not exist yet. Cannot create router instance";
            }
        }
    }

    app.augment("router", module);
})(SUGAR.App);