var ConfigSet = {};
var ConfigSetArr = new Array();
var alertDbg=false;

function LocationRight() {
}


LocationRight.NOACCESS = -1;
LocationRight.UNDEFINED = 0;
LocationRight._SWITCH = 1;
LocationRight.CONF = 31;

function VirtualNode() {
}

VirtualNode.DIRECTORY=10000;
VirtualNode.ROOT=10001;

VirtualNode.isVirtual=function (nodeId) {
	return((nodeId*1==this.DIRECTORY) || (nodeId*1==this.ROOT));
}

$(document).ready(function() {

	$(".lnk" ).click(function(e) {
		document.location.href=$(this).attr('id')+".php";

	});

	if(typeof lookup_urlmainnav == 'function')
	{
		lookup_urlmainnav();
	}
	$(".icon").click(function () {
	    $(".mobilenav").fadeToggle(500);
	    $(".top-menu").toggleClass("top-animate");
	    $(".mid-menu").toggleClass("mid-animate");
	    $(".bottom-menu").toggleClass("bottom-animate");
	 });

	$(".mcst").click(function() {
		window.location = $(this).attr("href");
		return false;
	});


  	$('[data-toggle="tooltip"]').tooltip({delay: {onShow: function () {

	    var self = this;
	    		setTimeout(function () {
	    				self.getTip().hide();
	    		}, 4000)
	  		}
		}
	});

	
	$('[data-toggle="tooltip"]').on('shown.bs.tooltip', function () {
		   setTimeout(function () {
		    $('[data-toggle="tooltip"]').tooltip('hide');
		   }, 800);
	});

	(function ($, window) {

		    $.fn.contextMenu = function (settings) {

		        return this.each(function () {

		            // Open context menu
		            $(this).on("contextmenu", function (e) {
		                // return native menu if pressing control
		                if (e.ctrlKey) return;

		                //open menu
		                var $menu = $(settings.menuSelector)
		                    .data("invokedOn", $(e.target))
		                    .show()
		                    .css({
		                        position: "absolute",
		                        left: getMenuPosition(e.clientX, 'width', 'scrollLeft'),
		                        top: getMenuPosition(e.clientY, 'height', 'scrollTop')
		                    })
		                    .off('click')
		                    .on('click', 'a', function (e) {
		                        $menu.hide();

		                        var $invokedOn = $menu.data("invokedOn");
		                        var $selectedMenu = $(e.target);

		                        settings.menuSelected.call(this, $invokedOn, $selectedMenu);
		                    });

		                return false;
		            });

		            //make sure menu closes on any click
		            $('body').click(function () {
		                $(settings.menuSelector).hide();
		            });
		        });

		        function getMenuPosition(mouse, direction, scrollDir) {
		            var win = $(window)[direction](),
		                scroll = $(window)[scrollDir](),
		                menu = $(settings.menuSelector)[direction](),
		                position = mouse + scroll;

		            // opening menu would pass the side of the page
		            if (mouse + menu > win && menu < mouse)
		                position -= menu;

		            return position;
		        }

		    };
		})(jQuery, window);




    $('.divm').tooltip({title: "", html: true, placement: "bottom"});




});



function sendConfigSet(){

	$.ajax({
  	  type: 'POST',
  	  url: 'syncweb/configset.php',
  	  data: {
  	     set: ConfigSetArr
  	  },
  	  success: function(data){
  		 ConfigSetArr.length = 0
  	  },
  	  error: function(xhr, textStatus, error){
  	      console.log(xhr.statusText);
  	      console.log(textStatus);
  	      console.log(error);
  	      ConfigSetArr.length = 0;
  	  }
  	});

}


function sendLocalConfigSet(){
	$("#savedlg").dialog( "open" );
	$.ajax({
  	  type: 'POST',
  	  url: 'syncweb/local_configset.php',
  	  data: {
  	     set: ConfigSetArr
  	  },
  	  success: function(data){

  		ConfigSetArr.length = 0
  		$("#savedlg").dialog( "close" );
  	  },
  	  error: function(xhr, textStatus, error){
  	      console.log(xhr.statusText);
  	      console.log(textStatus);
  	      console.log(error);
  	      ConfigSetArr.length = 0;
  	  }
  	});

}

function getxSnippetContent(id, target, filter)
{
    filter = (filter == undefined)?"":filter;

    $.ajax({
	  	  type: 'POST',
	  	  url: "ajax/xsnippet.php",
		  data : {
			  snippet_id : id,
			  filter_str:  filter
		  },
		  beforeSend: function(){

				 $("#savedlg").dialog("open");
			},
	  	  complete: function(data){

	  		  $(target).html(data.responseText);
			  Alert(data.responseText);
			$("#savedlg").dialog("close");
	  	  },
	  	  error: function(xhr, textStatus, error){

	  	      console.log(xhr.statusText);
	  	      console.log(textStatus);
	  	      console.log(error);

	  	  }
	 });

}


