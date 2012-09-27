({
    events: {
        'click .search': 'showSearch',
        'keyup .dataTables_filter input': 'filterDocuments'
    },
    initialize: function(o) {
        app.view.View.prototype.initialize.call(this, o);
        this.getData();
        this.hasData = false;

    },

    render: function() {
        if (!this.hasData && (!this.docs || this.docs.length <= 0)) {
            this.$el.hide();
            return;
        }
        this.hasData = true;
        this.$el.show();
        app.view.View.prototype.render.call(this);
        if(this.term){
            this.showSearch();
        }
    },
    showSearch: function(){
        this.$el.find('.dataTables_filter').toggle();

    },

    filterDocuments: function(evt){
        var self = this;
        var term = self.$(evt.currentTarget).val();
        if(!self.lazyGetData){
            self.lazyGetData = _.debounce(function(term) {
                self.getData(term);
            }, 500);
        }

        self.lazyGetData(term);
    },


    getData: function(term) {
        if(this.term && term && this.term === term) return;
        this.term = term;
        var url = '../rest/v10/google/docs';
        if(this.term){
            url += '?q=' + term + '&limit=20';
        }
        var self = this;
        app.api.call('GET', url, null, {
            success: function(o) {
                self.docs = o.docs;
                self.render();
            }
        });

    },

    bindDataChange: function() {
        var self = this;
        if (this.model) {
            this.model.on("change", function() {
                this.hasData = false;
                self.getData();
            }, this);
        }
    }
})
