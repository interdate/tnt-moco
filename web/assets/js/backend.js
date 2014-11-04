$(document).ready(
	function(){
		
		
		//$('.tabMenu .menu .item').tab();
		
		$('.ui.checkbox').checkbox();
		
		$('.countriesMainList .ui.toggle.checkbox, .usersTable .ui.toggle.checkbox').checkbox({
			onEnable: function(){
				performEntityMethod($(this), 1);
			},
			onDisable: function(){
				performEntityMethod($(this), 0);				
			}
		});
		
		
		$('#user_role').change(function(){
			//alert(1);   
			//loadUserData($(this), $('#user_countries_wrapper'));
			
			modifyUserFormByRole($(this), $('#user_pickup'));
		});
		
		
		$('.countries_scrollbar, .users_scrollbar').perfectScrollbar({
			wheelSpeed: 35,			
			minScrollbarLength: 20
		});
		
		$('#search_role').change(function(){			
			modifySearchFormByRole($(this), $('#search_country'));
		});
		
		$('#search_country').change(function(){			
			modifySearchFormByRole($('#search_role'), $(this));
		});
		
		$('.dataEntry .countries').change(function(){
			loadDataEntryDepots($(this).val());			
		});
		
		/*
		var options = {
			valueNames: [ 'username' ],
			searchClass: 'counryAdminsSearch',
			listClass: 'counryAdminsList',
			//indexAsync: true
		};

		var contactsList = new List('contacts', options);
		contactsList.on('searchComplete', function(){
			$('.contacts').scrollTop(0);
		});
		*/	
		
		var options = {
			object: $('#rejectDoc'),
			template: $("#rejectionReasonTemplate"),
			title: 'Rejection Reason',
		};
		
		qtipInit(options);
	}
);

function loadDataEntryDepots(value){
	if(value != '')
		window.location.href = '/dataEntry/countries/' + value;
}


function qtipInit(options){
	options.object.qtip({			
		events: {
			show: function(){
				/*
				$('.qtip .unapprovedPhotoReasonForm .button').unbind('click').click(function(){
					var photosIds = [];
					var segments = [];
					var value = $(item).siblings('.waitingPhotoId').val();
					var segment = $(item).parents('.waitingPhoto');
					var reason = $('.qtip .unapprovedPhotoReasonForm select').find(':selected').val();
					photosIds.push(value);
					segments.push(segment);
					executeMultiplePhotosAction('unapprove', photosIds, segments, reason);		
				});
				*/
			},		
		},
		content: {
			text: options.template.html(),
			title: {
				text: options.title,
				button: true
			}
		},
		style: {
			classes: 'ui-tooltip-shadow ui-tooltip-rounded qtip-bootstrap',
			tip: {
	            corner: true,
	            height: 24
	        }
		},
		position: {
			my: 'bottom left', // Use the corner...
			at: 'bottom right', // ...and opposite corner
			width: 700, 
			adjust: {
				y: -250,
				x: -150
		    },	
		},
		show: {
			event: 'click',
			modal: {
	            on: true		            
	        }
		},			
		hide: false,
    });
}

function performEntityMethod(clickedObj, value){		
	
	var entityId = clickedObj.parent().find('input').val();		
	var entityMethod = clickedObj.parent().find('input').attr('name');
	var entityName = clickedObj.parents('.ui.form').find('.entityName').val();
		
	$.ajax({
		url: 'entity/' + entityName + '/' + entityId + '/' + entityMethod + '/' + value,
		type: 'Post',
		//data: 'paramId=' + paramId + '&property=' + property + '&value=' + value,
		error: function(response){
			//console.log("Error:" + response.responseText);
		},
		success: function(response){
			//console.log(response.responseText);
		}
	});	
}


function collectSelected(wrapper){
	
	var itemsIds = [];
	wrapper.each(function(){
		var itemId = $(this).attr('id');
		itemIdArr = itemId.split('_');
		itemsIds.push(itemIdArr[1]); 
	});
	
	return itemsIds;
}


