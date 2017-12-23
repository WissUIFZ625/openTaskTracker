<?php

include_once 'include/pdoinit.php';
include_once 'include/loginfunctions.php';

include_once 'include/permission_functions.php';
include_once 'include/permission_login_check.php';


sec_session_start(); //Wird nur gebraucht, wenn User eingelogt sein muss auf der Seite, bei index.php beispielsweise nicht
//DB-Initialisierung
$con = new connect_pdo();
$pdo = $con->dbh();



is_as_admin_permitted($pdo, 'index.php', 'index.php', 'Keine Berechtigung fuer Einstellungen', $return_back = TRUE);

?>

<html lang="de" ng-app="openTaskTracker_App" class="responsivelayout">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>openTaskTracker Online</title>

    <link rel="stylesheet" href="css/screen.min.css"/>
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


    <link rel="stylesheet" href="css/dragdrop.css"/>
</head>
<body id="atmosphere-responsive" class="js notouch loaded" ng-controller="openTaskTracker_Ctrl">


<div>
    <md-toolbar>
        <div class="md-toolbar-tools">
            <md-truncate>OpenTaskTracker</md-truncate>
            <span flex></span>
            <md-menu md-offset="30 35">
            <a style="color:white;" id="settings" ng-click="$mdMenu.open($event)"><?php echo "<img src=\"image/ic_control_point_black_24px.svg\">"?><?php echo htmlentities($_SESSION['username']); ?></a>

                <md-menu-content>

                    <md-menu-item id="new_group">
                        <md-button ng-click="showDialoges('opennewgroup') ">Neue Gruppe erstellen</md-button>
                    </md-menu-item>

                    <md-menu-item id="new_group">
                        <md-button ng-click="showDialoges('openaddGroup')">User Gruppe zuweisen</md-button>
                    </md-menu-item>

                    <md-menu-item id="new_projekt">
                        <md-button ng-click="showDialoges('newProject') ; getGroup();">Neues Projekt</md-button>
                    </md-menu-item>

                    <md-menu-item id="new_projekt">
                        <md-button ng-click="test()">Neuer Benutzer erstellen</md-button>
                    </md-menu-item>

                    <md-menu-item id="new_sprint">
                        <md-button ng-click="showDialoges('opennewSprint'); getProjekt();">Neuer Sprint </md-button>
                    </md-menu-item>

                </md-menu-content>
            </md-menu>
            <span flex="5"></span>
            <a style="color:white; " href="include/process_logout.php">Logout</a>
        </div>
    </md-toolbar>
</div>

<div class="buttonbar">
    <md-content layout-gt-md="row">
        <span flex></span>
        <md-button class="md-raised md-primary" ng-click="showDialoges('newTaskProject')">New Task</md-button>


        <md-dialog-actions class="screenhidden"
                ng-show="displayvieu === 4" >
            <md-button class="md-raised "
                       ng-click="displayvieu = 12">
                Default
            </md-button>
        </md-dialog-actions>
        <md-dialog-actions class="screenhidden"
                ng-show="displayvieu === 12" >
            <md-button class="md-raised "
                       ng-click="displayvieu = 4">
                Kanban
            </md-button>
        </md-dialog-actions>
        <md-dialog-actions
                ng-show="showSaveButton" ng-click="clear()">
            <md-button class="md-raised">
                Filter Löschen
            </md-button>
        </md-dialog-actions>
        <md-dialog-actions
                ng-hide="showSaveButton || ngfilterdiv == true" ng-click="ngfilterdiv = true">
            <md-button class="md-raised">
                Filter Anwenden
            </md-button>
        </md-dialog-actions>
        <md-dialog-actions
                 ng-click="ngfilterdiv = false; showSaveButton = false" ng-show="ngfilterdiv == true ">
            <md-button class="md-raised">
                Filter Ausblenden
            </md-button>
        </md-dialog-actions>

        <!--Vorläufig auskommentiert, wurde hinter username hinterlegt-->
       <!-- <md-button class="md-raised md-warn" ng-click="showNewProjectDialog()">New Projekt</md-button>-->
        <span flex></span>
    </md-content>
</div>


<md-dialog-actions  ng-show="ngfilterdiv">
    <md-content layout-gt-sm="row">
        <span flex></span>
        <md-input-container class="md-block flexwith" flex>
            <label>Projectstatus</label>
            <md-select ng-change="showSaveButton = true"
                       ng-model="tasktosearch.task_tst_id">
                <md-option ng-repeat="projectstate in projectstatus" value="{{$index +1}}">
                    {{projectstate.abbrev}}
                </md-option>
            </md-select>
        </md-input-container>
        <span flex></span>
        <md-input-container class="md-block flexwith" flex>
            <label>Bearbeiter</label>
            <md-select ng-change="showSaveButton = true"
                       ng-model="bearbeiter">
                <md-option ng-repeat="user in users" value="{{user.usr_name}}">
                    {{user.usr_name}}
                </md-option>
            </md-select>
        </md-input-container>
        <span flex></span>
        <span flex></span>
        <md-input-container class="md-block flexwith" flex>
            <label>Projekt</label>
            <md-select ng-change="showSaveButton = true"
                       ng-model="backlogs">
                <md-option ng-repeat="project in projects" value="{{project.pro_id}}">
                    {{project.pro_name}}
                </md-option>
            </md-select>
        </md-input-container>
        <span flex></span>
        <!--<md-input-container class="md-block flexwith" flex>
            <label>Gruppe</label>
            <md-select ng-change="showSaveButton = true"
                       ng-model="major">
                <md-option ng-repeat="group in groups" value="{{group.grp_name}}">
                    {{group.grp_name}}
                </md-option>
            </md-select>
        </md-input-container>-->
        <md-input-container class="md-block flexwith" flex>
            <label>Tasks</label>
            <md-select ng-change="showSaveButton = true"
                       ng-model="tasktosearch[i].task_name">
                <md-option ng-repeat="task in tasks" value="{{task.task_name}}">
                    {{task.task_name}}
                </md-option>
            </md-select>
        </md-input-container>
        <span flex></span>
    </md-content>
</md-dialog-actions>


<div class="simpleDemo row ">
    <div class="col-sm-12 middle">
        <div class="row ">
            <div ng-repeat="(listName, list) in models.lists" class="col-md-{{displayvieu}}  content">
                <div class="panel panel-info ">
                    <div class="panel-heading ">
                        <h3 class="panel-title ">{{listName}}</h3>
                    </div>
                    <div class="panel-body {{listName}}" ng-include="'simple/simple.html'" ondrop="dropped(event)"></div>

                </div>
            </div>
        </div>

        <div view-source="simple">

        </div>
    </div>
</div>


<div class="panel-body " ng-include="'dialog/dialoges.html'"></div>




</body>
</html>
