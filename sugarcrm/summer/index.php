<?php
if (empty($session_id)) {
    session_start();
}
if (!empty($_REQUEST['token'])) {
    chdir('..');
    if (!defined('sugarEntry')) {
        define('sugarEntry', true);
    }
    include 'include/entryPoint.php';
    require_once 'summer/splash/BoxOfficeClient.php';
    $box = BoxOfficeClient::getInstance();
    $box->createSession();
    // reload
    if (empty($_SESSION['authenticated_user_id'])) {
        $box->noLogin();
    }
    header("Location: index.php");
    die();
}
if (empty($_SESSION['authenticated_user_id'])) {
    header('Location: splash/');
    die();
}
if(!empty($_REQUEST['demo'])){
    if (!defined('sugarEntry')) {
        define('sugarEntry', true);
    }
    chdir('..');
    include 'include/entryPoint.php';
    include 'summer/demo.php';
    chdir('summer');
    header("Location: index.php");
}

?>
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="x-ua-compatible" content="IE=8">
    <?php
    $min_file = 'summer/summer.min.css';
    if(file_exists("../cache/".$min_file)) {
        echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"../cache/$min_file\" />\n";
    } else {
        require_once('../jssource/JSGroupings.php');
        foreach($js_groupings as $group) {
            foreach($group as $file => $min) {
                if($min == $min_file) {
                    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"../$file\" />\n";
                }
            }
        }
    }
    ?>
</head>
<body>
<div>
    <div id="sidecar">
        <div id="alert" class="alert-top"></div>
        <div id="header"></div>
        <div id="subnav"></div>
        <div id="content"></div>
        <div id="footer"></div>
        <div id="todo-widget-container" class="btn-group"></div>
    </div>
</div>

<?php
    $min_file = 'summer/summer.min.js';
    if(file_exists("../cache/".$min_file)) {
        echo "<script src=\"../cache/$min_file\"></script>\n";
    } else {
        require_once('../jssource/JSGroupings.php');
        foreach($js_groupings as $group) {
            foreach($group as $file => $min) {
                if($min == $min_file) {
                    echo "<script src=\"../$file\"></script>\n";
                }
            }
        }
    }
?>
<script language="javascript">
    var App, syncResult, view, layout, html;

    SUGAR.App[SUGAR.App.config.authStore || "cache"].set('AuthAccessToken', '<?php echo session_id();?>');
    SUGAR.App[SUGAR.App.config.authStore || "cache"].set('AuthRefreshToken', '<?php echo session_id();?>');

    App = SUGAR.App.init({
        el: "#sidecar",
        callback: function(app) {

            app.start();
            app.api.me("read", null, null, {
                success: function(data) {
                    if (data.current_user) {
                        app.user._reset(data ? data.current_user : null);
                    }
                    //callback(null, data);
                }
            });


            if(!_.has(app, 'forecasts')) {
                app.forecasts = {}
            }
            app.augment("forecasts", _.extend(app.forecasts, {
                initForecast: function() {
                    var url = app.api.buildURL("Forecasts/init");
                    App.api.call('GET', url, null, {success: function(forecastData) {
                        // get default selections for filter and category
                        app.defaultSelections = forecastData.defaultSelections;
                        app.initData = forecastData.initData;
                        app.user.set(app.initData.selectedUser);
                    }});
                    return app;
                }
            }));
            app.forecasts.initForecast();

            if(!_.has(app, 'entityList')) {
                var url = app.api.buildURL("CustomReport/EntityList");
                app.entityList = [];
                // Fetch taggable entities.
                app.api.call('GET', url, null, {success: function(o) {
                    app.entityList = o;
                }});
            }

        }

    });

    App.api.debug = App.config.debugSugarApi;
</script>
</body>
</html>
