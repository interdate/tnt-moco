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
			loadUserData($(this), $('#user_country'));
		});
		
		
		$('.countries_scrollbar').perfectScrollbar({
			wheelSpeed: 35,			
			minScrollbarLength: 20
		});
		
		/*
		$('.usersTable tbody .edit.icon').click(function(){
			$(this).parents('tr').addClass("editedUser");			
			$(this).parents('tr').next().removeClass('hidden');			
			var userId = $(this).siblings('.userId').val();
			getUserProfile(userId);
		});
		*/
		
		
		
		/*
		
		var $role = $('#user_role');
		// When role gets selected ...
		$role.change(function() {
		  // ... retrieve the corresponding form.
		  var $form = $(this).closest('form');
		  // Simulate form data, but only include the selected role value.
		  var data = {};
		  data[$role.attr('name')] = $role.val();
		  // Submit data via AJAX to the form's action path.
		  $.ajax({
		    url : $form.attr('action'),
		    type: $form.attr('method'),
		    data : data,
		    error: function(error){
		    	console.log(error.responseText);
		    },
		    success: function(html) {
		    	
		    	//$('.test').html(html);
		      // Replace current countries field ...
		      $('#user_countries').replaceWith(
		        // ... with the returned one from the AJAX response.
		        $(html).find('#user_countries')
		      );
		      // countries field now displays the appropriate countries.
		      
		      
		      
		      
		      
		      var $countries = $('#user_countries');
				// When role gets selected ...
				$countries.change(function() {
					
					console.log("DATA:" + $countries.val());
					
				  // ... retrieve the corresponding form.
				  var $form = $(this).closest('form');
				  // Simulate form data, but only include the selected role value.
				  var data = {};
				  data[$countries.attr('name')] = $countries.val();
				  // Submit data via AJAX to the form's action path.
				  $.ajax({
				    url : $form.attr('action'),
				    type: $form.attr('method'),
				    data : data,
				    error: function(error){
				    	console.log(error.responseText);
				    },
				    success: function(html) {
				    	
				    	//$('body').html(html);
				    	//return;
				    	var depot = $(html).find('#user_depot');
				    	$('#user_countries').parent().after(depot);
				     
				    }
				  });
				});
		      
		      
		      
		      
		      
		      
		      
		      
		      
		      
		      
		      
		      
		      
		      
		    }
		  });
		});
		
		
		*/
		
		
		
		
		/*
		$('.tabMenu .menu .item').tab();
		$('.ui.checkbox').checkbox();
		$('.notChecked .ui.checkbox').checkbox({
			disable: true,
		});
		
		$('.notChecked .ui.checkbox').checkbox('disable');
		
		
		$('.ui.dropdown').dropdown({
			'on':'click',
			'delay': {
				show: 100,
				hide: 100
			},
			'duration' : 150
		});
		
		 
		$('#add_category, #save_category, #add_param_group, #save_group, #save_param_group_settings, #save_param_group, #add_param, #save_param').click(function(){
			var form = $(this).parents('form');
			var empty = false;
		
			//console.log(5555);
			
			form.find('input[required="required"]').each(function(){				
				if(!$(this).val().trim().length){					
					empty = true;
				}				
			});
			
			if(empty){
				alert('Please fill all the fields');
				return;
			}
			
			if(form.find('.ui.checkbox input[class="required"]').length){
				var checked = false;				
				form.find('.ui.checkbox input[class="required"]').each(function(){
					if($(this).is(':checked')){
						checked = true;
					}				
				});
				
				if(!checked){
					alert('Please choose at least one category');
					return;
				}
			}
			
			form.submit();			
			
		});
		
		sections = [];		
		accordionInit();
		paramCheckboxesInit();
		
		
		
		
		
		$('.multipleParamSettings .button').click(function(){
			var action = ( $(this).parent('div').hasClass('apply') ) ? 'apply' : 'remove';
			if(action == 'remove' && !confirm('Remove selected parameters? Are you sure?')){
				return false;
			}
			var paramsIds = [];
			$('.selParam input[type="checkbox"]:checked').each(function(){
				var itemId = $(this).attr('id');
				itemIdArr = itemId.split('_');
				paramsIds.push(itemIdArr[1]); 
			});
			executeMultupleParamsAction(action, paramsIds);
		});
		
		$('#multipleParamsForm .header .ui.checkbox').checkbox({
			onEnable: function(){
				$(this).parents('.header').siblings('.fields').find('select, input').attr('disabled', false);
			},
			onDisable: function(){
				$(this).parents('.header').siblings('.fields').find('select, input').attr('disabled', true);				
			}
		});
		
		
		
		
		
		
		
		$('#selectAll').click(function(){
			$(this).checkbox('toggle');
			
			if($(this).find('input[type="checkbox"]').is(":checked")){
				$('.ui.checkbox').checkbox('disable');			
			}
			else{
				$('.ui.checkbox').checkbox('enable');
			}
		});
		
		
		
		
		
		$('#statWraper .location').popup({
			delay: 1, //miliseconds
			duration: 50 //miliseconds,			
		});  
		
		
		
		$('#getSideBar').click(function(){
			
			$('.sidebar')			
			  .sidebar({
			    overlay: true
			  })
			  .sidebar('show')			  
			;
			
			if($('.sidebar').hasClass('active')){
				//$('#content_wrapper .headerWrapper').css({"z-index":"1"});				
			}
			else{
				//$('#content_wrapper .headerWrapper').css({"z-index":"999999"});
			}
			
		});		
		
		$('#usersMenuActions .item').click(function(){
			var action = $(this).attr('id');
			alert(action);
		});
		
		
		
		
		
		
		//$('.ui.checkbox').checkbox();
		
		
		*/
		
		
	}
);


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

function loadUserData(choosenItem, replacedItem){
	
	var form = choosenItem.closest('form');	
	var data = {};
	data[choosenItem.attr('name')] = choosenItem.val();
	
	console.log(JSON.stringify(data));
	//return;
		
	$.ajax({
		url: form.attr('action'),
		type: 'Post',		
		data: data,
		error: function(response){
			console.log(response.responseText);
		},
		success: function(data){
			$('#user_depot').parent().hide();
			$('#user_pickup').parents('.field').hide();
			//$('#' + id).parent().show();
			var id = replacedItem.attr('id');
			console.log('ID: '+ replacedItem.html());
			
			replacedItem.replaceWith($(data).find('#' + id));
			
			$('#' + id).parent().show();
			
			var roleId = $('#user_role').val();
			
			if(roleId == 5 && id != 'user_depot'){			
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
			
			
		}
	});
}


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

