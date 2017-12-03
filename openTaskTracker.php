<?php

include_once 'include/pdoinit.php';
include_once 'include/loginfunctions.php';

include_once 'include/permission_functions.php';
include_once 'include/permission_login_check.php';



sec_session_start(); //Wird nur gebraucht, wenn User eingelogt sein muss auf der Seite, bei index.php beispielsweise nicht
//DB-Initialisierung
$con = new connect_pdo();
$pdo = $con->dbh();


sec_session_start();
is_as_admin_permitted($pdo, 'index.php', 'index.php', 'Keine Berechtigung fuer Einstellungen', $return_back = TRUE);

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

<md-input-container class="md-block" flex="10">
    <label>Land</label>
    <md-select ng-change="showSaveButton = true"
               ng-model="customerdata.ident.land[$index]">
        <md-option ng-repeat="state in states" value="{{state.abbrev}}">
            {{state.abbrev}}
        </md-option>
    </md-select>
</md-input-container>




<div>
    <md-content class="customcoumn" layout-gt-md="row" layout-padding></md-content>
    <md-button class="md-raised" ng-click="showNewTaskDialog()">New Task</md-button>
    <md-button class="md-raised" ng-click="showNewProjectDialog()">New Projekt</md-button>
    </div>


<div class="simpleDemo row ">
    <div class="col-md-12 ">
        <div class="row ">
            <div ng-repeat="(listName, list) in models.lists" class="col-md-12 middle content">
                <div class="panel panel-info ">
                    <div class="panel-heading ">
                        <h3 class="panel-title ">List {{listName}}</h3>
                    </div>
                    <div class="panel-body " ng-include="'simple/simple.html'"></div>

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
