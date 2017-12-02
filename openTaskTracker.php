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
    <script type="text/ng-template" id="list.html">
        <ul dnd-list="list">
            <li ng-repeat="item in list"
                dnd-draggable="item"
                dnd-effect-allowed="move"
                dnd-moved="list.splice($index, 1)"
                dnd-selected="models.selected = item"
                ng-class="{selected: models.selected === item}"
                ng-include="item.type + '.html'">
            </li>
        </ul>
    </script>

</head>
<body id="atmosphere-responsive" class="js notouch loaded" ng-controller="openTaskTracker_Ctrl">



<div>
    <!--<h1>Logged in</h1>-->
    <md-toolbar>
        <div class="md-toolbar-tools">


            <md-truncate>Task</md-truncate>



        </div>
    </md-toolbar>



        <md-content class="customcoumn" layout-gt-md="row" layout-padding>


        </md-content>
    </div>
</div>

<!-- This template is responsible for rendering a container element. It uses
     the above list template to render each container column -->
<script type="text/ng-template" id="container.html">
    <div class="container-element box box-blue">
        <h3>Container {{item.id}}</h3>
        <div class="column" ng-repeat="list in item.columns" ng-include="'list.html'"></div>
        <div class="clearfix"></div>
    </div>
</script>

<!-- Template for a normal list item -->
<script type="text/ng-template" id="item.html">
    <div class="item">Item {{item.id}}</div>
</script>

<!-- Main area with dropzones and source code -->
<div class="col-md-10">
    <div class="row">
        <div ng-repeat="(zone, list) in models.dropzones" class="col-md-6">
            <div class="dropzone box box-yellow">
                <!-- The dropzone also uses the list template -->
                <h3>Dropzone {{zone}}</h3>
                <div ng-include="'list.html'"></div>
            </div>
        </div>
    </div>

    <div view-source="nested"></div>

    <h2>Generated Model</h2>
    <pre>{{modelAsJson}}</pre>
</div>

<!-- Sidebar -->
<div class="col-md-2">

    <div class="toolbox box box-grey box-padding">
        <h3>New Elements</h3>
        <ul>
            <!-- The toolbox only allows to copy objects, not move it. After a new
                 element was created, dnd-copied is invoked and we generate the next id -->
            <li ng-repeat="item in models.templates"
                dnd-draggable="item"
                dnd-effect-allowed="copy"
                dnd-copied="item.id = item.id + 1"
            >
                <button type="button" class="btn btn-default btn-lg" disabled="disabled">{{item.type}}</button>
            </li>
        </ul>
    </div>

    <div ng-if="models.selected" class="box box-grey box-padding">
        <h3>Selected</h3>
        <strong>Type: </strong> {{models.selected.type}}<br>
        <input type="text" ng-model="models.selected.id" class="form-control" style="margin-top: 5px" />
    </div>

    <div class="trashcan box box-grey box-padding">
        <!-- If you use [] as referenced list, the dropped elements will be lost -->
        <h3>Trashcan</h3>
        <ul dnd-list="[]">
            <li><img src="nested/trashcan.jpg"></li>
        </ul>
    </div>

</div>

</body>
</html>

