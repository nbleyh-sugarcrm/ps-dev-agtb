describe('BaseCasesCreateArticleActionField', function() {

    var app, field, moduleName = 'Cases';

    beforeEach(function() {
        app = SugarTest.app;

        SugarTest.loadComponent('base', 'field', 'button');
        SugarTest.loadComponent('base', 'field', 'rowaction');
        SugarTest.loadComponent('base', 'field', 'create-article-action', moduleName);
        field = SugarTest.createField('base', 'create-article-action', 'create-article-action', 'edit', {}, moduleName);
    });

    afterEach(function() {
        sinon.collection.restore();
        field.dispose();
        app.cache.cutAll();
        app.view.reset();
        Handlebars.templates = {};
        field = null;
    });

    it('should set the route based on model id', function() {
        field.model.id = 'test-case-id';
        field.initialize(field.options);
        expect(field.def.route).toEqual('bwc/index.php?module=KBDocuments&action=EditView&case_id=test-case-id');
    });

    it('should load the rowaction template', function() {
        var fieldType;
        sinon.collection.stub(field, '_super', function() {
            fieldType = field.type;
        });
        field._loadTemplate();
        expect(fieldType).toEqual('rowaction');
    });
});
