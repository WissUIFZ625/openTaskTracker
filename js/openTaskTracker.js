var scopetmp = {};
$(document).ready(function () {

	scopetmp = angular.element(document.body).scope();
   // scopetmp = angular.element(document.getElementById("thewrapper")).scope();

    $(document).on("click", "#notLogedInok", function ()
    {
        window.location.replace("login_inter.php");//Aufgerufene Seite ist mit zurück nicht abrufbar
        //window.location.href = "login.php";//Aufgerufene Seite ist mit zurück abrufbar

    });

    $("body").on("click", "#mod_change_admin_pw", function(e) {

        $("#inp_old_adminpw").val("");
        $("#inp_adminpw").val("");
        $("#inp_adminpwcomp").val("");
        $("#inp_adminpw_rep_same").css("display", "none");
        // $('#modal_change_pw').modal('show');
        scopetmp.showChangeAdminPWDialog();


    });


    $("body").on("click", "#change_email_inter", function(e) {

        $("#inp_old_email").val(scopetmp.customerdata.email);
        $("#inp_newemail").val("");
        $("#inp_newemailcomp").val("");
        $("#inp_adminpw_rep_same").css("display", "none");
        // $('#modal_change_pw').modal('show');
        scopetmp.showChangeEmailDialog();


    });

    //PW is the same//
    $('body').on('input', '#inp_adminpw', function()
    {
        if($("#inp_adminpw").val() === $("#inp_adminpwcomp").val())
        {
            $("#inp_adminpw_rep_same").css("display", "none");
            $("#save_admin_pw").prop('disabled', false);
            //$("#shh_confirm_code_same").css("display", "none");
        }
        else
        {
            $("#inp_adminpw_rep_same").css("display", "block");
            $("#save_admin_pw").prop('disabled', true);
        }
    });

    //PW is the same//Repetition
    $('body').on('input', '#inp_adminpwcomp', function()
    {
        if($("#inp_adminpw").val() === $("#inp_adminpwcomp").val())
        {
            $("#inp_adminpw_rep_same").css("display", "none");
            $("#save_admin_pw").prop('disabled', false);
            //$("#shh_confirm_code_same").css("display", "none");
        }
        else
        {
            $("#inp_adminpw_rep_same").css("display", "block");
            $("#save_admin_pw").prop('disabled', true);
        }
    });

    $("body").on("click", "#save_admin_pw", function(e) {
        $("#show_admin_pw_change").html('');

        var pw_old=$("#inp_old_adminpw").val();
        var pw=$("#inp_adminpw").val();
        var pw_confirm=$("#inp_adminpwcomp").val();


        // if((typeof pw_old !== 'undefined' || pw_old !== null) &&(typeof pw !== 'undefined' || pw !== null)  && (typeof pw_confirm !== 'undefined' || pw_confirm !== null ))
        if(pw_old && pw && pw_confirm )
        {
            if(pw == pw_confirm )
            {
                //AJAX
                $.ajax({
                    type: 'POST',
                    url: 'ajax_inter/pw_user_change_inter.php',
                    data: {
                        pw_old: hex_sha512(pw_old),
                        pw: hex_sha512(pw),
                        pwconfirm: hex_sha512(pw_confirm)
                    },
                    success: function(data){

                    },
                    error: function(xhr, textStatus, error){
                        console.log(xhr.statusText);
                        //alert(xhr.statusText);
                        console.log(textStatus);
                        console.log(error);
                    }

                }).done(function(result)
                {
                    if(result=="true")
                    {
                        scopetmp.pwchangesuccsess();
                        //$("#show_admin_pw_change").html('<span id="show_admin_pw_change_span" style="color:red;"  data-ng-init="welcometoopenTaskTracker()">Passwort wurde erfolgreich geändert. Sie werden in kürze ausgeloggt.</span>');
                        setTimeout(hideAdminPwChange, 5000);
                    }
                    else if (result=="oldpwfail")
                    {
                        scopetmp.pwchangeoldpw();
                        //$("#show_admin_pw_change").html('<span id="show_admin_pw_change_span" style="color:red;">Adminpasswort konnte nicht geändert werden</span>');
                    }
                    else
                    {
                        scopetmp.pwchangefail();
                       // $("#show_admin_pw_change").html('<span id="show_admin_pw_change_span" style="color:red;">Adminpasswort konnte nicht geändert werden</span>');
                    }
                });
            }
        }
        scopetmp.pwchangenotempty();
    });

    function hideAdminPwChange()
    {
        //$( "#show_admin_pw_change_span" ).fadeOut( "slow" );
        //setTimeout(cleanAdminPwChange, 1000);
        $.ajax({
            type: 'POST',
            url: 'include_inter/process_logout_inter.php',
            success: function(data){

            },
            error: function(xhr, textStatus, error){
                console.log(xhr.statusText);
                console.log(textStatus);
                console.log(error);
            }

        }).done(function(result)
        {
            window.location="../index.php";

        });
    }

    $("body").on("click", "#save_email", function(e) {


        var email_old=$("#inp_old_email").val();
        var email_new=$("#inp_new_email").val();
        var email_confirm=$("#inp_emailcomp").val();


        // if((typeof pw_old !== 'undefined' || pw_old !== null) &&(typeof pw !== 'undefined' || pw !== null)  && (typeof pw_confirm !== 'undefined' || pw_confirm !== null ))
        if(email_old && email_new && email_confirm )
        {
            if(email_new == email_confirm )
            {
                //AJAX
                $.ajax({
                    type: 'POST',
                    url: 'ajax_inter/email_user_change_inter.php',
                    data: {
                        email_old: email_old,
                        email_new: email_new,
                        email_confirm: email_confirm
                    },
                    success: function(data){

                    },
                    error: function(xhr, textStatus, error){
                        console.log(xhr.statusText);
                        console.log(textStatus);
                        console.log(error);
                    }

                }).done(function(result)
                {
                    if(result=="true")
                    {
                       scopetmp.emailsentsuccsess();
                        setTimeout(hideAdminPwChange, 10000);

                    }
                    else if (result=="oldpwfail")
                    {
                        //scopetmp.pwchangeoldpw();
                        //$("#show_admin_pw_change").html('<span id="show_admin_pw_change_span" style="color:red;">Adminpasswort konnte nicht geändert werden</span>');
                    }
                    else
                    {
                        //scopetmp.pwchangefail();
                        // $("#show_admin_pw_change").html('<span id="show_admin_pw_change_span" style="color:red;">Adminpasswort konnte nicht geändert werden</span>');
                    }
                });
            }else{
            scopetmp.emailnotident();
            }
        }else{
        scopetmp.emailchangenotempty();
        }
    });


	if($("#notLogedIn").length)
	{
		scopetmp.showNotLogedInAlertDlg();
	}
		flt = new Object();
		//flt.ssh_id=id;
		//flt.ssh_id=scopetmp.clicked_share_id;
		flt.outtype='json';
		flt.filtertype='email';
		scopetmp.getCustomerData();


//	    $.ajax({
//		  type: 'POST',
//		  url: 'ajax/edit_get_share_item.php',
//                  dataType: "json",
//		  data: {
//			 item_id: scopetmp.clicked_share_id,
//                         item_type:scopetmp.clicked_share_type,
//                         is_angular_json: "true"
//		  },
//		  success: function(data){
//                        
//		  },
//		  error: function(xhr, textStatus, error){
//			  console.log(xhr.statusText);
//			  console.log(textStatus);
//			  console.log(error);
//                  }
//
//		  }).done(function(result)
//                        {
//
//                        });


});