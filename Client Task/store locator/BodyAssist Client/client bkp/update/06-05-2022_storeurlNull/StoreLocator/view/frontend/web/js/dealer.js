require(['jquery','Magento_Ui/js/modal/confirm'], function($,confirmation){ 
	$(document).ready(function($){
        $(".infotitle").on('click', function(){
        	$(this).addClass('active');
		    if(!$('.'+$(this).attr('id')).hasClass('show')){
		    	$('.'+$(this).attr('id')).addClass('show');
		    }else{
		    	$('.'+$(this).attr('id')).removeClass('show');
		    	$(this).removeClass('active');
		    }
		});

		$(".timeschedule").change(function() {
			
			var selectedid = jQuery(this).val();
			if(selectedid == 1){
				$('.'+$(this).attr('id')).addClass('show');
			}else{
				$('.'+$(this).attr('id')).removeClass('show');
			}
		});
		//$('select#country option:contains("Select Country")').text('').attr("selected", "selected");
		$("select#country option[value='']").val('Select Country');
		$("select#country option[value='Select Country']").text('---Select Country---').attr("selected", "selected");

		$(".btnsubmit").on('click', function(event){
		    
		    var sname = $('#sname').val();
		    var address = $('#add').val();
		    var city = $('#city').val();
		    var latitude = $('#latitude').val();
		    var longitude = $('#longitude').val();
		    var storeurl = $('#storeurl').val();
		    var email = $('#email').val();
		    var website = $('#website').val();
		    var country = $('select#country option:selected').val();
		    var error = 0;
		    var contactError = false;
		    var storecoordinates=false;
		    var addressError = false;
		    if (sname == '') {
				error++;
				$('#sname').addClass('error');
				$('.information').addClass('show');
				$('.error_msg_sname').text('Please Enert Store Name');
			} else {
				$('#sname').removeClass('error');
				$('.information').removeClass('show');
				$('.error_msg_sname').text('');
			}
			if (address == '') {
				error++;
				addressError = true;
				$('#add').addClass('error');
				$('.error_msg_add').text('Please Enter Store Address');
			} else {
				$('#add').removeClass('error');
				$('.error_msg_add').text('');
			}
			if (city == '') {
				error++;
				addressError = true; 
				$('#city').addClass('error');
				$('.error_msg_city').text('Please Enert City');
			} else {
				$('#city').removeClass('error');
				$('.error_msg_city').text('');
			}
			if(country == 'Select Country'){
				error++;
				addressError = true; 
				$('.error_msg_country').text('Please Select Country');
			}
			else{
				$('.error_msg_country').text('');
			}
			if(addressError == true){
				$('.address').addClass('show');
			}
			else{
				$('.address').removeClass('show');
			}

			if (latitude == '') {
				error++;
				storecoordinates = true;
				$('#latitude').addClass('error');
				$('.error_msg_latitude').text('Please Enert Store Latitude');
			} else {
				var LatitudePattern =/^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,15}/;
				if(!LatitudePattern.test(latitude)){
					storecoordinates = true;
					error++;
					$('#latitude').addClass('error');
					$('.error_msg_latitude').text('Please Enter Valid Latitude(Only Digit)');
				}
				if(LatitudePattern.test(latitude)){
					$('#latitude').removeClass('error');
					$('.error_msg_latitude').text('');	
				}
			}
			if (longitude == '') {
				error++;
				storecoordinates = true;
				$('#longitude').addClass('error');
				$('.error_msg_longitude').text('Please Enert Store Longitude');
			} else {
				var longitudePattern =/^-?((1?[0-7]?|[0-9]?)[0-9]|180)\.[0-9]{1,6}/;
				if(!longitudePattern.test(longitude)){
					error++;
					storecoordinates =true;
					$('#longitude').addClass('error');
					$('.error_msg_longitude').text('Please Enter Valid Longitude(Only Digit)');
				}
				if(longitudePattern.test(longitude)){
					$('#longitude').removeClass('error');
					$('.error_msg_longitude').text('');	
				}
			}
			if(storecoordinates == true){
				$('.storecoordinates').addClass('show');
			}
			else{
				$('.storecoordinates').removeClass('show');
			}
			if (website == '') {
				error++;
				contactError = true;
				$('#website').addClass('error');
				$('.error_msg_website').text('Please Enert Website');
			} else {
				$('#website').removeClass('error');
				$('.error_msg_website').text('');
			}
			if (storeurl == '') {
				error++;
				contactError = true;
				$('#storeurl').addClass('error');
				$('.error_msg_storeurl').text('Please Enert Store Url');
			}else{
				$('#storeurl').removeClass('error');
				$('.error_msg_storeurl').text('');	
			}
			if (email == '') {
				error++;
				contactError = true;
				$('#email').addClass('error');
				$('.error_msg_email').text('Please EnterStore Email');
			}else{
				var filter = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
				if (!filter.test(email)) {
					error++;
					$('#email').addClass('error');
					$('.error_msg_email').text('Please Enert Valid Email');
				} else {
					$('#email').removeClass('error');
					$('.error_msg_email').text('');
				}
			} 
			if(contactError == true){
				$('.contact').addClass('show');
			}
			else{
				$('.contact').removeClass('show');
			}
			if(error != 0){
				window.scrollTo(0, 0);
				event.preventDefault();
			}
		});

		$(".delete_store").click(function() {
	    	
	    	var deleteUrl = $(this).attr("value");
	    	//var tr = $(this).closest('tr');
		    confirmation({
		        title: $.mage.__('Delete'),
		        content: $.mage.__('Are you sure you want to delete this Store?'),
		        actions: {
		            confirm: function(){
		            	$.ajax({
		                    url: deleteUrl,
		                    showLoader: true,
		                    success: function (data) {
		                    	//tr.remove(); 
		                    	location.reload();
		                    	window.scrollTo(0, 0);  
		                    }
		                });
		            },
		            cancel: function(){
		           	
		            }
		        }
		    });
	    });  

		jQuery("input").keypress(function(){
			var inputid = jQuery(this).attr('id');
			$('.error_msg_'+inputid).text('');
			$('#'+inputid).removeClass('error');
		});

		var savecontry = $('#savecontry').val();
		if(savecontry != " "){
			jQuery('#country option[value='+savecontry+']').attr('selected','selected');
		}
		
    });
});