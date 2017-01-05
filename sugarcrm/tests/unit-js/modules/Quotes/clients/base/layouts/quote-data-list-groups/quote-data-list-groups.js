ddescribe('Quotes.Base.Layouts.QuoteDataListGroups', function() {
    var app;
    var layout;

    beforeEach(function() {
        app = SugarTest.app;
        SugarTest.testMetadata.init();
        SugarTest.testMetadata.set();

        sinon.collection.stub(app.metadata, 'getView', function() {
            return {
                panels: [{
                    fields: [
                        'field1', 'field2', 'field3', 'field4'
                    ]
                }]
            };
        });

        sinon.collection.stub(app.metadata, 'getLayout', function() {
            return {
                name: 'ProductBundlesQuoteDataGroupMetadata'
            };
        });
        layout = SugarTest.createLayout('base', 'Quotes', 'quote-data-list-groups', null, null, true);
        sinon.collection.stub(layout, 'before', function() {});
        sinon.collection.stub(layout, '_super', function() {});
    });

    afterEach(function() {
        sinon.collection.restore();
        layout.dispose();
        layout = null;
    });

    describe('initialize()', function() {
        it('should have className', function() {
            expect(layout.className).toBe('table dataTable quote-data-list-table');
        });

        it('should have tagName', function() {
            expect(layout.tagName).toBe('table');
        });

        it('should have sortableTag', function() {
            expect(layout.sortableTag).toBe('tbody');
        });

        it('should initialize groupIds to an empty array', function() {
            expect(layout.groupIds).toEqual([]);
        });

        it('should initialize currentBulkSaveRequests to an empty array', function() {
            expect(layout.currentBulkSaveRequests).toEqual([]);
        });

        it('should initialize quoteDataGroupMeta to ProductBundlesQuoteDataGroupLayout metadata', function() {
            expect(layout.quoteDataGroupMeta).toEqual({
                name: 'ProductBundlesQuoteDataGroupMetadata'
            });
        });

        it('should call .before render', function() {
            layout.initialize({});
            expect(layout.before).toHaveBeenCalledWith('render');
        });
    });

    describe('bindDataChange()', function() {
        beforeEach(function() {
            sinon.collection.spy(layout.context, 'on');
            sinon.collection.spy(layout.model, 'on');
            layout.bindDataChange();
        });

        it('should listen on layout.model for change:show_line_nums', function() {
            expect(layout.model.on).toHaveBeenCalledWith('change:show_line_nums');
        });

        it('should listen on layout.model for change:bundles', function() {
            expect(layout.model.on).toHaveBeenCalledWith('change:bundles');
        });

        it('should listen on layout.context for quotes:group:create', function() {
            expect(layout.context.on).toHaveBeenCalledWith('quotes:group:create');
        });

        it('should listen on layout.context for quotes:group:delete', function() {
            expect(layout.context.on).toHaveBeenCalledWith('quotes:group:delete');
        });

        it('should listen on layout.context for quotes:selected:delete', function() {
            expect(layout.context.on).toHaveBeenCalledWith('quotes:selected:delete');
        });

        it('should listen on layout.context for quotes:defaultGroup:create', function() {
            expect(layout.context.on).toHaveBeenCalledWith('quotes:defaultGroup:create');
        });

        it('should listen on layout.context for quotes:defaultGroup:save', function() {
            expect(layout.context.on).toHaveBeenCalledWith('quotes:defaultGroup:save');
        });
    });

    describe('_onShowLineNumsChanged()', function() {
        beforeEach(function() {
            sinon.collection.stub(layout.context, 'trigger', function() {});
        });

        it('should trigger the quotes:show_line_nums:changed event with true', function() {
            layout._onShowLineNumsChanged({}, true);

            expect(layout.context.trigger).toHaveBeenCalledWith('quotes:show_line_nums:changed', true);
        });

        it('should trigger the quotes:show_line_nums:changed event with false', function() {
            layout._onShowLineNumsChanged({}, false);

            expect(layout.context.trigger).toHaveBeenCalledWith('quotes:show_line_nums:changed', false);
        });
    });

    describe('_render()', function() {
        beforeEach(function() {
            sinon.collection.stub($.fn, 'sortable', function() {});
        });

        describe('when there are no sortable items', function() {
            beforeEach(function() {
                sinon.collection.stub(layout, '$', function() {
                    return [];
                });
            });

            it('should not call sortable', function() {
                layout._render();
                expect($.fn.sortable).not.toHaveBeenCalled();
            });
        });

        describe('when there are sortable items', function() {
            beforeEach(function() {
                sinon.collection.stub(layout, '$', function() {
                    return ['<div></div>'];
                });

                layout._render();
            });

            it('should call sortable', function() {
                expect($.fn.sortable).toHaveBeenCalled();
            });

            describe('with the correct sortable params', function() {
                var callObj;
                beforeEach(function() {
                    callObj = $.fn.sortable.args[0][0];
                });

                afterEach(function() {
                    callObj = null;
                });

                it('should call with axis = "y"', function() {
                    expect(callObj.axis).toBe('y');
                });

                it('should call with connectWith = "tbody"', function() {
                    expect(callObj.connectWith).toBe('tbody');
                });

                it('should call with cursor = "move"', function() {
                    expect(callObj.cursor).toBe('move');
                });

                it('should call with helper = "clone"', function() {
                    expect(callObj.helper).toBe('clone');
                });

                it('should call with items = "tr.sortable"', function() {
                    expect(callObj.items).toBe('tr.sortable');
                });

                it('should call with out handler defined', function() {
                    expect(callObj.out).toBeDefined();
                });

                it('should call with over handler defined', function() {
                    expect(callObj.over).toBeDefined();
                });

                it('should call with placeholder = "ui-state-highlight"', function() {
                    expect(callObj.placeholder).toBe('ui-state-highlight');
                });

                it('should call with revert = true', function() {
                    expect(callObj.revert).toBeTruthy();
                });

                it('should call with start handler defined', function() {
                    expect(callObj.start).toBeDefined();
                });

                it('should call with stop handler defined', function() {
                    expect(callObj.stop).toBeDefined();
                });
            });
        });
    });

    describe('_onDragStart()', function() {
        beforeEach(function() {
            sinon.collection.stub(app.tooltip, 'clear', function() {});
            sinon.collection.stub(app.tooltip, '_disable', function() {});

            layout._onDragStart();
        });

        it('should call tooltip clear', function() {
            expect(app.tooltip.clear).toHaveBeenCalled();
        });

        it('should call tooltip _disable', function() {
            expect(app.tooltip._disable).toHaveBeenCalled();
        });
    });

    describe('_onDragStop()', function() {
        var evtParam;
        var uiParam;
        var oldGroup;
        var oldGroupId;
        var oldGroupModel;
        var oldGroupTriggerSpy;
        var newGroup;
        var newGroupId;
        var newGroupModel;
        var newGroupTriggerSpy;
        var rowModelId;
        var rowModelModule;
        var rowModel;

        beforeEach(function() {
            evtParam = {};

            oldGroupTriggerSpy = sinon.collection.spy();
            newGroupTriggerSpy = sinon.collection.spy();

            rowModelModule = 'Products';
            rowModel = app.data.createBean('Products', {
                id: rowModelId,
                module: rowModelModule,
                position: 2
            });
            rowModelId = rowModel.cid;
            //rowModel.module = rowModelModule;

            oldGroupModel = app.data.createBean('ProductBundles', {
                id: oldGroupId,
                name: 'oldGroupModelName_original'
            });

            oldGroupId = oldGroupModel.cid;
            oldGroup = {
                groupId: oldGroupId,
                collection: app.data.createBeanCollection('ProductBundles'),
                trigger: oldGroupTriggerSpy,
                model: oldGroupModel,
                addRowModel: function(model, isEdit) {
                    this.collection.add(model);
                },
                removeRowModel: function(model, isEdit) {
                    this.collection.remove(model);
                }
            };

            newGroupModel = app.data.createBean('ProductBundles', {
                id: 'new_id',
                name: 'newGroupModelName_original'
            });
            newGroupId = newGroupModel.cid;
            newGroup = {
                groupId: newGroupId,
                collection: app.data.createBeanCollection('ProductBundles'),
                trigger: newGroupTriggerSpy,
                model: newGroupModel,
                addRowModel: function(model, isEdit) {
                    this.collection.add(model);
                },
                removeRowModel: function(model, isEdit) {
                    this.collection.remove(model);
                },
                $: function() {
                    return [
                        '<tr name="Products_' + oldGroupId + '" ></tr>',
                        '<tr name="Products_' + rowModelId + '" ></tr>'
                    ];
                }
            };

            newGroup.collection.comparator = function(model) {
                return model.get('position');
            };

            sinon.collection.stub(app.tooltip, '_enable', function() {});
            sinon.collection.stub(layout, '_saveDefaultGroupThenCallBulk', function() {});
            sinon.collection.stub(layout, '_callBulkRequests', function() {});

            sinon.collection.stub(layout, '_getComponentByGroupId', function(id) {
                if (id === oldGroupId) {
                    return oldGroup;
                } else {
                    return newGroup;
                }
            });
            sinon.collection.stub(layout, '_updateRowPositions', function(group) {
                return {
                    data: {
                        position: 0
                    },
                    method: 'PUT',
                    url: '/v10/ProductBundles/' + group.groupId + '/link/products/rowModelId1'
                };
            });
        });

        afterEach(function() {
            evtParam = null;
            uiParam = null;
        });

        describe('when old group is different than new group', function() {
            beforeEach(function() {
                uiParam = {
                    item: {
                        get: function() {
                            return '<div data-group-id="' + oldGroupId + '"' +
                                'name="' + rowModelModule + '_' + rowModelId + '"></div>';
                        }
                    }
                };
                sinon.collection.stub($.fn, 'parent', function() {
                    return '<div data-group-id="' + newGroupId + '"></div>';
                });
                sinon.collection.stub(layout, '_moveItemToNewGroup', function() {});
                oldGroup.collection.add(rowModel);

                layout.currentBulkSaveRequests = [{
                    id: 'hey'
                }];
                layout._onDragStop(evtParam, uiParam);
            });

            it('should call _getComponentByGroupId with oldGroup ID', function() {
                expect(layout._getComponentByGroupId).toHaveBeenCalledWith(oldGroupId);
            });

            it('should call _getComponentByGroupId with newGroup ID', function() {
                expect(layout._getComponentByGroupId).toHaveBeenCalledWith(newGroupId);
            });

            describe('triggering group events', function() {
                describe('on oldGroup', function() {
                    it('should trigger quotes:group:changed', function() {
                        expect(oldGroupTriggerSpy.args[0][0]).toBe('quotes:group:changed');
                    });

                    it('should trigger quotes:group:save:start', function() {
                        expect(oldGroupTriggerSpy.args[1][0]).toBe('quotes:group:save:start');
                    });
                });

                describe('on newGroup', function() {
                    it('should trigger quotes:group:changed', function() {
                        expect(newGroupTriggerSpy.args[0][0]).toBe('quotes:group:changed');
                    });

                    it('should trigger quotes:group:save:start', function() {
                        expect(newGroupTriggerSpy.args[1][0]).toBe('quotes:group:save:start');
                    });
                });
            });

            describe('on save default group', function() {
                it('should call _callBulkRequests with an regular saved group', function() {
                    expect(layout._saveDefaultGroupThenCallBulk).not.toHaveBeenCalled();
                    expect(layout._callBulkRequests).toHaveBeenCalled();
                });
                it('should call _saveDefaultGroupThenCallBulk with an unsaved group', function() {
                    sinon.collection.stub(newGroupModel, 'isNew', function() { return true });
                    // reset everything back
                    layout._saveDefaultGroupThenCallBulk.restore();
                    layout._callBulkRequests.restore();
                    sinon.collection.stub(layout, '_saveDefaultGroupThenCallBulk', function() {});
                    sinon.collection.stub(layout, '_callBulkRequests', function() {});
                    newGroup.collection.remove(rowModel);
                    oldGroup.collection.add(rowModel);

                    layout._onDragStop(evtParam, uiParam);

                    expect(newGroupModel.isNew).toHaveBeenCalled();
                    expect(layout._saveDefaultGroupThenCallBulk).toHaveBeenCalled();
                    expect(layout._callBulkRequests).not.toHaveBeenCalled();
                });
            });
        });

        describe('when old group is the same as new group', function() {
            beforeEach(function() {
                uiParam = {
                    item: {
                        get: function() {
                            return '<div data-group-id="' + newGroupId + '"' +
                                'name="' + rowModelModule + '_' + rowModelId + '"></div>';
                        }
                    }
                };
                sinon.collection.stub($.fn, 'parent', function() {
                    return '<div data-group-id="' + newGroupId + '"></div>';
                });

                newGroup.collection.add(rowModel);

                layout._onDragStop(evtParam, uiParam);
            });

            it('should have the rowModel still in newGroup', function() {
                expect(newGroup.collection.length).toBe(1);
            });

            it('should not call _getComponentByGroupId with oldGroup ID', function() {
                expect(layout._getComponentByGroupId).not.toHaveBeenCalledWith(oldGroupId);
            });

            it('should call _getComponentByGroupId with newGroup ID', function() {
                expect(layout._getComponentByGroupId).toHaveBeenCalledWith(newGroupId);
            });

            describe('triggering group events', function() {
                describe('on oldGroup', function() {
                    it('should not trigger any events on the oldGroup', function() {
                        expect(oldGroupTriggerSpy).not.toHaveBeenCalled();
                    });
                });

                describe('on newGroup', function() {
                    it('should trigger quotes:group:changed', function() {
                        expect(newGroupTriggerSpy.args[0][0]).toBe('quotes:group:changed');
                    });

                    it('should trigger quotes:group:save:start', function() {
                        expect(newGroupTriggerSpy.args[1][0]).toBe('quotes:group:save:start');
                    });
                });
            });

            it('should call _callBulkRequests with an regular saved group', function() {
                expect(layout._saveDefaultGroupThenCallBulk).not.toHaveBeenCalled();
                expect(layout._callBulkRequests).toHaveBeenCalled();
            });
        });

        describe('always re-enable tooltips', function() {
            beforeEach(function() {
                uiParam = {
                    item: {
                        get: function() {
                            return '<div data-group-id="' + newGroupId + '"' +
                                'name="' + rowModelModule + '_' + rowModelId + '"></div>';
                        }
                    }
                };
                sinon.collection.stub($.fn, 'parent', function() {
                    return '<div data-group-id="' + newGroupId + '"></div>';
                });

                layout._onDragStop(evtParam, uiParam);
            });

            it('should call tooltip _enable', function() {
                expect(app.tooltip._enable).toHaveBeenCalled();
            });
        });
    });


    describe('moveMassCollectionItemsToNewGroup()', function() {
        var rowModel;
        var massCollection;
        var newGroupData;

        beforeEach(function() {
            massCollection = new Backbone.Collection();

            rowModel = app.data.createBean('Products', {
                id: 'rowModelId'
            });

            rowModel.link = {
                bean: {
                    id: 'oldGroupId'
                }
            };

            newGroupData = {
                related_record: {
                    id: 'newGroupId'
                }
            };

            massCollection.add(rowModel);
            layout.context.set('mass_collection', massCollection);

            sinon.collection.stub(layout, '_moveItemToNewGroup', function() {});
            sinon.collection.stub(layout, '_callBulkRequests', function() {});
        });

        afterEach(function() {
            rowModel = null;
            massCollection = null;
            newGroupData = null;
        });

        it('should not call _moveItemToNewGroup() if massCollection is empty', function() {
            massCollection.reset();
            layout.moveMassCollectionItemsToNewGroup(newGroupData);

            expect(layout._moveItemToNewGroup).not.toHaveBeenCalled();
        });

        it('should call _moveItemToNewGroup() with isRowEdit false', function() {
            layout.moveMassCollectionItemsToNewGroup(newGroupData);

            expect(layout._moveItemToNewGroup).toHaveBeenCalledWith('oldGroupId', 'newGroupId', 'rowModelId', false);
        });

        it('should call _moveItemToNewGroup() with isRowEdit true', function() {
            rowModel.modelView = 'edit';
            layout.moveMassCollectionItemsToNewGroup(newGroupData);

            expect(layout._moveItemToNewGroup).toHaveBeenCalledWith('oldGroupId', 'newGroupId', 'rowModelId', true);
        });

        it('should call _callBulkRequests()', function() {
            layout.moveMassCollectionItemsToNewGroup(newGroupData);

            expect(layout._callBulkRequests).toHaveBeenCalled();
        });

        it('should reset mass collection', function() {
            layout.moveMassCollectionItemsToNewGroup(newGroupData);

            expect(massCollection.length).toBe(0);
        });
    });

    describe('_onSaveUpdatedMassCollectionItemsSuccess()', function() {
        var bulkResponses;
        var newGroup;
        var groupModel;
        var groupCollection;
        var rowModel;

        beforeEach(function() {
            bulkResponses = [{
                contents: {
                    record: {
                        id: 'bundleId1',
                        total: '100'
                    },
                    related_record: {
                        id: 'modelId1',
                        total: '100',
                        date_modified: '100',
                        position: 0
                    }
                }
            }];

            groupModel = app.data.createBean('ProductBundles', {
                id: 'bundleId1',
                total: '0'
            });
            groupCollection = new Backbone.Collection();

            rowModel = app.data.createBean('Products', {
                id: 'modelId1',
                position: '1',
                date_modified: '50'
            });
            groupCollection.add(rowModel);

            newGroup = {
                model: groupModel,
                collection: groupCollection
            };

            sinon.collection.stub(layout, '_getComponentByGroupId', function() {
                return newGroup;
            });

            layout._onSaveUpdatedMassCollectionItemsSuccess(bulkResponses);
        });

        afterEach(function() {
            bulkResponses = null;
            rowModel = null;
            groupModel = null;
            groupCollection = null;
            newGroup = null;
        });

        it('should update the group model with new data', function() {
            expect(groupModel.get('total')).toBe('100');
        });

        it('should update the row model with new data', function() {
            expect(rowModel.get('position')).toBe(0);
        });
    });

    describe('_updateModelWithRecord()', function() {
        var model;
        var record;

        beforeEach(function() {
            model = app.data.createBean('Products', {
                id: 'oldId'
            });
            record = {
                id: 'newId'
            };

            sinon.collection.spy(model, 'setSyncedAttributes');
            sinon.collection.spy(model, 'set');
        });

        afterEach(function() {
            model = null;
            record = null;
        });

        it('should do nothing if model does not exist', function() {
            layout._updateModelWithRecord(undefined, record);

            expect(model.set).not.toHaveBeenCalled();
        });

        it('should call setSyncedAttributes if model exists', function() {
            layout._updateModelWithRecord(model, record);

            expect(model.setSyncedAttributes).toHaveBeenCalledWith(record);
            expect(model.getSynced('id')).toBe('newId');
        });

        it('should call set if model exists', function() {
            layout._updateModelWithRecord(model, record);

            expect(model.set).toHaveBeenCalledWith(record);
            expect(model.get('id')).toBe('newId');
        });
    });

    describe('_moveItemToNewGroup()', function() {
        var oldGroup;
        var oldGroupId;
        var oldGroupModel;
        var newGroup;
        var newGroupId;
        var newGroupModel;
        var rowModelId;
        var rowModelModule;
        var rowModel;
        var oldGroupTriggerSpy;
        var newGroupTriggerSpy;

        beforeEach(function() {
            oldGroupTriggerSpy = sinon.collection.spy();
            newGroupTriggerSpy = sinon.collection.spy();

            rowModelId = 'rowModelId1';
            rowModelModule = 'Products';
            rowModel = new Backbone.Model({
                id: rowModelId,
                module: rowModelModule,
                position: 2
            });
            rowModel.module = rowModelModule;

            oldGroupId = 'oldGroupId1';
            oldGroupModel = app.data.createBean('ProductBundles', {
                id: oldGroupId,
                name: 'oldGroupModelName_original'
            });
            oldGroup = {
                groupId: oldGroupId,
                collection: app.data.createBeanCollection('ProductBundles'),
                model: oldGroupModel,
                trigger: oldGroupTriggerSpy,
                removeRowModel: function(model, isEdit) {
                    this.collection.remove(model);
                }
            };

            newGroupId = 'newGroupId1';
            newGroupModel = app.data.createBean('ProductBundles', {
                id: newGroupId,
                name: 'newGroupModelName_original'
            });
            newGroup = {
                groupId: newGroupId,
                collection: app.data.createBeanCollection('ProductBundles'),
                model: newGroupModel,
                trigger: newGroupTriggerSpy,
                addRowModel: function(model, isEdit) {
                    this.collection.add(model);
                }
            };

            sinon.collection.stub(layout, '_getComponentByGroupId', function(id) {
                if (id === oldGroupId) {
                    return oldGroup;
                } else {
                    return newGroup;
                }
            });

            sinon.collection.stub(layout, '_updateRowPositions', function(group) {
                var retCalls = [];

                _.each(group.collection.models, _.bind(function(retCalls, model, index) {
                    if (model && index != model.previous('position')) {
                        retCalls.push({
                            data: {
                                position: index
                            },
                            method: 'PUT',
                            url: '/v10/ProductBundles/' + group.groupId + '/link/products/' + model.id
                        });
                    }
                }, this, retCalls), this);
                return retCalls;
            });
        });

        afterEach(function() {
            oldGroup = null;
            oldGroupId = null;
            oldGroupModel = null;
            newGroup = null;
            newGroupId = null;
            newGroupModel = null;
            rowModelId = null;
            rowModelModule = null;
            rowModel = null;
            oldGroupTriggerSpy = null;
            newGroupTriggerSpy = null;
        });

        describe('adding/removing from collections and setting positions', function() {
            beforeEach(function() {
                oldGroup.collection.add(rowModel);
                layout._moveItemToNewGroup(oldGroupId, newGroupId, rowModelId, false);
            });

            it('should call _getComponentByGroupId with oldGroup ID', function() {
                expect(layout._getComponentByGroupId).toHaveBeenCalledWith(oldGroupId);
            });

            it('should call _getComponentByGroupId with newGroup ID', function() {
                expect(layout._getComponentByGroupId).toHaveBeenCalledWith(newGroupId);
            });

            it('should set new position on rowModel when newPosition is not passed in', function() {
                expect(rowModel.get('position')).toBe(0);
            });

            it('should remove the rowModel from oldGroup', function() {
                expect(oldGroup.collection.length).toBe(0);
            });

            it('should add the rowModel to newGroup', function() {
                expect(newGroup.collection.length).toBe(1);
            });

            it('should call quotes:line_nums:reset on oldGroup', function() {
                expect(oldGroupTriggerSpy).toHaveBeenCalledWith('quotes:line_nums:reset');
            });

            it('should call quotes:line_nums:reset on newGroup', function() {
                expect(newGroupTriggerSpy).toHaveBeenCalledWith('quotes:line_nums:reset');
            });
        });

        describe('when newPosition is passed in', function() {
            beforeEach(function() {
                oldGroup.collection.add(rowModel);
                layout._moveItemToNewGroup(oldGroupId, newGroupId, rowModelId, false, 2);
            });

            it('should set the position to be the passed in position', function() {
                expect(rowModel.get('position')).toBe(2);
            });
        });

        describe('setting currentBulkSaveRequests', function() {
            var rowModel2Id;
            var rowModel2Module;
            var rowModel2;
            var result;

            beforeEach(function() {
                rowModel2Id = 'rowModelId2';
                rowModel2Module = 'Products';
                rowModel2 = new Backbone.Model({
                    id: rowModel2Id,
                    module: rowModel2Module,
                    position: 1
                });
                rowModel2.module = rowModel2Module;

                // set rowModel's position to 1
                rowModel.set('position', 2);

                oldGroup.collection.add(rowModel2);
                oldGroup.collection.add(rowModel);
                layout.currentBulkSaveRequests = [];

                layout._moveItemToNewGroup(oldGroupId, newGroupId, rowModelId, false);
            });

            afterEach(function() {
                rowModel2Id = null;
                rowModel2Module = null;
                rowModel2 = null;
            });

            it('should set currentBulkSaveRequests first call URL', function() {
                result = layout.currentBulkSaveRequests[0].url;

                expect(result.indexOf('ProductBundles/newGroupId1/link/products/rowModelId1')).not.toBe(-1);
            });

            it('should set currentBulkSaveRequests first call METHOD', function() {
                result = layout.currentBulkSaveRequests[0].method;

                expect(result).toBe('POST');
            });

            it('should set currentBulkSaveRequests first call DATA', function() {
                result = layout.currentBulkSaveRequests[0].data;

                expect(result).toEqual({
                    id: newGroupId,
                    link: 'products',
                    related: {
                        position: 0
                    },
                    relatedId: rowModelId
                });
            });

            it('should set currentBulkSaveRequests second call URL', function() {
                result = layout.currentBulkSaveRequests[1].url;

                expect(result.indexOf('ProductBundles/oldGroupId1/link/products/rowModelId2')).not.toBe(-1);
            });

            it('should set currentBulkSaveRequests second call METHOD', function() {
                result = layout.currentBulkSaveRequests[1].method;

                expect(result).toBe('PUT');
            });

            it('should set currentBulkSaveRequests second call DATA', function() {
                result = layout.currentBulkSaveRequests[1].data;

                expect(result).toEqual({
                    position: 0
                });
            });

            it('should set currentBulkSaveRequests third call URL', function() {
                result = layout.currentBulkSaveRequests[2].url;

                expect(result.indexOf('ProductBundles/newGroupId1/link/products/rowModelId1')).not.toBe(-1);
            });

            it('should set currentBulkSaveRequests third call METHOD', function() {
                result = layout.currentBulkSaveRequests[2].method;

                expect(result).toBe('PUT');
            });

            it('should set currentBulkSaveRequests third call DATA', function() {
                result = layout.currentBulkSaveRequests[2].data;

                expect(result).toEqual({
                    position: 0
                });
            });
        });
    });

    describe('_updateDefaultGroupWithNewData()', function() {
        var group;
        var recordData;
        var elAttrSpy;
        var trAttrSpy;

        beforeEach(function() {
            elAttrSpy = sinon.collection.spy();
            trAttrSpy = sinon.collection.spy();
            group = {
                model: app.data.createBean('ProductBundle'),
                $el: {
                    attr: elAttrSpy
                },
                $: function() {
                    return {
                        attr: trAttrSpy
                    };
                }
            };
            recordData = {
                id: 'newId1',
                test: 'abc'
            };
            layout.groupIds = ['oldGroupId1'];
            layout.defaultGroupId = 'oldGroupId1';
            layout._updateDefaultGroupWithNewData(group, recordData);
        });

        afterEach(function() {
            group = null;
            recordData = null;
            elAttrSpy = null;
            trAttrSpy = null;
        });

        it('should update groupIds with the new group ID', function() {
            expect(layout.groupIds).toEqual([group.model.cid]);
        });

        it('should set the defaultGroupId to be the new group ID', function() {
            expect(layout.defaultGroupId).toBe(group.model.cid);
        });

        it('should set recordData on the model', function() {
            expect(group.model.get('id')).toBe('newId1');
            expect(group.model.get('test')).toBe('abc');
        });

        it('should set the group groupId to be the new group ID', function() {
            expect(group.groupId).toBe(group.model.cid);
        });

        it('should call attr function on $el to update the group ID', function() {
            expect(elAttrSpy).toHaveBeenCalled();
        });

        it('should call attr function on any tr elements to update the group ID', function() {
            expect(trAttrSpy).toHaveBeenCalled();
        });
    });

    describe('_saveDefaultGroupThenCallBulk', function() {
        var oldGroup;
        var newGroup;
        var newGroupId;
        var newGroupModel;
        var bulkSaveRequests;
        var layoutModelId;
        var appApiCallArgs;

        beforeEach(function() {
            oldGroup = {};
            newGroupModel = app.data.createBean('ProductBundles', {
                name: 'New Test Group'
            });
            newGroup = {
                model: newGroupModel
            };
            bulkSaveRequests = [];
            layoutModelId = 'layoutModelId1';

            sinon.collection.stub(app.api, 'relationships', function() {});

            layout.model.set('id', layoutModelId);
            layout._saveDefaultGroupThenCallBulk(oldGroup, newGroup, bulkSaveRequests);
            appApiCallArgs = app.api.relationships.args[0];
        });

        afterEach(function() {
            oldGroup = null;
            newGroup = null;
            newGroupModel = null;
            bulkSaveRequests = null;
            layoutModelId = null;
            appApiCallArgs = null;
        });

        it('should call app.api.relationships with method "create"', function() {
            expect(appApiCallArgs[0]).toBe('create');
        });

        it('should call app.api.relationships with module "Quotes"', function() {
            expect(appApiCallArgs[1]).toBe('Quotes');
        });

        describe('with proper payload', function() {
            it('should use layout model id', function() {
                expect(appApiCallArgs[2].id).toBe(layoutModelId);
            });

            it('should use product_bundles as link name', function() {
                expect(appApiCallArgs[2].link).toBe('product_bundles');
            });

            it('should have related object', function() {
                expect(appApiCallArgs[2].related.position).toBe(0);
                expect(appApiCallArgs[2].related.name).toBe('New Test Group');
            });
        });
    });

    describe('_onDefaultGroupSaveSuccess', function() {
        var oldGroup;
        var newGroup;
        var newGroupModel;
        var bulkSaveRequests;
        var newGroupOldId;
        var serverData;
        var newGroupSavedId;

        beforeEach(function() {
            oldGroup = {};
            newGroupModel = app.data.createBean('ProductBundles', {
                id: ''
            });
            newGroup = {
                model: newGroupModel,
                groupId: newGroupOldId
            };
            newGroupOldId = 'oldGroupId1';
            newGroupSavedId = 'savedGroupId2';
            serverData = {
                related_record: {
                    id: newGroupSavedId,
                    test: true
                }
            };
            bulkSaveRequests = [{
                url: 'v10/ProductBundles/' + newGroupOldId + '/stuff'
            }];
            sinon.collection.stub(layout, '_callBulkRequests', function() {});
            sinon.collection.stub(layout, '_updateDefaultGroupWithNewData', function() {});

            layout._onDefaultGroupSaveSuccess(oldGroup, newGroup, bulkSaveRequests, newGroupOldId, serverData);
        });

        afterEach(function() {
            oldGroup = null;
            newGroup = null;
            bulkSaveRequests = null;
            newGroupOldId = null;
            newGroupSavedId = null;
            serverData = null;
        });

        it('should update old group id in requests to new group id', function() {
            expect(bulkSaveRequests[0].url).toBe('v10/ProductBundles/' + newGroupSavedId + '/stuff');
        });

        it('should call _updateDefaultGroupWithNewData to update the group', function() {
            expect(layout._updateDefaultGroupWithNewData).toHaveBeenCalled();
        });

        it('should call _callBulkRequests', function() {
            expect(layout._callBulkRequests).toHaveBeenCalled();
        });
    });

    describe('_callBulkRequests', function() {
        beforeEach(function() {
            layout.currentBulkSaveRequests = [{
                url: 'testUrl'
            }];
            sinon.collection.stub(app.api, 'call', function() {
                layout.currentBulkSaveRequests = null;
            });

            layout._callBulkRequests();
        });

        it('should call with method create', function() {
            expect(app.api.call.args[0][0]).toBe('create');
        });

        it('should call with bulk url', function() {
            var url = app.api.call.args[0][1].split('/');
            expect(_.last(url)).toBe('bulk');
        });

        it('should call with requests array', function() {
            expect(app.api.call.args[0][2].requests).toBeDefined();
        });

        it('should have correct requests length', function() {
            expect(app.api.call.args[0][2].requests.length).toBe(1);
        });

        it('should empty currentBulkSaveRequests', function() {
            expect(layout.currentBulkSaveRequests.length).toBe(0);
        });
    });

    describe('_onSaveUpdatedGroupSuccess()', function() {
        var oldGroup;
        var oldGroupId;
        var oldGroupModel;
        var oldGroupTriggerSpy;
        var newGroup;
        var newGroupId;
        var newGroupModel;
        var newGroupTriggerSpy;
        var bulkResponses;
        var bulkOldModelUpdate;
        var bulkNewModelUpdate;
        var oldProductGroupModel;
        var bundleItem1;
        var bundleItem2;
        var bundles;

        beforeEach(function() {
            oldGroupTriggerSpy = sinon.collection.spy();
            newGroupTriggerSpy = sinon.collection.spy();

            bundles = {
                remove: $.noop
            };

            bundleItem1 = app.data.createBean('Products', {
                id: 'bundleItem1'
            });
            bundleItem2 = app.data.createBean('Products', {
                id: 'bundleItem2'
            });
            oldProductGroupModel = app.data.createBean('ProductBundles', {
                id: 'oldProductGroupModelId',
                product_bundle_items: new Backbone.Collection(bundleItem1)
            });
            sinon.collection.spy(oldProductGroupModel, 'setSyncedAttributes');

            oldGroupId = 'oldGroupId1';
            oldGroupModel = app.data.createBean('Products', {
                id: 'oldGroupModelId1',
                name: 'oldGroupModelName_original',
                position: 0
            });
            sinon.collection.spy(oldGroupModel, 'setSyncedAttributes');

            oldGroup = {
                groupId: oldGroupId,
                model: oldProductGroupModel,
                collection: app.data.createMixedBeanCollection(),
                trigger: oldGroupTriggerSpy,
                dispose: $.noop
            };

            newGroupId = 'newGroupId1';
            newGroupModel = app.data.createBean('ProductBundles', {
                id: 'newGroupModelId1',
                name: 'newGroupModelName_original',
                product_bundle_items: new Backbone.Collection(bundleItem2)
            });
            sinon.collection.spy(newGroupModel, 'setSyncedAttributes');
            newGroup = {
                groupId: newGroupId,
                collection: app.data.createMixedBeanCollection(),
                trigger: newGroupTriggerSpy
            };

            oldGroup.collection.add(oldGroupModel);
            newGroup.collection.add(newGroupModel);

            sinon.collection.stub(layout, '_getComponentByGroupId', function(id) {
                if (id === oldGroupId) {
                    return oldGroup;
                } else {
                    return newGroup;
                }
            });

            bulkOldModelUpdate = {
                contents: {
                    record: {
                        id: 'oldProductGroupModelId'
                    },
                    related_record: {
                        id: 'oldGroupModelId1',
                        name: 'oldGroupModelName_new',
                        position: 1
                    }
                }
            };
            bulkNewModelUpdate = {
                contents: {
                    record: {},
                    related_record: {
                        id: 'newGroupModelId1',
                        name: 'newGroupModelName_new',
                        position: 1
                    }
                }
            };
            layout.currentBulkSaveRequests = [
                bulkOldModelUpdate,
                bulkNewModelUpdate
            ];
            bulkResponses = [
                bulkOldModelUpdate,
                bulkNewModelUpdate
            ];
            layout.model.set('bundles', bundles, {silent: true});
        });

        afterEach(function() {
            oldGroupModel.dispose();
            oldGroupModel = null;
            newGroupModel.dispose();
            newGroupModel = null;
            oldGroupId = null;
            newGroupId = null;
            oldGroup = null;
            newGroup = null;
            bulkOldModelUpdate = null;
            bulkNewModelUpdate = null;
            bulkResponses = null;
            bundles = null;
        });

        describe('when oldGroup is not sent', function() {
            beforeEach(function() {
                layout._onSaveUpdatedGroupSuccess(undefined, newGroup, bulkResponses);
            });

            it('should not trigger quotes:group:save:stop if oldGroup is not passed in', function() {
                expect(oldGroupTriggerSpy).not.toHaveBeenCalled();
            });

            it('should always trigger quotes:group:save:stop on newGroup', function() {
                expect(newGroupTriggerSpy).toHaveBeenCalled();
            });

            it('should not update old group', function() {
                expect(oldGroupModel.get('name')).toBe('oldGroupModelName_original');
                expect(oldGroupModel.setSyncedAttributes).not.toHaveBeenCalled();
                expect(oldProductGroupModel.setSyncedAttributes).not.toHaveBeenCalled();
            });

            it('should update the new group record position', function() {
                expect(newGroupModel.get('name')).toBe('newGroupModelName_original');
                expect(newGroupModel.get('position')).toBe(1);
                expect(newGroupModel.setSyncedAttributes).toHaveBeenCalled();
            });
        });

        describe('when oldGroup is sent', function() {
            beforeEach(function() {
                layout._onSaveUpdatedGroupSuccess(oldGroup, newGroup, bulkResponses);
            });

            it('should trigger quotes:group:save:stop on oldGroup', function() {
                expect(oldGroupTriggerSpy).toHaveBeenCalled();
            });

            it('should always trigger quotes:group:save:stop on newGroup', function() {
                expect(newGroupTriggerSpy).toHaveBeenCalled();
            });

            it('should update the old group position', function() {
                expect(oldGroupModel.get('name')).toBe('oldGroupModelName_original');
                expect(oldGroupModel.get('position')).toBe(1);
                expect(oldGroupModel.setSyncedAttributes).toHaveBeenCalled();
                expect(oldProductGroupModel.setSyncedAttributes).toHaveBeenCalled();
            });

            it('should update the new group record position', function() {
                expect(newGroupModel.get('name')).toBe('newGroupModelName_original');
                expect(newGroupModel.get('position')).toBe(1);
                expect(newGroupModel.setSyncedAttributes).toHaveBeenCalled();
            });
        });

        describe('when deleted item exists', function() {
            beforeEach(function() {
                newGroup.model = newGroupModel;
                bulkResponses.push({
                    contents: {
                        id: oldGroupId
                    }
                });
                layout.groupIds = [oldGroupId, newGroupId];
                sinon.collection.stub(oldGroup, 'dispose', function() {
                    this.model.unset('product_bundle_items');
                });
                layout._onSaveUpdatedGroupSuccess(oldGroup, newGroup, bulkResponses);
            });

            it('should remove the deleted group id from groupIds', function() {
                expect(layout.groupIds).toEqual([newGroupId]);
            });

            it('should move the deleted group models to new group', function() {
                expect(newGroup.model.get('product_bundle_items').length).toBe(2);
            });

            it('should remove the deleted group models', function() {
                expect(oldGroup.model.get('product_bundle_items')).toBeUndefined();
            });

            it('should dispose the deleted group', function() {
                expect(oldGroup.dispose).toHaveBeenCalled();
            });

            it('should trigger quotes:line_nums:reset on newGroup', function() {
                expect(newGroupTriggerSpy).toHaveBeenCalledWith('quotes:line_nums:reset');
            });
        });
    });

    describe('_updateRowPositions()', function() {
        var dataGroup;
        var rowModel1;
        var rowModel2;
        var rowModel3;
        var rowModel4;
        var results;
        var callUrl;

        beforeEach(function() {
            dataGroup = {
                groupId: 'dataGroupId1',
                collection: new Backbone.Collection(),
                $: function() {
                    return [
                        '<tr name="Products_qliId1"></tr>',
                        '<tr name="ProductBundleNotes_pbnId2"></tr>',
                        '<tr name="Products_qliId3"></tr>',
                        '<tr name="ProductBundleNotes_pbnId4"></tr>'
                    ];
                }
            };

            dataGroup.collection.comparator = function(model) {
                return model.get('position');
            };

            sinon.collection.spy(dataGroup.collection, 'sort');

            rowModel1 = new Backbone.Model({
                id: 'qliId1',
                module: 'Products',
                position: -1
            });
            rowModel2 = new Backbone.Model({
                id: 'pbnId2',
                module: 'ProductBundleNotes',
                position: -1
            });
            rowModel3 = new Backbone.Model({
                id: 'qliId3',
                module: 'Products',
                position: -1
            });
            rowModel4 = new Backbone.Model({
                id: 'pbnId4',
                module: 'ProductBundleNotes',
                position: -1
            });
        });

        afterEach(function() {
            dataGroup = null;
            rowModel1 = null;
            rowModel2 = null;
            rowModel3 = null;
            rowModel4 = null;
            results = null;
            callUrl = null;
        });

        describe('full row position updates', function() {
            beforeEach(function() {
                dataGroup.collection.add(rowModel1);
                dataGroup.collection.add(rowModel2);
                dataGroup.collection.add(rowModel3);
                dataGroup.collection.add(rowModel4);

                results = layout._updateRowPositions(dataGroup);
            });

            it('should call collection.sort on the group', function() {
                expect(dataGroup.collection.sort).toHaveBeenCalled();
            });

            describe('first call', function() {
                it('should be called with method PUT', function() {
                    expect(results[0].method).toBe('PUT');
                });

                it('should be called with data position 0', function() {
                    expect(results[0].data.position).toBe(0);
                });

                it('should have properly formatted endpoint url', function() {
                    callUrl = results[0].url.split('/');
                    callUrl = _.without(callUrl, '.', '..', 'rest', 'v10');

                    expect(callUrl[0]).toBe('ProductBundles');
                    expect(callUrl[1]).toBe('dataGroupId1');
                    expect(callUrl[2]).toBe('link');
                    expect(callUrl[3]).toBe('products');
                    expect(callUrl[4]).toBe('qliId1');
                });
            });

            describe('second call', function() {
                it('should be called with method PUT', function() {
                    expect(results[1].method).toBe('PUT');
                });

                it('should be called with data position 1', function() {
                    expect(results[1].data.position).toBe(1);
                });

                it('should have properly formatted endpoint url', function() {
                    callUrl = results[1].url.split('/');
                    callUrl = _.without(callUrl, '.', '..', 'rest', 'v10');

                    expect(callUrl[0]).toBe('ProductBundles');
                    expect(callUrl[1]).toBe('dataGroupId1');
                    expect(callUrl[2]).toBe('link');
                    expect(callUrl[3]).toBe('product_bundle_notes');
                    expect(callUrl[4]).toBe('pbnId2');
                });
            });

            describe('third call', function() {
                it('should be called with method PUT', function() {
                    expect(results[2].method).toBe('PUT');
                });

                it('should be called with data position 0', function() {
                    expect(results[2].data.position).toBe(2);
                });

                it('should have properly formatted endpoint url', function() {
                    callUrl = results[2].url.split('/');
                    callUrl = _.without(callUrl, '.', '..', 'rest', 'v10');

                    expect(callUrl[0]).toBe('ProductBundles');
                    expect(callUrl[1]).toBe('dataGroupId1');
                    expect(callUrl[2]).toBe('link');
                    expect(callUrl[3]).toBe('products');
                    expect(callUrl[4]).toBe('qliId3');
                });
            });

            describe('fourth call', function() {
                it('should be called with method PUT', function() {
                    expect(results[3].method).toBe('PUT');
                });

                it('should be called with data position 3', function() {
                    expect(results[3].data.position).toBe(3);
                });

                it('should have properly formatted endpoint url', function() {
                    callUrl = results[3].url.split('/');
                    callUrl = _.without(callUrl, '.', '..', 'rest', 'v10');

                    expect(callUrl[0]).toBe('ProductBundles');
                    expect(callUrl[1]).toBe('dataGroupId1');
                    expect(callUrl[2]).toBe('link');
                    expect(callUrl[3]).toBe('product_bundle_notes');
                    expect(callUrl[4]).toBe('pbnId4');
                });
            });
        });

        describe('swap two row positions', function() {
            beforeEach(function() {
                rowModel1.set('position', 0);
                // switched rowModel2 and rowModel3's position
                rowModel2.set('position', 2);
                rowModel3.set('position', 1);
                rowModel4.set('position', 3);

                dataGroup.collection.add(rowModel1);
                dataGroup.collection.add(rowModel2);
                dataGroup.collection.add(rowModel3);
                dataGroup.collection.add(rowModel4);

                results = layout._updateRowPositions(dataGroup);
            });

            describe('first call', function() {
                it('should be called with method PUT', function() {
                    expect(results[0].method).toBe('PUT');
                });

                it('should be called with data position 0', function() {
                    expect(results[0].data.position).toBe(1);
                });

                it('should have properly formatted endpoint url', function() {
                    callUrl = results[0].url.split('/');
                    callUrl = _.without(callUrl, '.', '..', 'rest', 'v10');

                    expect(callUrl[0]).toBe('ProductBundles');
                    expect(callUrl[1]).toBe('dataGroupId1');
                    expect(callUrl[2]).toBe('link');
                    expect(callUrl[3]).toBe('product_bundle_notes');
                    expect(callUrl[4]).toBe('pbnId2');
                });
            });

            describe('second call', function() {
                it('should be called with method PUT', function() {
                    expect(results[1].method).toBe('PUT');
                });

                it('should be called with data position 2', function() {
                    expect(results[1].data.position).toBe(2);
                });

                it('should have properly formatted endpoint url', function() {
                    callUrl = results[1].url.split('/');
                    callUrl = _.without(callUrl, '.', '..', 'rest', 'v10');

                    expect(callUrl[0]).toBe('ProductBundles');
                    expect(callUrl[1]).toBe('dataGroupId1');
                    expect(callUrl[2]).toBe('link');
                    expect(callUrl[3]).toBe('products');
                    expect(callUrl[4]).toBe('qliId3');
                });
            });
        });
    });

    describe('_getComponentByGroupId()', function() {
        var group;
        var result;
        beforeEach(function() {
            group = {
                name: 'quote-data-group',
                groupId: 'groupId1'
            };
            layout._components = [group];
        });

        afterEach(function() {
            group = null;
            result = null;
            layout._components = [];
        });

        it('should return the group if given a correct id', function() {
            result = layout._getComponentByGroupId('groupId1');
            expect(result).toBe(group);
        });

        it('should return undefined if group is not found', function() {
            result = layout._getComponentByGroupId('groupId2');
            expect(result).toBe(undefined);
        });
    });

    describe('_onGroupDragTriggerOver()', function() {
        var evtParam;
        var uiParam;
        var group;
        var groupTriggerSpy;
        beforeEach(function() {
            evtParam = {
                target: '<div data-group-id="groupId1"></div>'
            };
            uiParam = {};
            groupTriggerSpy = sinon.collection.spy();
            group = {
                trigger: groupTriggerSpy
            };
        });

        afterEach(function() {
            evtParam = null;
            uiParam = null;
            groupTriggerSpy = null;
            group = null;
        });

        it('should trigger quotes:sortable:over on the group', function() {
            sinon.collection.stub(layout, '_getComponentByGroupId', function() {
                return group;
            });
            layout._onGroupDragTriggerOver(evtParam, uiParam);

            expect(groupTriggerSpy).toHaveBeenCalledWith('quotes:sortable:over');
        });

        it('should not trigger quotes:sortable:over if no group is found', function() {
            sinon.collection.stub(layout, '_getComponentByGroupId', function() {
                return undefined;
            });
            layout._onGroupDragTriggerOver(evtParam, uiParam);

            expect(groupTriggerSpy).not.toHaveBeenCalled();
        });
    });

    describe('_onGroupDragTriggerOut()', function() {
        var evtParam;
        var uiParam;
        var group;
        var groupTriggerSpy;
        beforeEach(function() {
            evtParam = {
                target: '<div data-group-id="groupId1"></div>'
            };
            uiParam = {};
            groupTriggerSpy = sinon.collection.spy();
            group = {
                trigger: groupTriggerSpy
            };
        });

        afterEach(function() {
            evtParam = null;
            uiParam = null;
            groupTriggerSpy = null;
            group = null;
        });

        it('should trigger quotes:sortable:over on the group', function() {
            sinon.collection.stub(layout, '_getComponentByGroupId', function() {
                return group;
            });
            layout._onGroupDragTriggerOut(evtParam, uiParam);

            expect(groupTriggerSpy).toHaveBeenCalledWith('quotes:sortable:out');
        });

        it('should not trigger quotes:sortable:over if no group is found', function() {
            sinon.collection.stub(layout, '_getComponentByGroupId', function() {
                return undefined;
            });
            layout._onGroupDragTriggerOut(evtParam, uiParam);

            expect(groupTriggerSpy).not.toHaveBeenCalled();
        });
    });

    describe('beforeRender()', function() {
        beforeEach(function() {
            sinon.collection.stub(layout, '$', function() {
                return [
                    '<div class="ui-sortable"></div>'
                ];
            });
            sinon.collection.stub($.fn, 'sortable', function() {});
            layout.beforeRender();
        });

        it('should call sortable("destoy") on groups with sortable', function() {
            expect($.fn.sortable).toHaveBeenCalledWith('destroy');
        });
    });

    describe('_onProductBundleChange()', function() {
        var quoteData;
        var defaultBundle;
        var bundle1;
        var bundle2;
        var bundle3;
        beforeEach(function() {
            defaultBundle = app.data.createBean('ProductBundles', {
                id: 'defaultId1',
                name: 'defaultName1',
                default_group: true
            });
            bundle1 = app.data.createBean('ProductBundles', {
                id: 'testId1',
                name: 'testName1'
            });
            bundle2 = app.data.createBean('ProductBundles', {
                id: 'testId2',
                name: 'testName2'
            });
            bundle3 = app.data.createBean('ProductBundles', {
                id: 'testId3',
                name: 'testName3'
            });
            quoteData = app.data.createBeanCollection('ProductBundles', [
                defaultBundle,
                bundle1,
                bundle2
            ]);

            sinon.collection.spy(layout, '_addQuoteGroupToLayout');
            sinon.collection.spy(layout, 'render');
            layout.model.set('bundles', quoteData);
        });

        it('should set this.groupIds with quoteData record IDs', function() {
            expect(layout.groupIds).toEqual([defaultBundle.cid, bundle1.cid, bundle2.cid]);
        });

        it('should call _addQuoteGroupToLayout with new bundle 1 data', function() {
            expect(layout._addQuoteGroupToLayout).toHaveBeenCalledWith(bundle1);
        });

        it('should call _addQuoteGroupToLayout with new bundle 2 data', function() {
            expect(layout._addQuoteGroupToLayout).toHaveBeenCalledWith(bundle2);
        });

        it('should call _addQuoteGroupToLayout with only bundle 3 data', function() {
            // unset the spy to reset the called with list
            layout._addQuoteGroupToLayout.restore();
            sinon.collection.spy(layout, '_addQuoteGroupToLayout');

            quoteData = app.data.createBeanCollection('ProductBundles', [
                defaultBundle,
                bundle1,
                bundle2,
                bundle3
            ]);

            layout.model.set('bundles', quoteData);
            expect(layout._addQuoteGroupToLayout).not.toHaveBeenCalledWith(defaultBundle);
            expect(layout._addQuoteGroupToLayout).not.toHaveBeenCalledWith(bundle1);
            expect(layout._addQuoteGroupToLayout).not.toHaveBeenCalledWith(bundle2);
            expect(layout._addQuoteGroupToLayout).toHaveBeenCalledWith(bundle3);
        });

        it('should call render on quote_data change', function() {
            expect(layout.render).toHaveBeenCalled();
        });
    });

    describe('_addQuoteGroupToLayout()', function() {
        var initComponentsSpy;
        beforeEach(function() {
            initComponentsSpy = sinon.collection.spy();
            sinon.collection.spy(layout.context, 'getChildContext');
            sinon.collection.stub(app.view, 'createLayout', function() {
                return {
                    initComponents: initComponentsSpy
                };
            });
            sinon.collection.stub(layout, 'addComponent', function() {});

            layout._addQuoteGroupToLayout({});
        });

        it('should call layout.context.getChildContext', function() {
            expect(layout.context.getChildContext).toHaveBeenCalled({module: 'ProductBundles'});
        });

        it('should call app.view.createLayout', function() {
            expect(app.view.createLayout).toHaveBeenCalled();
        });

        it('should call group layout initComponents', function() {
            expect(initComponentsSpy).toHaveBeenCalled();
        });

        it('should call layout.addComponent', function() {
            expect(layout.addComponent).toHaveBeenCalled();
        });
    });

    describe('_onCreateQuoteGroup()', function() {
        var callArgs;
        var bundles;
        beforeEach(function() {
            bundles = new Backbone.Collection([{
                id: 'testId1',
                position: 0
            }, {
                id: 'testId2',
                position: 1
            }]);
            layout.model.set('id', 'testQuoteLayoutId');
            layout.model.set('bundles', bundles);
            layout.model.set({
                currency_id: 'currency_id_1',
                base_rate: '50.37'
            });

            sinon.collection.stub(app.alert, 'show', function() {});
            sinon.collection.stub(app.api, 'relationships', function() {});
            layout._onCreateQuoteGroup();
            callArgs = app.api.relationships.firstCall;
        });

        afterEach(function() {
            callArgs = null;
        });

        it('should call app.alert.show', function() {
            expect(app.alert.show).toHaveBeenCalled();
        });

        it('should call app.api.relationships', function() {
            expect(app.api.relationships).toHaveBeenCalled();
        });

        it('should call app.api.relationships with method create', function() {
            expect(callArgs.args[0]).toBe('create');
        });

        it('should call app.api.relationships with module Quotes', function() {
            expect(callArgs.args[1]).toBe('Quotes');
        });

        it('should call app.api.relationships with proper link payload Quote ID', function() {
            expect(callArgs.args[2].id).toBe('testQuoteLayoutId');
        });

        it('should call app.api.relationships with proper link payload link name', function() {
            expect(callArgs.args[2].link).toBe('product_bundles');
        });

        it('should call app.api.relationships with proper link payload position', function() {
            expect(callArgs.args[2].related.position).toBe(2);
        });

        it('should call app.api.relationships with proper link payload currency', function() {
            expect(callArgs.args[2].related.currency_id).toBe('currency_id_1');
            expect(callArgs.args[2].related.base_rate).toBe('50.37');
        });

        it('should call app.api.relationships with payload position of 1 when no bundles exist', function() {
            // because default group is 0, any new create could get a position of 1
            layout.model.set('bundles', new Backbone.Collection());
            layout._onCreateQuoteGroup();

            callArgs = app.api.relationships.lastCall;

            expect(callArgs.args[2].related.position).toBe(1);
        });
    });

    describe('_onCreateQuoteGroupSuccess()', function() {
        var newBundleData;
        var bundles;
        beforeEach(function() {
            bundles = new Backbone.Collection([{
                id: 'testId1',
                position: 0
            }, {
                id: 'testId2',
                position: 1
            }]);
            layout.model.set('bundles', bundles);
            newBundleData = {
                related_record: {
                    id: 'testId3',
                    position: 2,
                    product_bundle_items: []
                }
            };
            sinon.collection.stub(app.alert, 'dismiss', function() {});
            sinon.collection.stub(app.alert, 'show', function() {});
            sinon.collection.stub(layout, '_addQuoteGroupToLayout', function() {});
            sinon.collection.stub(layout, 'render', function() {});
            sinon.collection.stub(layout.context, 'trigger', function() {});
            sinon.collection.spy(bundles, 'add');
        });

        afterEach(function() {
            newBundleData = null;
        });

        describe('regardless of show_line_nums', function() {
            beforeEach(function() {
                layout._onCreateQuoteGroupSuccess(newBundleData);
            });

            it('should call app.alert.dismiss to get rid of old alert', function() {
                expect(app.alert.dismiss).toHaveBeenCalledWith('adding_bundle_alert');
            });

            it('should call app.alert.show to display new alert', function() {
                expect(app.alert.show).toHaveBeenCalledWith('added_bundle_alert');
            });

            it('should add the newBundleData to records', function() {
                expect(layout.model.get('bundles').at(3).get('id')).toBe('testId3');
            });

            it('should call add on bundles collection', function() {
                expect(bundles.add).toHaveBeenCalledWith(newBundleData.related_record);
            });

            it('should trigger quotes:group:create:success and send newBundleData', function() {
                expect(layout.context.trigger).toHaveBeenCalledWith('quotes:group:create:success', newBundleData);
            });
        });

        describe('with show_line_nums', function() {
            it('should call context.trigger if show_line_nums is true', function() {
                layout.model.set('show_line_nums', true);
                layout._onCreateQuoteGroupSuccess(newBundleData);

                expect(layout.context.trigger).toHaveBeenCalledWith('quotes:show_line_nums:changed', true);
            });

            it('should not call context.trigger if show_line_nums is false', function() {
                layout.model.set('show_line_nums', false);
                layout._onCreateQuoteGroupSuccess(newBundleData);

                expect(layout.context.trigger).not.toHaveBeenCalledWith('quotes:show_line_nums:changed', true);
            });
        });
    });

    describe('_onCreateQuoteGroupSuccess()', function() {
        var newBundleData;
        var layoutModel;
        var bundlesCollection;

        beforeEach(function() {
            bundlesCollection = new Backbone.Collection({
                id: 'recordId1'
            });
            layoutModel = app.data.createBean('Quotes', {
                bundles: bundlesCollection
            });
            layout.model = layoutModel;
            newBundleData = {
                record: {
                    id: 'recordId1'
                },
                related_record: {
                    id: 'relatedId1'
                }
            };
            sinon.collection.stub(app.alert, 'dismiss', function() {});
            sinon.collection.stub(app.alert, 'show', function() {});
            sinon.collection.stub(layout.context, 'trigger', function() {});
        });

        afterEach(function() {
            newBundleData = null;
            bundlesCollection = null;
            layoutModel.dispose();
            layoutModel = null;
        });

        describe('general behavior', function() {
            beforeEach(function() {
                layout.model.set('show_line_nums', false);

                layout._onCreateQuoteGroupSuccess(newBundleData);
            });

            it('should call app.alert.dismiss on the adding_bundle_alert', function() {
                expect(app.alert.dismiss).toHaveBeenCalledWith('adding_bundle_alert');
            });

            it('should call app.alert.show on the added_bundle_alert', function() {
                expect(app.alert.show).toHaveBeenCalledWith('added_bundle_alert');
            });

            it('should set product_bundle_items to empty array when no product_bundle_items exist', function() {
                expect(newBundleData.related_record.product_bundle_items).toEqual([]);
            });

            it('should set related_record._justSaved = true', function() {
                expect(newBundleData.related_record._justSaved).toBeTruthy();
            });

            it('should not trigger quotes:show_line_nums:changed on layout.context', function() {
                expect(layout.context.trigger).not.toHaveBeenCalledWith('quotes:show_line_nums:changed', true);
            });

            it('should call app.alert.show on the added_bundle_alert', function() {
                expect(layout.context.trigger).toHaveBeenCalledWith('quotes:group:create:success');
            });
        });

        describe('when show_line_nums is true', function() {
            beforeEach(function() {
                layout.model.set('show_line_nums', true);

                layout._onCreateQuoteGroupSuccess(newBundleData);
            });

            it('should trigger quotes:show_line_nums:changed on layout.context', function() {
                expect(layout.context.trigger).toHaveBeenCalledWith('quotes:show_line_nums:changed', true);
            });
        });
    });

    describe('_onDeleteSelectedItems()', function() {
        var massCollection;
        var groupLayout;
        var groupLayoutId;
        var groupList;
        var rowModel1;
        var rowModel2;
        var request;

        beforeEach(function() {
            massCollection = new Backbone.Collection();

            rowModel1 = app.data.createBean('Products', {
                id: 'productId1'
            });
            rowModel1.module = 'Products';
            rowModel1.link = {
                bean: {
                    id: 'layoutId1'
                }
            };

            rowModel2 = app.data.createBean('Products', {
                id: 'productId2'
            });
            rowModel2.module = 'Products';
            rowModel2.link = {
                bean: {
                    id: 'layoutId1'
                }
            };

            massCollection.add(rowModel1);
            massCollection.add(rowModel2);

            groupList = {
                toggledModels: {
                    productId1: rowModel1
                }
            };
            groupLayoutId = 'layoutId1';
            groupLayout = {
                groupId: groupLayoutId,
                collection: app.data.createMixedBeanCollection(),
                getGroupListComponent: function() {
                    return groupList;
                },
                dispose: $.noop
            };

            sinon.collection.stub(layout, '_getComponentByGroupId', function() {
                return groupLayout;
            });
            sinon.collection.stub(app.api, 'buildURL', function(path) {
                return '.../' + path;
            });
            sinon.collection.stub(layout, '_callBulkRequests', function() {});

            layout._onDeleteSelectedItems(massCollection);
        });

        afterEach(function() {
            massCollection = null;
            groupLayout = null;
            groupLayoutId = null;
            groupList = null;
            rowModel1 = null;
            rowModel2 = null;
            request = null;
        });

        it('should have 3 bulk requests ready', function() {
            expect(layout.currentBulkSaveRequests.length).toBe(3);
        });

        describe('first request', function() {
            beforeEach(function() {
                request = layout.currentBulkSaveRequests[0];
            });

            it('should have url Products/productId1', function() {
                expect(request.url).toBe('Products/productId1');
            });

            it('should have method = DELETE', function() {
                expect(request.method).toBe('DELETE');
            });
        });

        describe('second request', function() {
            beforeEach(function() {
                request = layout.currentBulkSaveRequests[1];
            });

            it('should have url Products/productId1', function() {
                expect(request.url).toBe('Products/productId2');
            });

            it('should have method = DELETE', function() {
                expect(request.method).toBe('DELETE');
            });
        });

        describe('third request', function() {
            beforeEach(function() {
                request = layout.currentBulkSaveRequests[2];
            });

            it('should have url Products/productId1', function() {
                expect(request.url).toBe('ProductBundles/layoutId1');
            });

            it('should have method = DELETE', function() {
                expect(request.method).toBe('GET');
            });
        });
    });

    describe('_onDeleteSelectedItemsSuccess()', function() {
        var massCollection;
        var bulkRequests;
        var rowModel;
        var groupLayoutId;
        var groupLayout;
        var groupLayoutCollection;
        var groupLayoutModel;

        beforeEach(function() {
            massCollection = new Backbone.Collection();
            groupLayoutCollection = new Backbone.Collection();
            rowModel = app.data.createBean('Products', {
                id: 'productId1'
            });
            rowModel.module = 'Products';
            rowModel.link = {
                bean: {
                    id: 'layoutId1'
                }
            };
            massCollection.add(rowModel);
            groupLayoutCollection.add(rowModel);

            groupLayoutModel = app.data.createBean('ProductBundles', {
                id: 'layoutId1',
                total: '100'
            });
            bulkRequests = [];

            groupLayoutId = 'layoutId1';
            groupLayout = {
                groupId: groupLayoutId,
                collection: groupLayoutCollection,
                model: groupLayoutModel,
                getGroupListComponent: function() {
                    return groupList;
                },
                trigger: sinon.collection.spy(),
                dispose: $.noop
            };

            sinon.collection.stub(app.alert, 'dismiss', function() {});
            sinon.collection.stub(app.alert, 'show', function() {});
            sinon.collection.stub(layout, '_getComponentByGroupId', function() {
                return groupLayout;
            });
        });

        afterEach(function() {
            massCollection = null;
            bulkRequests = null;
            rowModel = null;
            groupLayoutId = null;
            groupLayout = null;
            groupLayoutCollection = null;
            groupLayoutModel = null;
        });

        describe('general behavior', function() {
            beforeEach(function() {
                layout._onDeleteSelectedItemsSuccess(massCollection, bulkRequests);
            });

            it('should call app.alert.dismiss deleting_line_item', function() {
                expect(app.alert.dismiss).toHaveBeenCalledWith('deleting_line_item');
            });

            it('should call app.alert.show deleted_line_item', function() {
                expect(app.alert.show).toHaveBeenCalledWith('deleted_line_item');
            });
        });

        describe('when request is a record delete', function() {
            beforeEach(function() {
                bulkRequests = [{
                    contents: {
                        id: 'productId1'
                    }
                }];

                layout._onDeleteSelectedItemsSuccess(massCollection, bulkRequests);
            });

            it('should remove rowModel from the group layout collection', function() {
                expect(groupLayout.collection.length).toBe(0);
            });

            it('should remove rowModel from massCollection', function() {
                expect(massCollection.length).toBe(0);
            });
        });

        describe('when request is a group update', function() {
            beforeEach(function() {
                bulkRequests = [{
                    contents: {
                        id: 'layoutId1',
                        total: '0.00'
                    }
                }];
                sinon.collection.stub(layout, '_updateModelWithRecord');

                layout._onDeleteSelectedItemsSuccess(massCollection, bulkRequests);
            });

            it('should call _updateModelWithRecord and update the layout model', function() {
                expect(layout._updateModelWithRecord).toHaveBeenCalled();
            });

            it('should call trigger quotes:line_nums:reset on the layout', function() {
                expect(groupLayout.trigger).toHaveBeenCalledWith('quotes:line_nums:reset');
            });
        });
    });

    describe('_onDeleteQuoteGroupConfirm()', function() {
        var groupId;
        var groupName;
        var groupModel;
        var groupToDelete;
        var defaultGroupId;
        var defaultGroup;
        var defaultGroupModel;
        var bundleItem1;

        beforeEach(function() {
            bundleItem1 = app.data.createBean('Products', {
                id: 'bundleItemId1',
                position: 0
            });
            defaultGroupId = 'defaultGroupId1';
            defaultGroupModel = app.data.createBean('ProductBundles', {
                id: defaultGroupId,
                product_bundle_items: new Backbone.Collection()
            });
            defaultGroup = {
                id: defaultGroupId,
                model: defaultGroupModel
            };
            groupId = 'testId1';
            groupName = 'testName1';
            groupModel = new Backbone.Model({
                id: groupId,
                name: groupName,
                product_bundle_items: new Backbone.Collection(bundleItem1)
            });
            groupToDelete = {
                id: groupId,
                name: groupName,
                model: groupModel
            };
            sinon.collection.stub(layout, '_getComponentByGroupId', function() {
                return defaultGroup;
            });
            layout.defaultGroupId = defaultGroupId;
            layout.model.set('id', 'testQuoteLayoutId');
            layout.model.set('bundles', new Backbone.Collection(groupModel));
            sinon.collection.stub(app.alert, 'show', function() {});
            sinon.collection.stub(layout, '_saveDefaultGroupThenCallBulk', function() {});
            sinon.collection.stub(layout, '_callBulkRequests', function() {});
        });

        afterEach(function() {
            groupId = null;
            groupName = null;
            groupModel = null;
            groupToDelete = null;
            defaultGroupId = null;
            defaultGroup = null;
            defaultGroupModel = null;
            bundleItem1 = null;
        });

        it('should call app.alert.show', function() {
            layout._onDeleteQuoteGroupConfirm(groupId, groupName, groupToDelete);

            expect(app.alert.show).toHaveBeenCalled();
        });

        it('should call _saveDefaultGroupThenCallBulk if default group is not saved', function() {
            sinon.collection.stub(defaultGroupModel, 'isNew', function() { return true; });
            layout._onDeleteQuoteGroupConfirm(groupId, groupName, groupToDelete);

            expect(layout._saveDefaultGroupThenCallBulk).toHaveBeenCalled();
        });

        it('should call _callBulkRequests if default group is already saved', function() {
            layout._onDeleteQuoteGroupConfirm(groupId, groupName, groupToDelete);

            expect(layout._callBulkRequests).toHaveBeenCalled();
        });
    });

    describe('_dispose()', function() {
        beforeEach(function() {
            sinon.collection.stub(layout, 'beforeRender', function() {});
        });

        it('should call beforeRender', function() {
            layout._dispose();

            expect(layout.beforeRender).toHaveBeenCalled();
        });
    });
});
