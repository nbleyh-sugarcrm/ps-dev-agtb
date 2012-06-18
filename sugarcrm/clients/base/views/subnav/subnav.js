({
    events: {
        //same as edit-view::saveModel()
        'click [name=save_button]': 'saveModel'
    },
    /**
     * Listens to the app:view:change event and show or hide the subnav
     */
    initialize: function(options) {
        app.view.View.prototype.initialize.call(this, options);
        this.context.set('subnavModel', new Backbone.Model());
        this.subnavModel = this.context.get('subnavModel');
    },
    //same as edit-view::saveModel()
    saveModel: function() {
        var self = this;
        _.extend(this.meta, this.subnavModel.get('meta'));
        this.fields = this.subnavModel.get('fields') || this.fields;

        // TODO we need to dismiss this in global error handler
        app.alert.show('save_edit_view', {level: 'process', title: 'Saving'});
        this.model.save(null, {
            success: function() {
                app.alert.dismiss('save_edit_view');
                self.app.navigate(self.context, self.model, 'detail');
            },
            fieldsToValidate: this.getFields(this.module)
        });
    },
    bindDataChange: function() {
        if (this.subnavModel) {
            this.subnavModel.on("change", this.render, this);
        }
    }
})
