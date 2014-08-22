/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
/**
 * SugarCRM error handlers.
 */
(function(app) {
    app.error = _.extend(app.error);

    function backToLogin(bDismiss) {
        if(bDismiss) app.alert.dismissAll();
        app.router.login();
    }

    function showErrorPage(status, dismiss) {
        if(dismiss) {
            app.alert.dismissAll();
        }

        app.controller.loadView({
           layout: "error",
           errorType: status,
           module: "Error",
           create: true
       });
    }

    function alertUser(key,title,msg) {
        app.alert.show(key, {
            level: 'error',
            messages: app.lang.get(msg),
            title: app.lang.get(title)
        });
    }
    
    /**
     * This is caused by attempt to login with invalid creds. 
     */
    app.error.handleNeedLoginError = function(error) {
        backToLogin(true);
        // Login can fail for many reasons such as lock out, bad credentials, etc.  Server message to provides details.
        alertUser("needs_login_error" , "LBL_INVALID_CREDS_TITLE", error.message);
    };

    /**
     * This is caused by expired or invalid token. 
     */
    app.error.handleInvalidGrantError = function(error) {
        backToLogin(true);
        alertUser("invalid_grant_error", "LBL_INVALID_GRANT_TITLE", "LBL_INVALID_GRANT");
    };

    /**
     * Client authentication handler. 
     */
    app.error.handleInvalidClientError = function(error) {
        backToLogin(true);
        alertUser("invalid_client_error","LBL_AUTH_FAILED_TITLE","LBL_AUTH_FAILED");
    };
    
    /**
     * Invalid request handler. 
     */
    app.error.handleInvalidRequestError = function(error) {
        backToLogin(true);
        alertUser("invalid_request_error", "LBL_INVALID_REQUEST_TITLE", "LBL_INVALID_REQUEST");
    };

    /**
     * 0 Timeout error handler. If server doesn't respond within timeout.
     */
    app.error.handleTimeoutError = function(error) {
        app.alert.dismissAll();
        alertUser("timeout_error", "LBL_REQUEST_TIMEOUT_TITLE", "LBL_REQUEST_TIMEOUT");
    };

    /**
     * 401 Unauthorized error handler. 
     */
    app.error.handleUnauthorizedError = function(error) {
        backToLogin(true);
        alertUser("unauthorized_request_error", "LBL_UNAUTHORIZED_TITLE", "LBL_UNAUTHORIZED");
    };

    /**
     * 403 Forbidden error handler. 
     */
    app.error.handleForbiddenError = function(error) {
        app.alert.dismissAll();
        // If portal is not configured, return to login screen if necessary
        if(error.code == "portal_not_configured"){
            backToLogin(true);
        }
        app.logger.error(app.lang.get(error.message ? error.message : "LBL_RESOURCE_UNAVAILABLE"));
    };
    
    /**
     * 404 Not Found handler.
     * If a model triggered the 404 but the model did not belong to the master layout,
     * this function will not handle that error.
     * Those errors should be handled by listeners on the model/collection and the views that
     * requested the data.
     */
    app.error.handleNotFoundError = function(error, model, options) {
        var layout = app.controller.layout || {};
        if ((options && options.context != layout.context)
            || (model && layout.context && layout.context.get("model") && layout.context.get("model") != model)
        ) {
            return;
        }
        if (!layout ||
            !_.isObject(layout.error) ||
            !_.isFunction(layout.error.handleNotFoundError) ||
            layout.error.handleNotFoundError(error, model, options) !== false
        ) {
            showErrorPage("404");
        }
    };

    /**
     * 405 Method not allowed handler.
     */
    app.error.handleMethodNotAllowedError = function(error) {
        backToLogin(true);
        alertUser("not_allowed_error", "LBL_METHOD_NOT_ALLOWED_TITLE", "LBL_METHOD_NOT_ALLOWED");
    };

    /**
     * 409 Handle conflict error.
     */
    app.error.handleMethodConflictError = function(error) {
        app.logger.error('Data conflict detected.');
    };

    /**
     * 422 Handle validation error
     */
    app.error.handleValidationError = function(error) {
        var layout = app.controller.layout;
        if( !_.isObject(layout.error) ||
            !_.isFunction(layout.error.handleValidationError) ||
            layout.error.handleValidationError(error) !== false
        ) {
            //Ignore errors triggered from models, they should be handled by the views.
            if (error instanceof app.data.beanModel) {
                return;
            }
            alertUser("validation_error", "LBL_PRECONDITION_MISSING_TITLE", error.message || "LBL_PRECONDITION_MISSING");
            error.handled = true;
        }
    };

    /**
     * 412 Header precondition failure error.
     */
    app.error.handleHeaderPreconditionFailed = function(error, b, c, d) {
        //Only kick off a sync if we are not already in the process of syncing
        if (error && error.code ==='metadata_out_of_date' && app.isSynced) {
            app.sync();
        }
    };

    /**
     * 424 Method failure error.
     */
    app.error.handleMethodFailureError = function(error) {
        // TODO: For finer grained control we could sniff the {error: <code>} in the response text (JSON) for one of:
        // missing_parameter, invalid_parameter, request_failure
        error.handled = true;
        if (error.code == "request_failure") {
            showErrorPage("422");
        } else {
            alertUser("precondtion_failure_error", "LBL_PRECONDITION_MISSING_TITLE", "LBL_PRECONDITION_MISSING");
        }
    };
       
    /**
     * 500 Internal server error handler. 
     */
    app.error.handleServerError = function(error) {
        if(error.payload.url) {
            // Redirect admins instead of loading the error view.
            if (app.acl.hasAccess('admin','Administration')) {
                app.router.navigate(error.payload.url,{trigger: true, replace: true});
                return;
            }
        }
        app.controller.loadView({
            layout: "error",
            errorType: error.status || "500",
            module: "Error",
            error: error, 
            create: true
        });
    };

})(SUGAR.App);

