/***Create a object to hold selectbox categories values.****/
var WIKI = WIKI || {}; 
WIKI.Views = WIKI.Views || {};
WIKI.Logs = WIKI.Logs || {};

 
WIKI.App = (function(){

            /***Initialize Categories & Toggle Form***/
            var init = function(){
	              this.form = new WIKI.Views.toggleForm();
	              this.form.hideShowForm();    

	              this.fetchCategories = new WIKI.Views.getCategory();
	              this.fetchCategories.showCategory();          
            };

           
             return {
             	init:init
             }

})();

 /***Give our functions access to jQuery ***/
(function($){
  WIKI.Views.toggleForm = function(){}

  WIKI.Views.toggleForm.prototype = {
             hideShowForm:function(){
                  setInterval(function(){

	                 	  if(document.getElementById("show_data")){
	                 	     $('#searching').html("");
	                 	     $('#submit').show();

	                 	}
                 		
                   },4000);

                  $('#form').submit(function(e){
                	       $('#submit').hide();
                 	       $('#searching').html("Searching Category Articles..Please Wait");

                 });
          }
  }

 /***Get categories json file ***/
  WIKI.Views.getCategory = function(){
  	this.url = "./js/categories.json";
  }

 /***Show categories ***/
  WIKI.Views.getCategory.prototype = {
  	  showCategory:function(){

  	  	  $.ajax({
  	  	  	url:this.url,
  	  	  	type:"GET",
  	  	  	contentType:"application/json",
  	  	  	success:function(response){

                 $.each(response,function(key,value){
                 	  

                      var options = document.createElement("option");
                      options.className = "categories";
                      options.value = value.category;
                      options.innerHTML  = value.category;
                      document.getElementById("categories").appendChild(options);
                 });


  	  	  	},
  	  	  	error:function(response){
  	  	  		
  	  	  		WIKI.Logs.log(response);
  	  	  	}
  	  	  })
  	  }
  }


})(jQuery);


WIKI.Logs.log = function(message){
	console.log(message);
}