function modifyUserFormByRole(roleWrapper, pickupWrapper){
	
	var data = {};
	var roleVal = roleWrapper.val();
	
	if(roleVal == 5){		
		pickupWrapper.parents('.field').show();		
	}
	else{
		pickupWrapper.parents('.field').hide();		
	}
	
	$('#user_depot').parent().hide();
	
	
	console.log(pickupWrapper.parents('.field').html());
	
	data[roleWrapper.attr('name')] = roleVal;
	
	console.log(JSON.stringify(data));
	console.log(roleWrapper.closest('form').attr('action'));
	//return;
	
		
	
		
	$.ajax({
		url: roleWrapper.closest('form').attr('action'),
		type: 'Post',		
		data: data,
		error: function(response){
			//console.log(response.responseText);
			$('.er').html(response.responseText);
			//alert(response.responseText);
		},
		success: function(data){	
			$('#user_countries_container').replaceWith( $(data).find('#user_countries_container') );				
			$('#user_countries_container').find('select').change(function(){
				modifyUserFormByCountry($(this), roleWrapper, $('#user_pickup'));
			});
						
			$('.countries_scrollbar').perfectScrollbar({
				wheelSpeed: 35,			
				minScrollbarLength: 20
			});
			
			$('.ui.checkbox').checkbox();			
		}
	});
}


function modifyUserFormByCountry(countryWrapper, roleWrapper, pickupWrapper){
	
	var roleId = roleWrapper.val();
	if(roleId != 5)
		return;
	
	var data = {};	
	data[countryWrapper.attr('name')] = countryWrapper.val();
	data['role'] = roleWrapper.val();
	console.log(JSON.stringify(data));
	
	$('#user_depot').parent().hide();	
		
	$.ajax({
		url: countryWrapper.closest('form').attr('action'),
		type: 'Post',		
		data: data,
		error: function(response){
			console.log(response.responseText);
			$('.er').html(response.responseText);
			alert(response.responseText);
		},
		success: function(data){
			console.log($(data).find('#user_depot').html() );
			//$('.er').html(data);
			//return;
			
			$('#user_depot').replaceWith( $(data).find('#user_depot') );			
			$('#user_depot').parent().show();
					
		}
	});
}


function modifySearchFormByRole(roleWrapper, countryWrapper){
	var roleId = roleWrapper.val();
	var countryId = countryWrapper.val();
	
	if(roleId != 5 || !countryId.length){
		$('#search_depot').val('').html('<option value="">Depot</option>').attr('disabled','disabled');
		return;
	}
		
	//alert(roleId);
	var data = {};	
	data['countryId'] = countryWrapper.val();
	data['role'] = roleWrapper.val();
	//console.log(JSON.stringify(data));
	//alert($('#loadDepotsRoute').val());
	//$('#search_depot').parent().hide();	
		
	$.ajax({
		url: $('#loadDepotsRoute').val(),
		type: 'Post',		
		data: data,
		error: function(response){
			console.log(response.responseText);
			$('.er').html(response.responseText);
			alert(response.responseText);
		},
		success: function(data){
			//console.log($(data).find('#user_depot').html() );
			//$('.er').html(data);
			//return;
			
			//$('#search_depot').replaceWith( $(data).find('#user_depot') );	
			$('#search_depot').parent().html(data).trigger('create');
			//$('#search_depot').parent().show();
					
		}
	});
	/*
	$('#search_depot').parent().html();
	var roleId = roleWrapper.val();
	var countryId = countryWrapper.val();
	
	if(roleId != 5 || !countryId.length){
		return;
	}
	
	var data = {};	
	data[countryWrapper.attr('name')] = countryWrapper.val();	
	console.log(JSON.stringify(data));
		
	$.ajax({
		url: countryWrapper.closest('form').attr('action'),
		type: 'Post',		
		data: data,
		error: function(response){
			console.log(response.responseText);			
		},
		success: function(data){
			$('#search_depot').replaceWith( data );			
			$('#search_depot').parent().show();
		}
	});
	*/
	
}


/*

function loadUserData(choosenItem, replacedItem){
	
	var form = choosenItem.closest('form');	
	var data = {};
	
	data[choosenItem.attr('name')] = choosenItem.val();
	if(choosenItem.attr('name') != 'user[role]'){
		data['user[role]'] = $('#user_role').val();
	}
	
	console.log(JSON.stringify(data));
	//return;
		
	$.ajax({
		url: form.attr('action'),
		type: 'Post',		
		data: data,
		error: function(response){
			//console.log(response.responseText);
			$('.er').html(response.responseText);
			//alert(response.responseText);
		},
		success: function(data){
			
			//$('.er').html(data);
			//alert(data);
			
			$('#user_depot').parent().hide();
			$('#user_pickup').parents('.field').hide();
			//$('#' + id).parent().show();
			var id = replacedItem.attr('id');
			
			
			replacedItem.replaceWith($(data).find('#' + id));
			
			$('#' + id).parent().show();
			
			var roleId = $('#user_role').val();
			
			if(roleId == 5 && id != 'user_depot'){
				console.log('ID: '+ id);				
				$('#' + id).change(function(){					
					loadUserData($(this), $('#user_depot'));
				});
			}
			
			if(roleId == 5 || id == 'user_depot'){
				$('#user_pickup').parents('.field').show();
			}
			
			$('.countries_scrollbar').perfectScrollbar({
				wheelSpeed: 35,			
				minScrollbarLength: 20
			});
			
			//console.log($(data).find('#' + id).html());
			
			$('.ui.checkbox').checkbox();
			
			
			
			
			
		}
	});
}
*/