function getSnippetContent(id, target, filter)
{
    filter = (filter == undefined)?"":filter;
	if(id != 'WebSwitchCollection')
	{
		if(typeof filter.lid !=='undefined')
		{
			filter.lid = filter.lid.toString();
			//if(filter.lid.indexOf("|") != -1)
			//{
			//	filter.lid = filter.lid.split[0];
			//}
		}
	}
    $.ajax({
	  	  type: 'POST',
	  	  url: "ajax/getsnippet.php",
		  data : {
			  snippet_id : id,
			  filter_str:  filter
		  },
	  	  success: function(data){

	  		  $(target).html(data);

	  	  },
	  	  error: function(xhr, textStatus, error){

	  	      console.log(xhr.statusText);
	  	      console.log(textStatus);
	  	      console.log(error);

	  	  }
	 });

}

function getSnippetContentRooms(id, target, filter)
{
    filter = (filter == undefined)?"":filter;
	if(id != 'WebSwitchCollection')
	{
		if(typeof filter.lid !=='undefined')
		{
			filter.lid = filter.lid.toString();
			//if(filter.lid.indexOf("|") != -1)
			//{
			//	filter.lid = filter.lid.split[0];
			//}
		}
	}

    $.ajax({
	  	  type: 'POST',
	  	  url: "ajax/getsnippet.php",
		  cache: false,
		  data : {
			  snippet_id : id,
			  filter_str:  filter
		  },
	  	  success: function(data){

	  		  //$(target).html(data);

	  	  },
	  	  error: function(xhr, textStatus, error){

	  	      console.log(xhr.statusText);
	  	      console.log(textStatus);
	  	      console.log(error);

	  	  }
	 }).done(function(result)
		{
			$(target).html(result);
		}
	);
}


function getSnippetContentReturn(id, target, filter, cbfunction)
{
    filter = (filter == undefined)?"":filter;
	if(id != 'WebSwitchCollection')
	{
		if(typeof filter.lid !=='undefined')
		{
			filter.lid = filter.lid.toString();
		}
	}
    $.ajax({
	  	  type: 'POST',
	  	  url: "ajax/getsnippet.php",
		  data : {
			  snippet_id : id,
			  filter_str:  filter
		  },
	  	  success: function(data){
/*			// target optional machen?
			//target wird nur beachtet, wenn es gesetzt ist
			if(target)
			{
	  		  $(target).html(data);
			}
			  //Callbackfunctionen als Array �bergeben, damit mehrere Funktionen ausgef�hrt werden k�nnen
			  cbfunction(data);*/

	  	  },
	  	  error: function(xhr, textStatus, error){

	  	      console.log(xhr.statusText);
	  	      console.log(textStatus);
	  	      console.log(error);

	  	  }
	 }).done(function(result)
    {
        // target optional machen?
        //target wird nur beachtet, wenn es gesetzt ist
        if(target)
        {
            $(target).html(result);
        }
        //Callbackfunctionen als Array �bergeben, damit mehrere Funktionen ausgef�hrt werden k�nnen
        cbfunction(data);
    });

}
function getSnippetContentRefresh(id, target, filter, cbfunction)
{
    filter = (filter == undefined)?"":filter;
    if(id != 'WebSwitchCollection')
    {
        if(typeof filter.lid !=='undefined')
        {
            filter.lid = filter.lid.toString();
        }
    }
    $.ajax({
        type: 'POST',
        url: "ajax/getsnippet.php",
        data : {
            snippet_id : id,
            filter_str:  filter
        },
        success: function(data){
/*            // target optional machen?
            //target wird nur beachtet, wenn es gesetzt ist
            if(target)
            {
                $(target).html(data);
            }
            //Callbackfunctionen als Array �bergeben, damit mehrere Funktionen ausgef�hrt werden k�nnen
            cbfunction(data);*/

        },
        error: function(xhr, textStatus, error){

            console.log(xhr.statusText);
            console.log(textStatus);
            console.log(error);

        }
    }).done(function(result)
    {
        // target optional machen?
        //target wird nur beachtet, wenn es gesetzt ist
        if(target)
        {
            $(target).html(result);
        }
        //Callbackfunctionen als Array �bergeben, damit mehrere Funktionen ausgef�hrt werden k�nnen
        /*cbfunction(data);*/
        cbfunction();
    });

}
function getSnippetUrl(id, target, filter)
{
    filter = (filter == undefined)?"":filter;
    if(id != 'WebSwitchCollection')
    {
        if(typeof filter.lid !=='undefined')
        {
            filter.lid = filter.lid.toString();
            //if(filter.lid.indexOf("|") != -1)
            //{
            //	filter.lid = filter.lid.split[0];
            //}
        }
    }
    $.ajax({
        type: 'POST',
        url: "ajax/getsnippet.php",
        data : {
            snippet_id : id,
            filter_str:  filter
        },
        success: function(data){

          scopetmp.customButton.link=data.trim();

        },
        error: function(xhr, textStatus, error){

            console.log(xhr.statusText);
            console.log(textStatus);
            console.log(error);

        }
    });

}

