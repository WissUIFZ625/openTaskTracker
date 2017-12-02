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

</head>
<body id="atmosphere-responsive" class="js notouch loaded" ng-controller="openTaskTracker_Ctrl">



<div>
    <!--<h1>Logged in</h1>-->
    <div>
        <md-toolbar md-scroll-shrink>
            <div class="md-toolbar-tools">Kundendaten</div>
        </md-toolbar>
        <md-content class="customcoumn" layout-gt-md="row" layout-padding>

            <md-input-container>
                <label>Vorname</label>
                <input ng-change="showSaveButton = true" ng-model="customerdata.name" type="text">
            </md-input-container>
            <md-input-container>
                <label>Nachname</label>
                <input ng-change="showSaveButton = true" ng-model="customerdata.surname" type="text">
            </md-input-container>
        </md-content>
    </div>
</div>

</body>
</html>

