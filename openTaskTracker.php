<?php
//include_once 'include/loginfunctions.php';
//require_once 'include/handle_session.php';
//$session = new SecureSessionHandler('test');
//$session->start();
require_once 'include/loginfunctions_inter.php';

sec_session_start();
?>

<html lang="de" ng-app="cockpit_interApp" class="responsivelayout">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>Xaxada Online</title>
    <link rel="stylesheet" href="css/screen.min.css"/>
    <link rel="stylesheet" href="css_inter/style.css"/>
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


    <link rel="stylesheet" type="text/css" href="css_inter/menu.css">
    <link rel="stylesheet" type="text/css" href="css_inter/result-light.css">
    <link href="bower_components/hover/css/hover-min.css" rel="stylesheet" media="all">
    <link href="bower_components/angular-xeditable/dist/css/xeditable.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" media="screen" href="css_inter/bootstrap.min.css"/>

    <link rel="stylesheet" href="css_inter/jquery-ui.css">
    <link rel="stylesheet" href="css_inter/cleanslate.css"/>

    <link href="css_inter/styles.css" rel="stylesheet" type="text/css">
    <link href="css_inter/hamburgler.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css_inter/configuration.css" type="text/css" media="screen"/>
    <link rel="stylesheet" type="text/css" href="css_inter/normalize.css">
    <link rel="stylesheet" type="text/css" href="css_inter/customer_cockpit_inter.css">
    <!--<script src="js_inter/configuration.js"></script>-->

    <script src="js_inter/cockpit_inter.js"></script>
    <script src="js_inter/ctrl/cockpit_interctrl.js"></script>

</head>
<body id="atmosphere-responsive" class="js notouch loaded" ng-controller="cockpit_interCtrl">

<!--//modals-->
<?php

if(isset($_GET["wel"]) && !empty($_GET["wel"]))
{
    echo "<div ng-controller=\"cockpit_interCtrl\" data-ng-init='welcometoxaxada()'></div>";
}

?>

<div style="visibility: hidden">
    <div class="md-dialog-container" id="alertNotLogedInDialog">

        <md-dialog aria-label="Nicht eingelogt">
            <form ng-cloak>
                <md-toolbar>
                    <div class="md-toolbar-tools">
                        <h2 style="color:white;">Nicht eingelogt</h2>
                        <span flex></span>
                        <!--<md-button class="md-icon-button" ng-click="closeDialog()">-->
                        <!--  <!--<md-icon md-svg-src="img/icons/ic_close_24px.svg" aria-label="Close dialog"></md-icon>-->
                        <!--  <img src="img/close.png" aria-label="Close dialog">-->
                        <!--</md-button>-->
                    </div>
                </md-toolbar>

                <md-dialog-content>
                    <div class="md-dialog-content">
                        <span>Sie sind nicht eingelogt...</span>
                    </div>
                </md-dialog-content>

                <md-dialog-actions layout="row">
                    <!--<md-button ng-click="closeDialog()" id="codeModal_vswitch_modal_ok">-->
                    <md-button id="notLogedInok">
                        Zum Login
                    </md-button>
                    <!--  <md-button ng-click="closeDialog()">-->
                    <!--	Schliessen-->
                    <!--  </md-button>-->
                </md-dialog-actions>
            </form>
        </md-dialog>
    </div>
</div>


