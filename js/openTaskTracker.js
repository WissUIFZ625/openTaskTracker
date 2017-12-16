var scopetmp = {};
$(document).ready(function () {

	scopetmp = angular.element(document.body).scope();
   // scopetmp = angular.element(document.getElementById("thewrapper")).scope();




		flt = new Object();
		//flt.ssh_id=id;
		//flt.ssh_id=scopetmp.clicked_share_id;
		flt.outtype='json';
		flt.filtertype='email';
		scopetmp.getTasks();



    //scopetmp.getTasks();
});