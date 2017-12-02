var openTaskTracker_App = angular.module('openTaskTracker_App', ['ngMaterial','dndLists']);
//var baseUrl = 'http://localhost:54037/Home/'

var mytimer;


openTaskTracker_App.controller('openTaskTracker_Ctrl', function ($scope, $http, $mdDialog)
{
    $scope.models = {
        selected: null,
        lists: {"A": [{
                "label": "Item A1"
            },

                {
                    "label": "Item A2"
                },
                {
                    "label": "Item A3"
                }], "B": [{
                "label": "Item B1"
            },
                {
                    "label": "Item B2"
                },
                {
                    "label": "Item B3"
                }]}
    };


    $scope.getCustomerData = function () {
        // use $.param jQuery function to serialize data from JSON
        var data = $.param({
            target_id: "CustomerDataCollection_inter", //target_id: "WebSwitchCollection"
            filter_str: flt
        });

        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };

        $http.post('ajax_inter/getjsondata_inter.php', data, config)
            .success(function (response, status, headers, config) {

                $scope.customerdata = response.customerdata;
                //$scope.$apply(function ()
                //{
                //////////$scope.shh_maildata = response.shh_maildata;
                //});

            })
            .error(function (data, status, header, config) {
                //alert(data);
            });
    };


    $scope.closeDialog = function () {
        $mdDialog.hide();
    };

    // Generate initial model
    for (var i = 1; i <= 3; ++i) {
        $scope.models.lists.A.push({label: "Item A" + i});
        $scope.models.lists.B.push({label: "Item B" + i});
    }

    // Model to JSON for demo purpose
    $scope.$watch('models', function(model) {
        $scope.modelAsJson = angular.toJson(model, true);
    }, true);


	$scope.getCustomerData = function() {
	    	 // use $.param jQuery function to serialize data from JSON
	         var data = $.param({
				 target_id: "CustomerDataCollection_inter", //target_id: "WebSwitchCollection"
	             filter_str: flt
	         });

	         var config = {
	             headers : {
	                 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
	             }
	         };

	         $http.post('ajax_inter/getjsondata_inter.php', data, config)
	         .success(function (response, status, headers, config) {

			 $scope.customerdata = response.customerdata;
				//$scope.$apply(function ()
				//{
					//////////$scope.shh_maildata = response.shh_maildata;
				//});

	         })
	         .error(function (data, status, header, config) {
	        //alert(data);
	         });
	};


	$scope.closeDialog = function()
	{
		$mdDialog.hide();
	};


    $scope.showNewProjectDialog = function () {
        $mdDialog.show({
            contentElement: '#newProject',
            parent: angular.element(document.body),
            clickOutsideToClose: true,
            escapeToClose: true
        });
    };

    $scope.showNewTaskDialog = function () {
        $mdDialog.show({
            contentElement: '#newProject',
            parent: angular.element(document.body),
            clickOutsideToClose: true,
            escapeToClose: true
        });
    };

    $scope.showChangeEmailDialog = function () {
        $mdDialog.show({
            contentElement: '#change_email_address_inter',
            parent: angular.element(document.body),
            clickOutsideToClose: true,
            escapeToClose: true
        });
    };

    $scope.showNoPIHashAlert = function (ev) {
        // Appending dialog to document.body to cover sidenav in docs app
        // Modal dialogs should fully cover application
        // to prevent interaction outside of dialog
        $mdDialog.show(
            $mdDialog.alert()
                .parent(angular.element(document.querySelector('#document.body')))
                .clickOutsideToClose(true)
                .title('Kein Identifikationscode')
                .textContent('Kein lokaler Identifikationscode gefunden')
                .ariaLabel('Alert Kein Identifikationscode')
                .ok('OK')
                .targetEvent(ev)
        );
    };


    $scope.showNotLogedInAlertDlg = function () {
        $mdDialog.show({
            contentElement: '#alertNotLogedInDialog',
            parent: angular.element(document.body),
            clickOutsideToClose: false,
            escapeToClose: false
        });
    };


    $scope.chk_hash_redirect = function (hashcode) {
        var url = hashcode;

        if ($scope.customerdata.ident.identify_hash !== -1 && $scope.customerdata.ident.identify_hash !== null && typeof $scope.customerdata.ident.identify_hash != 'undefined') {

            $.ajax({
                type: 'POST',
                url: 'ajax_inter/chk_pihash_is_vali.php',
                data: {
                    idhash: $scope.customerdata.ident.identify_hash[0]
                },
                success: function (data) {

                },
                error: function (xhr, textStatus, error) {
                    console.log(xhr.statusText);
                    console.log(textStatus);
                    console.log(error);
                }

            }).done(function (result) {
                if (result === 'true') {
                    //scopetmp.customerdata.ident_hash=result;

                    $.ajax({
                        type: 'POST',
                        url: 'ajax_inter/destroy_session.php',
                        data: {},
                        success: function (data) {

                        },
                        error: function (xhr, textStatus, error) {
                            console.log(xhr.statusText);
                            console.log(textStatus);
                            console.log(error);
                        }

                    }).done(function (result) {
                        //if(result ==='true')
                        //{
                        //scopetmp.customerdata.ident_hash=result;
                        window.location.href = url/*+'?pi_id_hash='/$scope.customerdata.ident.identify_hash*/;
                        /*window.open (url)/!*+'?pi_id_hash='/$scope.customerdata.ident.identify_hash*!/;*/
                        //}
                        //else
                        //{
                        //	scopetmp.showNoPIHashAlert();
                        //}
                        //scopetmp.customerdata.ident_hash=result;
                        //alert(result);
                    });

                    //window.location.href =url+'?pi_id_hash='+scopetmp.customerdata.ident_hash;
                }
                else {
                    $scope.showNoPIHashAlert();
                }
                //scopetmp.customerdata.ident_hash=result;
                //alert(result);
            });


            //window.location.href =url+'?pi_id_hash='+scopetmp.customerdata.ident_hash;
        }
        else {
            $scope.showNoPIHashAlert();
        }
    };
    $scope.send_mail_again = function (hashcode) {
        var confirm = $mdDialog.confirm()
            .title('Keine E-Mail erhalten')
            .textContent('Wollen Sie sich erneut eine E-Mail zustellen?')
            .ariaLabel('Lucky day')
            /*.targetEvent(ev)*/
            .ok('Ja')
            .cancel('Nein');

        $mdDialog.show(confirm).then(function () {
            var hashformail = hashcode;

            $.ajax({
                type: 'POST',
                url: 'ajax_inter/send_dbvali_mail_inter_again.php',
                data: {
                    ident_hash: hashformail
                },
                success: function (data) {

                },
                error: function (xhr, textStatus, error) {
                    console.log(xhr.statusText);
                    console.log(textStatus);
                    console.log(error);
                }

            }).done(function (result) {
                //$scope.customerdata.ident_hash=result;
                //alert(result);
                flt = new Object();
                //flt.ssh_id=id;
                //flt.ssh_id=scopetmp.clicked_share_id;
                flt.outtype = 'json';
                flt.filtertype = 'email';
                $scope.getCustomerData();

                mytimer = setInterval(function () {
                    flt = new Object();
                    //flt.ssh_id=id;
                    //flt.ssh_id=scopetmp.clicked_share_id;
                    flt.outtype = 'json';
                    flt.filtertype = 'email';
                    scopetmp.getCustomerData();
                }, 1000);
            });
        }, function () {


        });

    };

    $scope.clear_device_dlg = function (hashcode) {
        var confirm = $mdDialog.confirm()
            .title('Wollen Sie Ihr Gerät wirklich entfernen?')
            .textContent('Wenn Sie dies bestätigen, haben Sie keinen Zugriff mehr zum internationalen Schalten')
            .ariaLabel('Lucky day')
            /*.targetEvent(ev)*/
            .ok('OK')
            .cancel('Abbrechen');

        $mdDialog.show(confirm).then(function () {

            var hashformail = hashcode;
            $.ajax({
                type: 'POST',
                url: 'ajax_inter/clear_device_inter.php',
                data: {
                    ident_hash: hashformail
                },
                success: function (data) {

                },
                error: function (xhr, textStatus, error) {
                    console.log(xhr.statusText);
                    console.log(textStatus);
                    console.log(error);
                }

            }).done(function (result) {
                //$scope.customerdata.ident_hash=result;
                //alert(result);
                flt = new Object();
                //flt.ssh_id=id;
                //flt.ssh_id=scopetmp.clicked_share_id;
                flt.outtype = 'json';
                flt.filtertype = 'email';
                $scope.getCustomerData();
            });
        }, function () {


        });

    };
    $scope.test = function () {
        alert("Work in Progress");
    }

    $scope.send_idhash_cmail = function () {

        $mdDialog.show(
            $mdDialog.alert()

                .parent(angular.element(document.querySelector('#popupContainer')))
                .clickOutsideToClose(true)
                .title('E-Mail Bestätigen')
                .textContent('Es wurde eine E-Mail an die registrierte Adresse zugestellt.' +
                    '\r\nDas versenden der E-Mail kann je nach Mailserver bis zu 5 Minuten dauern.' +
                    '\r\nÜberprüfen Sie auch Ihren Spam und Junk Mail Ordner.')
                .ariaLabel('Alert Dialog Demo')
                .ok('OK')
        );

        $.ajax({
            type: 'POST',
            url: 'ajax_inter/send_dbvali_mail_inter.php',
            data: {
                ident_hash: $scope.new_ident_hash
            },
            success: function (data) {

            },
            error: function (xhr, textStatus, error) {
                console.log(xhr.statusText);
                console.log(textStatus);
                console.log(error);
            }

        }).done(function (result) {
            //$scope.customerdata.ident_hash=result;
            //alert(result);
            flt = new Object();
            //flt.ssh_id=id;
            //flt.ssh_id=scopetmp.clicked_share_id;
            flt.outtype = 'json';
            flt.filtertype = 'email';
            $scope.getCustomerData();
            $scope.new_ident_hash = "";

            mytimer = setInterval(function () {
                flt = new Object();
                //flt.ssh_id=id;
                //flt.ssh_id=scopetmp.clicked_share_id;
                flt.outtype = 'json';
                flt.filtertype = 'email';
                scopetmp.getCustomerData();
            }, 1000);

        });
    }
    $scope.update_option_device = function (hashcode, name, ort, select_land) {
        var confirm = $mdDialog.confirm()
            .title('Wollen Sie die Änderungen speichern?')
            .textContent('Der Name wird Ihnen später beim internationalen Schalten angezeigt.')
            .ariaLabel('Lucky day')
            /*.targetEvent(ev)*/
            .ok('OK')
            .cancel('Abbrechen');

        $mdDialog.show(confirm).then(function () {

            var hashformail = hashcode;
            var namerp = name;
            var standort = ort;
            var land = select_land;
            $.ajax({
                type: 'POST',
                url: 'ajax_inter/update_option_devices_inter.php',
                data: {
                    ident_hash: hashformail,
                    land: land,
                    standort: standort,
                    namerp: namerp
                },
                success: function (data) {

                },
                error: function (xhr, textStatus, error) {
                    console.log(xhr.statusText);
                    console.log(textStatus);
                    console.log(error);
                }

            }).done(function (result) {
                //$scope.customerdata.ident_hash=result;
                //alert(result);
                flt = new Object();
                //flt.ssh_id=id;
                //flt.ssh_id=scopetmp.clicked_share_id;
                flt.outtype = 'json';
                flt.filtertype = 'email';
                $scope.getCustomerData();
            });
        }, function () {


        });

    };
    $scope.update_userdata = function (name_u, surname_u, email_u, adress_u, plz_u, city_u, tel_u) {
        var confirm = $mdDialog.confirm()
            .title('Wollen Sie die geänderten Userdaten speichern?')
            .textContent('Die E-Mail Adresse können Sie zur Zeit nicht ändern.')
            .ariaLabel('Lucky day')
            /*.targetEvent(ev)*/
            .ok('OK')
            .cancel('Abbrechen');

        $mdDialog.show(confirm).then(function () {

            var name = name_u;
            var surname = surname_u;
            var email = email_u;
            var adresse = adress_u;
            var plz = plz_u;
            var city = city_u;
            var telefon = tel_u;
            $.ajax({
                type: 'POST',
                url: 'ajax_inter/update_userdata_inter.php',
                data: {
                    name: name,
                    surname: surname,
                    email: email,
                    adresse: adresse,
                    plz: plz,
                    city: city,
                    telephone: telefon,
                },
                success: function (data) {

                },
                error: function (xhr, textStatus, error) {
                    console.log(xhr.statusText);
                    console.log(textStatus);
                    console.log(error);
                }

            }).done(function (result) {
                //$scope.customerdata.ident_hash=result;
                //alert(result);
                flt = new Object();
                //flt.ssh_id=id;
                //flt.ssh_id=scopetmp.clicked_share_id;
                flt.outtype = 'json';
                flt.filtertype = 'email';
                $scope.getCustomerData();
            });
        }, function () {


        });

    };

    $scope.clear_Interval_Time = function (hashcode) {

        if (hashcode == null) {
            return true
        } else {
            clearInterval(mytimer);
            return true;
        }
    };


    $scope.insert_hash_from_clipboard = function () {

        //$scope.new_ident_hash="";
        myeditor = new Object();
        myeditor = document.getElementById("new_ident_hash");
        myeditor.focus();
        //editor.select();

        $scope.$apply(function () {
            $scope.execCommand('paste');
        });

    };

    $scope.welcometoopenTaskTracker = function () {
        $mdDialog.show(
            $mdDialog.alert()
                .parent(angular.element(document.querySelector('#popupContainer')))
                .clickOutsideToClose(true)
                .title('Herzlichen Willkommen bei openTaskTracker Online')
                .textContent('Bitte klicken Sie auf den orangen Button.')
                .ariaLabel('Alert Dialog Demo')
                .ok('Los gehts')
        );
    };

    $scope.pwchangesuccsess = function () {
        $mdDialog.show(
            $mdDialog.alert()
                .parent(angular.element(document.querySelector('#popupContainer')))
                .clickOutsideToClose(true)
                .title('Passwort wurde erfolgreich geändert')
                .textContent('Sie werden in Kürze ausgeloggt!')
                .ariaLabel('Alert Dialog Demo')
                .ok('OK')
        );
    };

    $scope.pwchangeoldpw = function () {
        $mdDialog.show(
            $mdDialog.alert()
                .parent(angular.element(document.querySelector('#popupContainer')))
                .clickOutsideToClose(true)
                .title('Passwort wurde nicht geändert')
                .textContent('Ihr altes Passwort stimmt nicht überein.')
                .ariaLabel('Alert Dialog Demo')
                .ok('OK')
        );
    };

    $scope.pwchangefail = function () {
        $mdDialog.show(
            $mdDialog.alert()
                .parent(angular.element(document.querySelector('#popupContainer')))
                .clickOutsideToClose(true)
                .title('Passwort konnte nicht geändert werden.')
                .textContent('Es ist ein Fehler aufgetreten.')
                .ariaLabel('Alert Dialog Demo')
                .ok('OK')
        );
    };

    $scope.pwchangenotempty = function () {
        $mdDialog.show(
            $mdDialog.alert()
                .parent(angular.element(document.querySelector('#popupContainer')))
                .clickOutsideToClose(true)
                .title('Passwort konnte nicht geändert werden.')
                .textContent('Sie können kein leeres Passwort speichern.')
                .ariaLabel('Alert Dialog Demo')
                .ok('OK')
        );
    };

    $scope.emailchangenotempty = function () {
        $mdDialog.show(
            $mdDialog.alert()
                .parent(angular.element(document.querySelector('#popupContainer')))
                .clickOutsideToClose(true)
                .title('E-Mailadresse konnte nicht geändert werden')
                .textContent('Es ist keine neue E-Mailadresse eingetragen.')
                .ariaLabel('Alert Dialog Demo')
                .ok('OK')
        );
    };

    $scope.emailnotident = function () {
        $mdDialog.show(
            $mdDialog.alert()
                .parent(angular.element(document.querySelector('#popupContainer')))
                .clickOutsideToClose(true)
                .title('E-Mailadresse konnte nicht geändert werden')
                .textContent('E-Mailadressen sind nicht identisch.')
                .ariaLabel('Alert Dialog Demo')
                .ok('OK')
        );
    };

    $scope.emailsentsuccsess = function () {
        $mdDialog.show(
            $mdDialog.alert()
                .parent(angular.element(document.querySelector('#popupContainer')))
                .clickOutsideToClose(true)
                .title('Es wurde eine E-Mail an die neue Adresse versendet.')
                .textContent('Bestätigen Sie diese E-Mail indem Sie dem Link folgen. Sie werden in kürze  ausgeloggt.')
                .ariaLabel('Alert Dialog Demo')
                .ok('OK')
        );
    };
});