function getUserProfile(userId){	
	//var route = $('#profileRoute').val();
	$('#profile_' + userId).html("").css({"height":"200px"}).siblings('.dimmer').dimmer('show');	
	
	//return;
	/*	
	$.ajax({
		url: route + '/' + userId,
		type: 'Get',		
		error: function(error){
			$('#profile_' + userId).html(JSON.stringify(error)).css({"height":"0px"}).siblings('.dimmer').dimmer('hide');
		},
		success: function(data){
			$('#profile_' + userId).html(data).css({"height":"0px"}).siblings('.dimmer').dimmer('hide');
			initInterfaceItems();
		},
	});
	*/	
}

function userSearch2(){
	var name = $('#userFieldName').val();
	var value = $('#userFieldValue').val();
	if(name.length > 0 && value.length > 0){
		//var route = $('#userRoute').val();
		//window.location = route + '?' + name + '=' + value;
		$('#userSearchSubmit_2').click();
	}else{
		alert("Please enter a value for the chosen user field above");
		return false;
	}
}

function userSearch1(){
	var property = $('#userProperty').val();
	var role = $('#search_role').val();
	var country = $('#search_country').val();
	if(property.length == 0 && role.length == 0 && country.length == 0){
		alert('Please select at least one field');
		return false;
	}else{
		$('#userSearchSubmit_1').click();
	}
}

































function setParamProperty(clickedObj, value){			
	var trId = clickedObj.parents('tr').attr('id');	
	var paramIdArr = trId.split('_');
	var paramId = paramIdArr[1];		
	var setter = clickedObj.parents('td').find('input').attr('name');	
	
	$.ajax({
		url: '/admin/catalog/params/param/' + paramId + '/setter:' + setter + '/value:' + value,
		type: 'Post',
		//data: 'paramId=' + paramId + '&property=' + property + '&value=' + value,
		error: function(response){
			console.log(response.responseText);
		},
		success: function(response){
			
		}
	});	
}

function executeMultupleParamsAction(action, paramsIds){			
	var route = $('#multipleParamsRoute').val();
	var settings = $('#multipleParamsForm').serialize();
	
	$('.sidebar')
	  .sidebar('hide')			  
	;
	
	$.ajax({
		url: route,
		type: 'Post',
		data: 'paramsIds=' + paramsIds + '&action=' + action + '&' + settings,
		error: function(response){
			//console.log(response.responseText);
			$('body').html("ERROR:" + response.responseText);
		},
		success: function(response){			
			$('#paramList').html(response);			
			accordionInit();
			paramCheckboxesInit();
			for(var i in sections){
				console.log(sections[i]);
				var section = sections[i];
				$('#' + section).prev('.title').click();
			}
			
			
		}
	});
}


function accordionInit(){
	$('.ui.accordion').accordion({
		'exclusive': false,
		onOpen: function(){
			var id = $(this).attr('id');
			if(sections.indexOf(id) == -1)
				sections.push(id);				
		},
		onClose: function(){
			var id = $(this).attr('id');
			var index = sections.indexOf(id);
			sections.splice(index,1);
		},
	});
}

function paramCheckboxesInit(){	
	
	$('.paramsTable .ui.toggle.checkbox').checkbox({			
		onEnable: function(){
			setParamProperty($(this), 1);
		},
		onDisable: function(){
			setParamProperty($(this), 0);
		}
	});
	
	$('.selParam .ui.checkbox').checkbox({			
		onEnable: function(){
			$('.sidebar')			
			  .sidebar({
			    overlay: true
			  })
			  .sidebar('show')			  
			;
		},
		onDisable: function(){
			
			if($('.selParam .ui.checkbox').find('input[type="checkbox"]').is(":checked")){
				return;			
			}
			
			$('.sidebar')			  
			  .sidebar('hide')			  
			;
		}
	});
	
	$('.selectGroupParams').checkbox({		
		onEnable: function(){
			$(this).parents('.paramsTable').find('.selParam .ui.checkbox').checkbox('enable');
		},
		onDisable: function(){			
			$(this).parents('.paramsTable').find('.selParam .ui.checkbox').checkbox('disable');
		}
	});
	
}

window.onload = function(){
	//alert(1);
	
};

