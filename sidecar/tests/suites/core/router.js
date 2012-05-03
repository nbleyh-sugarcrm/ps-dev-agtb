describe("Router", function() {
    var app, router;

    beforeEach(function() {
        app = SugarTest.app;
        router = app.router;
    });

    it("should call the controller to load a view for the default route", function() {
        var mock = sinon.mock(app.controller);
        mock.expects("loadView").once();

        router.start();
        expect(mock.verify()).toBeTruthy();
    });

    it("should build a route given a model", function(){
        var route,
            model = new Backbone.Model(),
            action = "edit";

        model.set("id", "1234");
        model.module = "Contacts";

        route = router.buildRoute(model.module, model.id, action);

        expect(route).toEqual("Contacts/1234/edit");
    });

    it("should build a route given a context", function(){
        var route,
            context = { get: function() { return "Contacts"; }},
            action = "create";

        route = router.buildRoute(context, null, action, {});

        expect(route).toEqual("Contacts/create");
    });

    it("should handle index route", function() {
        var mock = sinon.mock(app.controller);
        mock.expects("loadView").once().withArgs({
            module:'Contacts',
            layout:'list'
        });

        router.index('Contacts');
        expect(mock.verify()).toBeTruthy();
    });

    it("should handle arbitrary layout route", function() {
        var mock = sinon.mock(app.controller);
        mock.expects("loadView").once().withArgs({
            module:'Cases',
            layout:'list'
        });

        router.layout('Cases', 'list');
        expect(mock.verify()).toBeTruthy();
    });

    it("should handle create route", function() {
        var mock = sinon.mock(app.controller);
        mock.expects("loadView").once().withArgs({
            module: 'Cases',
            create: true,
            layout: 'edit'
        });

        router.create('Cases');
        expect(mock.verify()).toBeTruthy();
    });

    it("should handle record route", function() {
        var mock = sinon.mock(app.controller);
        mock.expects("loadView").once().withArgs({
            module: 'Cases',
            id: 123,
            action: 'edit',
            layout: 'edit'
        });

        router.record('Cases', 123, 'edit');
        expect(mock.verify()).toBeTruthy();
    });

    it("should handle login route", function() {
        var mock = sinon.mock(app.controller);
        mock.expects("loadView").once().withArgs({
            module:'Login',
            layout:'login',
            create: true
        });

        router.login();
        expect(mock.verify()).toBeTruthy();
    });

    it("should handle logout route", function() {
        var mock = sinon.mock(app.api);
        mock.expects("logout").once();

        router.logout();
        expect(mock.verify()).toBeTruthy();
    });

    // TODO: This test has been disabled, as the paramters don't work properly. Need to add supporting routes
    xit("should add params to a route if given in options ", function(){
        var route,
            context = {},
            options = {
                module: "Contacts",
                params: [
                    {name: "first", value: "Rick"},
                    {name: "last", value: "Astley"},
                    {name: "job", value: "Rock Star"}
                ]
            },
            action = "create";

        route = router.buildRoute(context, action, {}, options);

        expect(route).toEqual("Contacts/create?first=Rick&last=Astley&job=Rock+Star");
    });

});
