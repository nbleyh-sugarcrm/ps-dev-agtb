describe("Emails.Views.Compose", function() {
    var app, view;

    beforeEach(function() {
        app = SugarTest.app;
        SugarTest.testMetadata.init();
        SugarTest.loadComponent('base', 'view', 'editable');
        SugarTest.loadComponent('base', 'view', 'record');
        SugarTest.loadComponent('base', 'view', 'create');
        SugarTest.loadComponent('base', 'view', 'compose', 'Emails');
        SugarTest.testMetadata.set();

        var context = app.context.getContext();
        context.set({
            module: 'Emails',
            create: true
    });
        context.prepare();

        view = SugarTest.createView('base', 'Emails', 'compose', null, context, true);
    });

    afterEach(function() {
        SugarTest.testMetadata.dispose();
        app.cache.cutAll();
        app.view.reset();
        delete Handlebars.templates;
    });

    it("Intialize - model should not be empty", function() {
        expect(view.model.isNotEmpty).toBe(true);
    });

    describe('Render', function() {
        var setTitleStub, hideFieldStub, toggleSenderOptionsStub, populateToRecipientsStub;

        beforeEach(function() {
            setTitleStub = sinon.stub(view, 'setTitle'),
            hideFieldStub = sinon.stub(view, 'hideField'),
            toggleSenderOptionsStub = sinon.stub(view, 'toggleSenderOptions'),
            populateToRecipientsStub = sinon.stub(view, 'populateToRecipients');
        });

        afterEach(function() {
            setTitleStub.restore();
            hideFieldStub.restore();
            toggleSenderOptionsStub.restore();
            populateToRecipientsStub.restore();
        });

        it("No recipients on context - title should be set no recipients populated", function() {
            view._render();
            expect(setTitleStub).toHaveBeenCalled();
            expect(populateToRecipientsStub.callCount).toEqual(0);
        });

        it("Recipients on context - call is made to populate them", function() {
            var dummyRecipientModel = {'foo':'bar'};
            view.context.set('recipientModel', dummyRecipientModel);
            view._render();
            expect(populateToRecipientsStub.callCount).toEqual(1);
            expect(populateToRecipientsStub.lastCall.args).toEqual([dummyRecipientModel]);
        });

        //test different sender recipient scenarios
        var dataProvider = [
            {
                'testComment': 'model new, no cc or bcc => both hidden with links',
                'model': null,
                'hideFieldCount': 2,
                'hideFieldLastCallArgs': null,
                'toggleSenderLastCallArgs': ["to_addresses", true, true]
            },
            {
                'testComment': 'model not new and has cc => only bcc hidden with link',
                'model': {'id':'123','cc_addresses':'foo@bar.com'},
                'hideFieldCount': 1,
                'hideFieldLastCallArgs': ['bcc_addresses'],
                'toggleSenderLastCallArgs': ["to_addresses", false, true]
            },
            {
                'testComment': 'model not new and has bcc => only cc hidden with link',
                'model': {'id':'123','bcc_addresses':'foo@bar.com'},
                'hideFieldCount': 1,
                'hideFieldLastCallArgs': ['cc_addresses'],
                'toggleSenderLastCallArgs': ["to_addresses", true, false]
            },
            {
                'testComment': 'model not new and both cc & bcc => neither hidden, no links',
                'model': {'id':'123','cc_addresses':'foo@bar.com','bcc_addresses':'foo@bar.com'},
                'hideFieldCount': 0,
                'hideFieldLastCallArgs': null,
                'toggleSenderLastCallArgs': ["to_addresses", false, false]
            }
        ];

        _.each(dataProvider, function(data) {
            it(data.testComment, function() {
                view.model.off('change');
                if (data.model) {
                    view.model.set(data.model);
                }
                view._render();
                expect(hideFieldStub.callCount).toEqual(data.hideFieldCount);
                if (data.hideFieldLastCallArgs) {
                    expect(hideFieldStub.lastCall.args).toEqual(data.hideFieldLastCallArgs);
                }
                expect(toggleSenderOptionsStub.lastCall.args).toEqual(data.toggleSenderLastCallArgs);
            });
        });
    });

    describe('populateToRecipients', function() {
        var recipientModel, expectedResult, actualResult, contextTriggerStub;

        beforeEach(function() {
            expectedResult = {'id': '123', 'module': 'Foo'};
            recipientModel = new Backbone.Model({
                'id': expectedResult.id,
                '_module': expectedResult.module
            });
            contextTriggerStub = sinon.stub(view.context, 'trigger', function(trigger, recipient) {
                if (recipient) {
                    actualResult = recipient.attributes;
                }
            });
        });

        afterEach(function() {
            delete expectedResult;
            delete recipientModel;
            contextTriggerStub.restore();
        });

        it('should send email and name when email1 and name on model', function() {
            expectedResult.name = 'Tyler';
            expectedResult.email = 'foo@bar.com';
            recipientModel.set('name', expectedResult.name);
            recipientModel.set('email1', expectedResult.email);
            view.populateToRecipients(recipientModel);
            expect(actualResult).toEqual(expectedResult);
        });

        it('should send email only when email1 and no name on model', function() {
            expectedResult.email = 'foo@bar.com';
            recipientModel.set('email1', expectedResult.email);
            view.populateToRecipients(recipientModel);
            expect(actualResult).toEqual(expectedResult);
        });

        it('should send primary email and name when array on model', function() {
            expectedResult.name = 'Tyler';
            expectedResult.email = 'tyler@foo.com';
            recipientModel.set('email', [
                {'email_address': 'foo@bar.com'},
                {'email_address': expectedResult.email, 'primary_address': 1}
            ]);
            recipientModel.set('assigned_user_name', expectedResult.name);
            view.populateToRecipients(recipientModel);
            expect(actualResult).toEqual(expectedResult);
        });

        it('should not trigger event if no primary address on model', function() {
            recipientModel.set('email', [
                {'email_address': 'foo@bar.com'},
                {'email_address': 'tyler@foo.com'}
            ]);
            recipientModel.set('assigned_user_name', expectedResult.name);
            view.populateToRecipients(recipientModel);
            expect(contextTriggerStub.callCount).toBe(0);
        });

        it('should not trigger event if primary address is empty', function() {
            recipientModel.set('email', [
                {'email_address': 'foo@bar.com'},
                {'primary_address': 1}
            ]);
            view.populateToRecipients(recipientModel);
            expect(contextTriggerStub.callCount).toBe(0);
        });

        it('should not trigger event if no email at all', function() {
            view.populateToRecipients(recipientModel);
            expect(contextTriggerStub.callCount).toBe(0);
        });

    });

    describe('saveModel', function() {
        var apiCallStub, alertShowStub, alertDismissStub;

        beforeEach(function() {
            apiCallStub = sinon.stub(app.api, 'call', function(method, myURL, model, options) {
                options.success(model, null, options);
            });
            alertShowStub = sinon.stub(app.alert, 'show');
            alertDismissStub = sinon.stub(app.alert, 'dismiss');

            view.model.off('change');
        });

        afterEach(function() {
            apiCallStub.restore();
            alertShowStub.restore();
            alertDismissStub.restore();
        });

        it('should call mail api with correctly formatted model', function() {
            var actualModel,
                expectedStatus = 'ready';

            view.model.set('to_addresses', 'foo@bar.com');
            view.model.set('foo', 'bar');
            view.saveModel(expectedStatus, 'pending message', 'success message');

            expect(apiCallStub.lastCall.args[0]).toEqual('create');
            expect(apiCallStub.lastCall.args[1]).toMatch(/.*\/Mail/);

            actualModel = apiCallStub.lastCall.args[2];
            expect(actualModel.get('status')).toEqual(expectedStatus); //status set on model
            expect(actualModel.get('to_addresses')).toEqual([{email: 'foo@bar.com'}]); //email formatted correctly
            expect(actualModel.get('foo')).toEqual('bar'); //any other model attributes passed to api
        });

        it('should show pending message before call, then after call dismiss that message and show success', function() {
            var pending = 'pending message',
                success = 'success message';

            view.saveModel('ready', pending, success);

            expect(alertShowStub.firstCall.args[1].title).toEqual(pending);
            expect(alertDismissStub.firstCall.args[0]).toEqual(alertShowStub.firstCall.args[0]);
            expect(alertShowStub.secondCall.args[1].title).toEqual(success);
        })
    });

    describe('Send button', function() {
        beforeEach(function() {
            view.model.off('change');
        });

        it('should be disabled when to_addresses field is empty', function() {
            view.model.unset('to_addresses');
            view.model.set('subject', 'foo');
            view.model.set('html_body', 'bar');

            expect(view.isEmailSendable()).toBe(false);
        });

        it('should be enabled when to_addresses and subject fields are populated', function() {
            view.model.set('to_addresses', 'foo@bar.com');
            view.model.set('subject', 'foo');
            view.model.unset('html_body');

            expect(view.isEmailSendable()).toBe(true);
        });

        it('should be enabled when to_addresses and html_body fields are populated', function() {
            view.model.set('to_addresses', 'foo@bar.com');
            view.model.unset('subject');
            view.model.set('html_body', 'bar');

            expect(view.isEmailSendable()).toBe(true);
        });

        it('should be disabled when subject and html_body fields are empty', function() {
            view.model.set('to_addresses', 'foo@bar.com');
            view.model.unset('subject');
            view.model.unset('html_body');

            expect(view.isEmailSendable()).toBe(false);
        });
    });

    describe('Send', function() {
        var saveModelStub, alertShowStub;

        beforeEach(function() {
            saveModelStub = sinon.stub(view, 'saveModel');
            alertShowStub = sinon.stub(app.alert, 'show');

            view.model.off('change');
        });

        afterEach(function() {
            saveModelStub.restore();
            alertShowStub.restore();
        });

        it('should send email when subject and html_body fields are populated', function() {
            view.model.set('subject', 'foo');
            view.model.set('html_body', 'bar');

            view.send();

            expect(saveModelStub.calledOnce).toBe(true);
            expect(alertShowStub.called).toBe(false);
        });

        it('should show confirmation alert message when subject field is empty', function() {
            view.model.unset('subject');
            view.model.set('html_body', 'bar');

            view.send();

            expect(saveModelStub.called).toBe(false);
            expect(alertShowStub.calledOnce).toBe(true);
        });

        it('should show confirmation alert message when html_body field is empty', function() {
            view.model.set('subject', 'foo');
            view.model.unset('html_body');

            view.send();

            expect(saveModelStub.called).toBe(false);
            expect(alertShowStub.calledOnce).toBe(true);
        });
    });

    describe('insertTemplates - replacing signatures', function() {
        var apiCallStub, insertTemplateStub, signatureStub;

        beforeEach(function() {
            insertTemplateStub = sinon.stub(view, 'insertTemplateAttachments'),
                signatureStub = sinon.stub(view, 'updateEditorWithSignature'),
                apiCallStub = sinon.stub(app.api, 'call', function(method, myURL, model, options) {
                    options.success(model, null, options);
                });
            view.model.off('change');
        });

        afterEach(function() {
            apiCallStub.restore();
            insertTemplateStub.restore();
            signatureStub.restore();
        });

        it('should not populate editor if template parameter is not an object', function() {
            view.insertTemplate(null);
            expect(apiCallStub.callCount).toEqual(0);
            expect(insertTemplateStub.callCount).toEqual(0);
            expect(signatureStub.callCount).toEqual(0);
        });

        it('should call to set signature in editor with default signature id when not signature not selected', function() {
            var defaultId = '123445';
            view.defaultSignatureId = defaultId;
            view.insertTemplate(null);

            expect(apiCallStub.callCount).toEqual(0);
            expect(insertTemplateStub.callCount).toEqual(0);
            expect(signatureStub).toHaveBeenCalledWith(defaultId);
        });

        it('should call to set signature in editor with selected signature instead of default signature id', function() {
            var defaultId = '123445';
            var selectedSignatureId = '9999999';

            view.defaultSignatureId = defaultId;
            view.model.set('signature_id', selectedSignatureId);
            view.insertTemplate(null);

            expect(apiCallStub.callCount).toEqual(0);
            expect(insertTemplateStub.callCount).toEqual(0);
            expect(signatureStub).toHaveBeenCalledWith(selectedSignatureId);
        });
    });

    describe('insertTemplates - replacing templates', function() {
        var apiCallStub, insertTemplateStub, beanCollectionStub, signatureStub, field, fieldStub, getFieldStub;

        beforeEach(function() {
            insertTemplateStub = sinon.stub(view, 'insertTemplateAttachments'),
                signatureStub = sinon.stub(view, 'updateEditorWithSignature'),
                apiCallStub = sinon.stub(app.api, 'call', function (method, myURL, model, options) {
                    options.success(model, null, options);
                });

            beanCollectionStub = sinon.stub(app.data, 'createBeanCollection', function() {
                return {fetch:function(){}}
            });

            field = SugarTest.createField("base", "html_email", "htmleditable_tinymce", "edit");
            fieldStub = sinon.stub(field, "setEditorContent", function(){});
            getFieldStub = sinon.stub(view, 'getField').returns(field);

            view.model.off('change');
        });

        afterEach(function() {
            apiCallStub.restore();
            insertTemplateStub.restore();
            signatureStub.restore();
            fieldStub.restore();
            getFieldStub.restore();
            beanCollectionStub.restore();
        });

        it('should set content of editor with html version of template', function () {
            var Bean = SUGAR.App.Bean,
                bodyHtml = '<h1>Test/h1>',
                subject = 'This is my subject',
                templateModel = new Bean({
                    id:'1234',
                    subject:subject,
                    body_html:bodyHtml
                });

            fieldStub.withArgs(bodyHtml);
            view.insertTemplate(templateModel);

            expect(fieldStub.callCount).toEqual(1);
            expect(getFieldStub.callCount).toEqual(1);
            expect(beanCollectionStub.callCount).toEqual(1);
            expect(view.model.get('subject')).toEqual(subject);
        });

        it('should set content of editor with text only version of template', function () {
            var Bean = SUGAR.App.Bean,
                html = '<h1>Test/h1>',
                text = 'Test',
                subject = 'This is my subject',
                templateModel = new Bean({
                    id:'1234',
                    subject:subject,
                    body_html:html,
                    body:text,
                    text_only:1
                });

            fieldStub.withArgs(text);
            view.insertTemplate(templateModel);

            expect(fieldStub.callCount).toEqual(1);
            expect(getFieldStub.callCount).toEqual(1);
            expect(beanCollectionStub.callCount).toEqual(1);
            expect(view.model.get('subject')).toEqual(subject);
        });
    });
    
    describe('InitializeSendEmailModel', function() {
        beforeEach(function() {
            view.model.off('change');
        });

        it('should populate the send model attachments/documents correctly with both attachments and sugar documents', function() {
            var sendModel,
                attachment1 = {id:'123',type:'upload'},
                attachment2 = {id:'123',type:'document'},
                attachment3 = {id:'123',type:'foo'};

            view.model.set('attachments', [attachment1,attachment2,attachment3]);
            sendModel = view.initializeSendEmailModel();
            expect(sendModel.get('attachments')).toEqual([attachment1]);
            expect(sendModel.get('documents')).toEqual([attachment2]);
        });

        it('should populate the send model attachments/documents as empty when attachments not set', function() {
            var sendModel;
            view.model.unset('attachments');
            sendModel = view.initializeSendEmailModel();
            expect(sendModel.get('attachments')).toEqual([]);
            expect(sendModel.get('documents')).toEqual([]);
        });
    });

});
