$(document).ready(function () {
   $( "#password" ).on( "keypress", function(event) {
      if(event.which == 13)
      {
         formhash(this.form, this.form.password);
      }
         
    });
});