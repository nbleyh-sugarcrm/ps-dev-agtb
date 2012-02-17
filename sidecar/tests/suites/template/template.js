describe('template', function () {
    var app = SUGAR.App;

    afterEach(function () {
        //Reset the cache after every test
        app.cache.cutAll()
        app.template.initialize();
        delete Handlebars.templates;
    });

    it('should compile templates', function () {
        var src = "Hello {{name}}!";
        var key = "testKey";
        var temp = app.template.compile(src, key);
        expect(temp({name:"Jim"})).toEqual("Hello Jim!");
    });

    it('should retrieve compiled templates', function () {
        var src = "Hello {{name}}!";
        var key = "testKey";
        //Compile the template
        app.template.compile(src, key);

        expect(app.cache.get("templates")).not.toBeEmpty();

        //The compiled template should be attached to Handlebars
        expect(app.template.get(key)).toEqual(Handlebars.templates[key]);

        //Get should return a compiled template
        expect(app.template.get(key)({name:"Jim"})).toEqual("Hello Jim!");
    });

    it('should retrieve compiled templates from cache', function () {
        var src = "Hello {{name}}!";
        var key = "testKey";
        //Compile the template
        app.template.compile(src, key);
        //Initialize will reset the internal varaibles referencing the tempaltes in memory
        app.template.initialize();

        //Get should return a compiled template
        expect(app.template.get(key)({name:"Jim"})).toEqual("Hello Jim!");
    });

    it('should load multiple templates in a single call', function () {
        var data = {
            hello : "Hello {{name}}!",
            foo : "Bar"
        };
        app.template.load(data);

        //Get should return both the templates
        expect(app.template.get("hello")({name:"Jim"})).toEqual("Hello Jim!");
        expect(app.template.get("foo")()).toEqual("Bar");
    });

});