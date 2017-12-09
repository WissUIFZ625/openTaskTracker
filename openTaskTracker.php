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
            <a style="color:white;"><?php echo htmlentities($_SESSION['username']); ?></a>
            <span flex="5"></span>
            <a style="color:white; " href="include/process_logout.php">Logout</a>


        </div>
    </md-toolbar>


</div>

<div class="buttonbar">
    <md-content layout-gt-md="row">
        <span flex></span>
        <md-button class="md-raised md-primary" ng-click="showNewTaskDialog()">New Task</md-button>
        <md-button class="md-raised md-warn" ng-click="showNewProjectDialog()">New Projekt</md-button>
        <span flex></span>
    </md-content>
</div>


<div>
    <md-content layout-gt-md="row">
        <span flex></span>
        <md-input-container class="md-block" flex="15">
            <label>Projectstatus</label>
            <md-select ng-change="showSaveButton = true"
                       ng-model="status">
                <md-option ng-repeat="projectstate in projectstatus" value="{{projectstate.abbrev}}">
                    {{projectstate.abbrev}}
                </md-option>
            </md-select>
        </md-input-container>
        <span flex></span>
        <md-input-container class="md-block" flex="15">
            <label>Bearbeiter</label>
            <md-select ng-change="showSaveButton = true"
                       ng-model="bearbeiter">
                <md-option ng-repeat="projectma in projectbearbeiter" value="{{projectma.abbrev}}">
                    {{projectma.abbrev}}
                </md-option>
            </md-select>
        </md-input-container>
        <span flex></span>
        <md-dialog-actions
                ng-show="showSaveButton">
            <md-button class="md-raised">
                Anzeigen
            </md-button>
        </md-dialog-actions>
        <md-dialog-actions
                ng-hide="showSaveButton">

                <label style="margin-top: 20px;">
                    Select your Filter
                </label>

        </md-dialog-actions>
        <span flex></span>
        <md-input-container class="md-block" flex="15">
            <label>Produktebacklogs</label>
            <md-select ng-change="showSaveButton = true"
                       ng-model="backlogs">
                <md-option ng-repeat="backlogs in produktbacklogs" value="{{backlogs.abbrev}}">
                    {{backlogs.abbrev}}
                </md-option>
            </md-select>
        </md-input-container>
        <span flex></span>
        <md-input-container class="md-block" flex="15">
            <label>Major-Projekte</label>
            <md-select ng-change="showSaveButton = true"
                       ng-model="major">
                <md-option ng-repeat="major in majorprojekt" value="{{major.abbrev}}">
                    {{major.abbrev}}
                </md-option>
            </md-select>
        </md-input-container>
        <span flex></span>
    </md-content>
</div>


<div class="simpleDemo row ">
    <div class="col-sm-12 ">
        <div class="row ">
            <div ng-repeat="(listName, list) in models.lists" class="col-md-12 middle content">
                <div class="panel panel-info ">
                    <div class="panel-heading ">
                        <h3 class="panel-title ">{{listName}}</h3>
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
        <md-dialog aria-label="Neues Projekt erstellen ...">

            <form name="virtForm" ng-cloak class="projectdialog">
                <md-toolbar>
                    <div class="md-toolbar-tools">
                        <h2>Neues Projekt erstellen</h2>
                        <span flex></span>
                        <md-button class="md-icon-button" ng-click="closeDialog()">
                            <img src="img_inter/close.png" aria-label="Close dialog" class="ng-scope">
                        </md-button>
                    </div>
                </md-toolbar>
                <md-dialog-content>

                    <md-tabs md-dynamic-height="true">
                        <md-tab>

                            <md-tab-body>
                                <div layout="column">
                                    <div layout-sm="row" layout-align="start" layout-margin>
                                        <div>
                                            <md-input-container class="md-block">
                                                <input flex="100" type="text" id="inp_projekt_titel"
                                                       class="form-control pull-left col-md-8 input-sm ng-pristine ng-empty ng-invalid ng-invalid-required ng-touched"
                                                       placeholder="Projekt Titel" data-toggle="tooltip"
                                                       title="Titel"
                                                       name="projekt_titel" ng-model="projekt.titel"  ng-required="true"
                                                       data-original-title="Titel">
                                            </md-input-container>
                                        </div>
                                    </div>
                                    <div layout-sm="row" layout-align="start" layout-margin>
                                        <div>
                                            <md-input-container class="md-block" flex="100">
                                                <label>Projekt Gruppe</label>
                                                <md-select  id="inp_group" ng-required="true" title="Projekt Gruppe"
                                                            ng-model="status">
                                                    <md-option ng-repeat="projectgroup in projectgruppe" value="{{projectgroup.abbrev}}">
                                                        {{projectgroup.abbrev}}
                                                    </md-option>
                                                </md-select>
                                            </md-input-container>
                                        </div>
                                    </div>
                                    <div layout-sm="row" layout-align="start" layout-margin>
                                        <div>
                                            <md-input-container class="md-block" flex="100">
                                                <label>Projectstatus</label>
                                                <md-select  id="inp_status" ng-required="true"
                                                           ng-model="status">
                                                    <md-option ng-repeat="projectstate in projectstatus" value="{{projectstate.abbrev}}">
                                                        {{projectstate.abbrev}}
                                                    </md-option>
                                                </md-select>
                                            </md-input-container>
                                        </div>
                                    </div>

                                </div>
                            </md-tab-body>

                        </md-tab>

                    </md-tabs>
                </md-dialog-content>
                <md-dialog-actions>
                    <md-button class="md-primary" ng-click="closeDialog()">Abbrechen</md-button>
                    <md-button class="md-primary" ng-click="closeDialog()" id="save_new_project">Speichern
                    </md-button>
                </md-dialog-actions>
            </form>
        </md-dialog>
    </div>
</div>

<div style="visibility: hidden">
    <div class="md-dialog-container" id="newTaskProject">
        <md-dialog layout-padding>

        </md-dialog>
    </div>
</div>

</body>
</html>
