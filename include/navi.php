<?php
//include_once 'include/pdoinit.php';
//include_once 'include/loginfunctions.php';
//include_once 'include/usr_grp_functions.php';
//include_once 'include/permission_functions.php';
?>


<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
			<title>openTaskTracker</title>
			<link rel="stylesheet" href="css_inter/navi.css" type="text/css" />
			<!--<script src="js_inter/mnavlookup.js"></script>-->
			<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="bootstrap-3.3.7-dist/css/bootstrap.min.css" crossorigin="anonymous">
<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">-->

<!-- Optional theme -->
<link rel="stylesheet" href="bootstrap-3.3.7-dist/css/bootstrap-theme.min.css" crossorigin="anonymous">
<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">-->

</head>
<body>

    <div id="MainNavCon">
        <div class="logomainDiv"  onclick="document.getElementById('fav').style.display = 'none';">

            <!---<div style="font-size: 208%;color: white;margin-top: 31px;margin-left: 18px;  font-family: Microsoft Sans Serif, sans-serif;">openTaskTracker®</div>->
            <!--<img src="img/LOGO_PF.png" class="logomain">-->
        </div>

        <div id="NaviTopCon">
            <img src="img_inter/subnav_l.png" class="navcorL">
            <img src="img_inter/menulog.png" class="topnavminicon">
            <div id="NaviTop" class="topnav">
                <div class="col-sm-1 dropdown topnavitem">
                    <a style="color:white;" disabled="true"onclick="location.href='#';">Einstellungen</a>
<!--                    <div class="dropdown-content">
                        <p>Dropdown1</p>
                    </div>-->
                </div>
                <div class="col-sm-2 dropdown topnavitem">
                    <span style="color:white;" onclick="window.open('https://openTaskTracker.com/de/support---contact.html', 'zweitfenster')">&Uuml;ber Uns</span>
<!--                    <div class="dropdown-content">
                        <p>Dropdown2</p>
                    </div>-->
                </div>
                <div class="col-sm-2 dropdown topnavitem">
                    <img class="col-sm-2 dropdown topnavitem" style="height: 63px; width: auto;  cursor:pointer;  margin-left: -20px; top: -19px;" src="img_inter/openTaskTracker.png" onclick="window.open('http://www.openTaskTracker.com', 'zweitfenster')">
<!--                    <div class="dropdown-content">
                        <p>Dropdown3</p>
                    </div>-->
                </div>
                 <div class="col-sm-1 dropdown topnavitem">
                     <a style="color:white;" disabled href=""><?php echo htmlentities($_SESSION['user_mail']); ?></a>
<!--                    <div class="dropdown-content">
                        <p>Dropdown4</p>
                    </div>-->
                </div>
                <div class="col-sm-2 dropdown topnavitem">
                    <a style="color:white;  margin-left: 145px;" href="include_inter/process_logout_inter.php">Logout</a>
                </div>
           </div>
           <img src="img_inter/subnav_r.png" class="navcorR">
        </div>
        <div id="MainMenu" class="col-sm-12 menubar">
            <md-dialog-actions>
                <md-button class="useredit md-raised md-primary"  onclick="location.href='customer_cockpit_inter.php';">
                    Cockpit
                </md-button>
            </md-dialog-actions>
            <md-dialog-actions>
                <md-button class="useredit md-raised" onclick="location.href='userdata.php';">
                    Benutzerdaten
                </md-button>
            </md-dialog-actions>
            <md-dialog-actions>
                <md-button class="useredit md-raised" onclick="location.href='connected_openTaskTracker.php';">
                    openTaskTracker Verwalten
                </md-button>
            </md-dialog-actions>
            <!--<img src="img/menulog.png" class="menubarminicon">-->
         <!--  <button id="Verbraucher" class="col-sm-1"  type="button" data-original-title="" onclick="location.href='customer_cockpit.php';" title="">Cockpit</button>-->
	   <!--<button class="col-sm-1" id="to_cockpit" type="button" data-original-title="" onclick="location.href='customer_cockpit_inter.php';" title="">Cockpit</button>-->
           <!--  <button id="SchalterSensoren" class="col-sm-1" type="button" data-original-title="" onclick="location.href='enoceaninstall.php';" title="">Schalter & Sensoren</button>
            <button id="Verbinden" class="col-sm-1" type="button" data-original-title="" onclick="location.href='enoceanconfig.php';" title="">Verbinden</button>
            <button id="Webschalter" class="col-sm-1" type="button" data-original-title="" onclick="location.href='webswitch.php';" title="">Webschalter</button>
            <button id="Logik" class="col-sm-1" type="button" data-original-title="" onclick="location.href='connectmiddle.php';" title="">Logik</button>
            <button id="Gruppen" class="col-sm-1" type="button" data-original-title="" onclick="location.href='location.php';" title="">Räume</button>
            <button id="Benutzer" class="col-sm-1" type="button" data-original-title="" onclick="location.href='users.php';" title="">Benutzer</button>   
             <button id="Berechtigungen" class="col-sm-1" type="button" data-original-title="" onclick="location.href='access_management.php';" title="">Berechtigungen</button>
             <button id="Freigaben" class="col-sm-1" type="button" data-original-title="" onclick="location.href='share.php';" title="">Freigaben</button>
	    <button id="Webschalter" class="col-sm-1" type="button" data-original-title="" onclick="location.href='switch.php';" title="">Quick</button>-->
<!--        <button id="Schalten" class="col-sm-1" type="button" data-original-title="" onclick="location.href='switchgui.php';" title="">Zum System</button>
	    <button id="Schalten" class="col-sm-1" type="button" data-original-title="" onclick="location.href='switchgui.php';" title="">Zu Freigaben</button><!--Nötig?-->
	    <!--<button id="to_system" class="col-sm-1" type="button" data-original-title="" onclick="location.href='../xinter-v1.2/index.php';" title="">Zum System</button>-->
	    <!--<button id="to_system" class="col-sm-1" type="button" data-original-title="" data-href="../xinter-v1.2/index.php" onclick="location.href='../xinter-v1.2/index.php';" title="">Zum System</button>-->
	    <!--<button id="to_system" class="col-sm-1" type="button" data-original-title="" data-href="../xDistributor/web/xinter-v1.1.1-0/" title="">Zum System</button>-->
	    <!--<button class="col-sm-1" type="button" data-original-title="" onclick="location.href='../xinter-v1.2/figurelock_new.php';" title="">Zu Freigaben</button>--><!--Nötig?-->
        </div>
        </div>
    </div>
	

	<!-- Latest compiled and minified JavaScript -->
<script src="bootstrap-3.3.7-dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
	<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>-->

<div onclick="document.getElementById('fav').style.display = 'none';" onmouseover="document.getElementById('content').style.display = 'inlne';" id="fav" style="display:none" class="col-sm-12">
	<div class="col-sm-12 favs">
		<div class="col-sm-3 vswifield">
	
		</div>
</div>
</div>
</body>
</html>
