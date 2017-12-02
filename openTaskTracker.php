<?php
//include_once 'include/loginfunctions.php';
//require_once 'include/handle_session.php';
//$session = new SecureSessionHandler('test');
//$session->start();
require_once 'include/loginfunctions.php';

sec_session_start();
?>

<html lang="de" ng-app="openTaskTracker_App" class="responsivelayout">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>openTaskTracker Online</title>
    <link rel="stylesheet" href="css/screen.min.css"/>
    <link rel="stylesheet" href="css/dragdrop.css"/>
    <script src="bower_components/jquery/dist/jquery.min.js"></script>

    <script src="bower_components/angular/angular.min.js" type="text/javascript"></script>

    <!--Injected for Angular Dialogs-->
    <script src="bower_components/angular-animate/angular-animate.min.js"></script>
    <script src="bower_components/angular-aria/angular-aria.min.js"></script>
    <script src="bower_components/angular-material/angular-material.min.js"></script>
    <link rel="stylesheet" type="text/css" media="screen"
          href="bower_components/angular-material/angular-material.min.css"/>
    <script src="js/angular-drag-and-drop-lists.js"></script>

    <!--End of Injected for Angular Dialogs-->


    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">


    <link href="bower_components/hover/css/hover-min.css" rel="stylesheet" media="all">
    <link href="bower_components/angular-xeditable/dist/css/xeditable.css" rel="stylesheet">


    <script src="js/openTaskTracker.js"></script>
    <script src="js/ctrl/openTaskTrackerctrl.js"></script>


</head>
<body id="atmosphere-responsive" class="js notouch loaded" ng-controller="openTaskTracker_Ctrl">


<div>
    <md-toolbar>
        <div class="md-toolbar-tools">
            <md-truncate>OpenTaskTracker</md-truncate>
            <span flex></span>
            <a style="color:white;" ><?php echo htmlentities($_SESSION['username']); ?></a>
            <span flex="5"></span>
            <a style="color:white; " href="include/process_logout.php">Logout</a>


        </div>
    </md-toolbar>

    <md-content class="customcoumn" layout-gt-md="row" layout-padding>
    </md-content>
</div>

<div>
    <md-content class="customcoumn" layout-gt-md="row" layout-padding></md-content>
    <md-button class="md-raised md-primary" ng-click="showNewTaskDialog()">New Task</md-button>
    <md-button class="md-raised md-warn" ng-click="showNewProjectDialog()">New Projekt</md-button>
</div>




<div>
    <md-subheader class="md-no-sticky">Set your Filter's</md-subheader>
</div>
<div>
    <md-input-container md-no-float>
        <input type="text" placeholder="Filter 1">
    </md-input-container>
    <md-input-container md-no-float>
        <input type="text" placeholder="Filter 2">
    </md-input-container>
    <md-input-container md-no-float>
        <input type="text" placeholder="Filter 3">
    </md-input-container>
    <md-input-container md-no-float>
        <input type="text" placeholder="Filter 4">
    </md-input-container>
</div>

<div class="simpleDemo row">
    <div class="col-md-12">
        <div class="row">
            <div ng-repeat="(listName, list) in models.lists" class="col-md-12 middle">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">List {{listName}}</h3>
                    </div>
                    <div class="panel-body" ng-include="'simple/simple.html'"></div>

                </div>
            </div>
        </div>

        <div view-source="simple">

        </div>
    </div>
</div>



    <div style="visibility: hidden">
        <div class="md-dialog-container" id="newProject">
            <md-dialog layout-padding>

            </md-dialog>
        </div>
    </div>

</body>
</html>