function getJsonContent(id,  filter)
{
    filter = (filter == undefined)?"":filter;
    $.ajax({
	  	  type: 'POST',
	  	  url: "ajax/getjsondata.php",
		  data : {
			  target_id : id,
			  filter_str:  filter
		  },
	  	  success: function(data){
	  		 return data;
	  	  },
	  	  error: function(xhr, textStatus, error){
	  		  
	  	      console.log(xhr.statusText);
	  	      console.log(textStatus);
	  	      console.log(error);
	  		  	      
	  	  }
	 });
     
}


function refreshSelectContextTree(virtoff, request_permission, cntx){
	
	var confobj = new Object();
	confobj.vrtoff= virtoff;
	confobj.rprm= request_permission;
	
	$.ajax({
  	  type: 'POST',
  	  url: 'ajax/getselectlocation.php',
  	  data: {
  		ctxdata: confobj
  	  },
  	  success: function(res){
  	  
  		cntx.$apply(function () {
  			cntx.treeNodes=eval(res);
	    });
  		
  	  },
  	  error: function(xhr, textStatus, error){
  		  
  	      console.log(xhr.statusText);
  	      console.log(textStatus);
  	      console.log(error);

  	      ConfigSetArr.length = 0;
  	  }
  	});
}


function getCurrentContext(cntx){

	$.ajax({
  	  type: 'POST',
  	  url: 'ajax/getdefaultusrcntx.php',
  	  data: {
  		
  	  },
  	  success: function(res){
  	  
  		cntx.$apply(function () {
  			
  			cntx.currCntx=eval('(' + res + ')');
			
	    });
  		
  	  },
  	  error: function(xhr, textStatus, error){
  		  
  	      console.log(xhr.statusText);
  	      console.log(textStatus);
  	      console.log(error);
		  return -1;
  	      //ConfigSetArr.length = 0;
  	  }
  	});
	return cntx.currCntx;
}


function  isTemperatureSensor( type ){

	var is_type=false;
	
	if( (type>=1500 && type<=1550) ||  (type>=1450 && type<=1460) || type==503 ){
		
		is_type=true;
		
	}
	
	return is_type;
}


function  isSocket( type ){

	var is_type=false;

	if( type==1000 ){

		is_type=true;

	}

	return is_type;
}



function  isMotionSensor( type ){

	var is_type=false;

	if( type>=400 && type<=450 ){

		is_type=true;

	}

	return is_type;
}


function  isMasterSensor( type ){

	var is_type=false;

	if( type==502 || type==504 ||  type==100){

		is_type=true;

	}

	return is_type;
}

function  isHumitidySensor( type ){

	var is_type=false;

	if( (type==455 || type==501 ) ){

		is_type=true;

	}

	return is_type;
}

function  isBrigthnessSensor( type ){

	is_type=false;
	
	if( (type==455 || type==400) ){
		
		is_type=true;

	}
	return is_type;
}
	
function  isSensor( type ){

		var is_sensor=(isBrigthnessSensor(type) || isHumitidySensor(type) || isMotionSensor(type) || isTemperatureSensor(type) || isMasterSensor(type));

		return is_sensor;
	
}

function  isEnoSwitch( type ){

	var is_sensor=(type==300 || type==301 || type==302 || type==303);

	return is_sensor;

}

function Alert(a){
	
	if(alertDbg){
		alert(a);
	}
	
}


function sleep(milliseconds) {
	 var start = new Date().getTime();
	 for (var i = 0; i < 100000000; i++) {
	   if ((new Date().getTime() - start) > milliseconds){
	     
		   break;
	   }
	 }
}


function convertIpToDecimal(ip) {
        // a not-perfect regex for checking a valid ip address
	// It checks for (1) 4 numbers between 0 and 3 digits each separated by dots (IPv4)
	// or (2) 6 numbers between 0 and 3 digits each separated by dots (IPv6)
	var ipAddressRegEx = /^(\d{0,3}\.){3}.(\d{0,3})$|^(\d{0,3}\.){5}.(\d{0,3})$/;
	var valid = ipAddressRegEx.test(ip);
	if (!valid) {
		return false;
	}
	var dots = ip.split('.');
	// make sure each value is between 0 and 255
	for (var i = 0; i < dots.length; i++) {
		var dot = dots[i];
		if (dot > 255 || dot < 0) {
			return false;
		}
	}
	if (dots.length == 4) {
		// IPv4
		return ((((((+dots[0])*256)+(+dots[1]))*256)+(+dots[2]))*256)+(+dots[3]);
	} else if (dots.length == 6) {
		// IPv6
		return ((((((((+dots[0])*256)+(+dots[1]))*256)+(+dots[2]))*256)+(+dots[3])*256)+(+dots[4])*256)+(+dots[5]);
	}
	return false;
}



function isNumeric(n) {
	  return !isNaN(parseFloat(n)) && isFinite(n);
	}

function validateEmail(email) {
	  var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	  return re.test(email);
	}
	
	

function parse_json(data)
{
        var json_out;
        try
        {
                //Normal Json
                json_out = $.parseJSON(data);
        }
        catch(err)
        {
                //Angular Json
                try
                {
                        json_out = data[Object.keys(data)[0]];
                }
                catch(term_err)
                {
                        throw term_err;
                }
        }
    return json_out;
}