<!--<body>-->
<?php
if (is_logedin()) {
    ?>
    <div>
        <?php include 'include_inter/navi_inter.php' ?>
        <!--<div style="margin-top:-20px;">-->

        <div>
            <!--<h1>Logged in</h1>-->
            <div>
                <md-toolbar md-scroll-shrink>
                    <div class="md-toolbar-tools">Ihre xaxada</div>
                </md-toolbar>

            </div>

            <md-content layout-gt-md="row" layout-padding class="leftpadding customcoumn"> <!--Weisser Hintergrund-->

                <md-input-container flex="35">
                    <label style="overflow: visible; margin-left: 5% ">Neuen Identifikationscode hinzufügen</label>
                    <input ng-model="new_ident_hash" id="new_ident_hash">
                </md-input-container>


                <md-input-container ng-hide="new_ident_hash=='' || new_ident_hash==null">
                    <md-button class="md-icon-button md-accent" aria-label="Bestätigungsmail senden"
                               ng-click="send_idhash_cmail()" ng-mouseover="text = true" ng-mouseleave="text = true">
                        <md-icon md-svg-icon="img_inter/svg/email.svg"></md-icon>

                </md-input-container>

                <label ng-hide="new_ident_hash=='' || new_ident_hash==null" class="sendmail" ng-show="text" flex="40" ng-click="send_idhash_cmail()">E-Mail
                    senden</label>

                <md-content ng-repeat="single_ident in customerdata.ident.identify_hash">
                    <md-dialog-actions
                            ng-show=" customerdata.ident.identify_hash[$index] == null && new_ident_hash=='' ">
                        <md-button class="md-raised md-warn"
                                   ng-click="insert_hash_from_clipboard()">
                            Drücke nun nacheinader die Tasten (CTRL + V)
                        </md-button>
                    </md-dialog-actions>
                </md-content>


            </md-content>


            <md-content class="customcoumn" ng-repeat="single_ident in customerdata.ident.identify_hash">
                <md-content layout="column" class="ng-scope layout-column">
                    <md-content layout-gt-md="row" layout-padding
                                ng-show="customerdata.ident.identify_hash[$index] != null"> <!--Weisser Hintergrund-->
                        <md-input-container
                                ng-show="customerdata.ident.ident_confirmcode[$index]==false && customerdata.ident.dbmail_sent[$index]==false && clear_Interval_Time(customerdata.ident.identify_hash[$index])"
                                class="md-block">
                            <md-icon md-svg-icon="img_inter/svg/checked.svg"></md-icon>
                        </md-input-container>
                        <md-input-container
                                ng-show="customerdata.ident.ident_confirmcode[$index]==true && customerdata.ident.dbmail_sent[$index]!=false"
                                class="md-block">
                            <label style="overflow: visible; margin-left: 30px; ">Email versendet: <span>{{customerdata.ident.dbmail_sent[$index]}}</span></label>
                            <md-icon id="demo" md-svg-icon="img_inter/svg/watch_later.svg"></md-icon>
                        </md-input-container>


                        <md-input-container class="leftpadding hash" flex-gt-sm>
                            <label>Identifikationscode</label>
                            <input ng-model="single_ident" ng-readonly="true">
                        </md-input-container>


                        <md-input-container>
                            <!--<md-checkbox name="standard" ng-model="customerdata.ident.standard" required >
                                Als Standard definieren
                            </md-checkbox>-->
                            <md-dialog-actions
                                    ng-show="customerdata.ident.ident_confirmcode[$index]==false && customerdata.ident.dbmail_sent[$index]==false">
                                <md-button class="md-raised"
                                           ng-click="chk_hash_redirect('../xDistributor/web/xinter-v1.1.1-0/?pi_id_hash='+customerdata.ident.identify_hash[$index])">
                                    Zum System
                                </md-button>
                            </md-dialog-actions>
                            <md-dialog-actions
                                    ng-show="customerdata.ident.ident_confirmcode[$index]==true && customerdata.ident.dbmail_sent[$index] !=false ">
                                <md-button class="md-raised md-primary" aria-label="Bestätigungsmail senden"
                                           ng-click="send_mail_again(customerdata.ident.identify_hash[$index])">
                                    Keine E-Mail erhalten? Erneut senden
                                </md-button>
                            </md-dialog-actions>
                        </md-input-container>
                        <md-input-container>
                            <!--<md-checkbox name="standard" ng-model="customerdata.ident.standard" required >
                                Als Standard definieren
                            </md-checkbox>-->
                            <md-dialog-actions
                                    ng-show="customerdata.ident.ident_confirmcode[$index]==false && customerdata.ident.dbmail_sent[$index]==false">
                                <md-button class="md-raised md-warn"
                                           ng-click="clear_device_dlg(customerdata.ident.identify_hash[$index])">
                                    Löschen
                                </md-button>
                            </md-dialog-actions>
                            <!--<md-dialog-actions
                                    ng-show="customerdata.ident.ident_confirmcode[$index]==false && customerdata.ident.dbmail_sent[$index]==false && clear_Interval_Time()">
                                <md-button class="md-raised md-warn"
                                           ng-click="clear_device_dlg(customerdata.ident.identify_hash[$index])">
                                    Timer Stop
                                </md-button>
                            </md-dialog-actions>-->


                        </md-input-container>


                    </md-content>

                    <!-- <md-radio-button  ng-model="endtype" value="0"  name="endopt" class="md-primary">weniger Anzeigen</md-radio-button>
                     <md-radio-button  ng-model="endtype" value="2"  name="endopt" class="md-primary">mehr Anzeigen</md-radio-button>-->
                    <md-dialog-actions
                            ng-show="customerdata.ident.ident_confirmcode[$index]==false && customerdata.ident.dbmail_sent[$index]==false">
                        <div class="hiddenbutton" flex-gt-md>


                            <label class="radio-inline"><input type="radio" ng-model="endtype"
                                                               value="0" name="endopt{{customerdata.ident.namerp[$index]}}">weniger Anzeigen</label>

                            <label class="radio-inline"><input type="radio" ng-model="endtype"
                                                               value="2" name="endopt{{customerdata.ident.namerp[$index]}}">mehr Anzeigen</label>

                        </div>


                        <div class=" check-element animate-show" ng-hide="endtype=='0'">
                            <md-content layout-gt-md="row">
                                <md-input-container class="md-block" flex-gt-sm>
                                    <label>Name des xaxada</label>
                                    <input ng-change="showSaveButton = true"
                                           ng-model="customerdata.ident.namerp[$index]"
                                           placeholder="Geben Sie Ihrem xaxada einen Namen...">
                                </md-input-container>

                                <md-input-container class="md-block" flex-gt-sm>
                                    <label>Standort Ihres xaxada</label>
                                    <input ng-change="showSaveButton = true"
                                           ng-model="customerdata.ident.standort[$index]"
                                           placeholder="Definieren Sie hier einen Standort...">
                                </md-input-container>

                                <md-input-container class="md-block" flex-gt-sm>
                                    <label>Land</label>
                                    <md-select ng-change="showSaveButton = true"
                                               ng-model="customerdata.ident.land[$index]">
                                        <md-option ng-repeat="state in states" value="{{state.abbrev}}">
                                            {{state.abbrev}}
                                        </md-option>
                                    </md-select>
                                </md-input-container>


                                <md-dialog-actions
                                        ng-show="showSaveButton">
                                    <md-button class="md-raised"
                                               ng-click="update_option_device(customerdata.ident.identify_hash[$index],customerdata.ident.namerp[$index],customerdata.ident.standort[$index], customerdata.ident.land[$index])">
                                        Speichern
                                    </md-button>
                                </md-dialog-actions>
                            </md-content>
                        </div>
                    </md-dialog-actions>
                </md-content>


        </div>
    </div>
    </div>

    <?php
} else {

    ?>

    <div id="notLogedIn">
        Not logged in
    </div>
    <?php
}
?>
</body>
</html>

