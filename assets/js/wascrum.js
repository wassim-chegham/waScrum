// config
config = {};
config.SAVE_INTERVAL = 1 * 60000; // 1 min
config.CAN_ADD = true;
config.CAN_EDIT = true;
config.CAN_DELETE = true;
config.current_story = null;

// global
var timer = null;
var author = $('#profile b').text().toLowerCase();

var __autosave_board = function()
{
	var arr_s = new Array(), arr_t = new Array();
	// save stories
	$('.story-container[author="'+author+'"]').each(function(){
		var s = {};
		s.index = $(this).index();
		s.id = $(this).attr('id');
		s.content = $(this).html();
		s.author = $(this).attr('author');
		s.css_class = $(this).attr('class');
		s.parent = $(this).closest('li').index();
		arr_s.push(s);
		//console.log(s);
	});
	
	// save tasks
	$('.task-container[author="'+author+'"]').each(function(){
		var t = {};
		t.index = $(this).index();
		t.id = $(this).attr('id');
		t.story = $(this).attr('story');
		t.content = $(this).html();
		t.author = $(this).attr('author');
		t.css_class = $(this).attr('class');
		t.parent = $(this).closest('li').index();
		t.top = $(this).css('top');
		arr_t.push(t);
		//console.log(t);
	});
	
	if ( arr_s.length > 0 || arr_t.length > 0)
	{
		$.ajax({
			type: "POST",
			dataType: "json",
			url: 'do.save.php?'+Math.random(),
			data: { board: JSON.stringify({stories:arr_s, tasks:arr_t}) },
			beforeSend: function(){ 
				$('#btn-save-board').val(LANG.MSG_1).attr('disabled', true); 
				$('h4 span').html(LANG.MSG_2).addClass( "success" );
			},
			success: function(d){
				var c = 'success';
				if ( d.response == false ) c = 'error';
				$('h4 span').html( d.response ? LANG.MSG_10 : LANG.MSG_11 ).removeAttr('class').addClass( c );
				__auto_hide_feedback();
				
			},
			complete: function(){ $('#btn-save-board').val(LANG.MSG_3).attr('disabled', false); },
			error: function(){}
		});
	}
	
}
var __restore_board = function()
{
	var authors = new Array();
	
	// return true if the user n is not found 
	// into the authors array
	var __is_new_author = function(n)
	{
		for(u in authors)
		{
			if( n == authors[u] ) return false;
		}
		return true;
	}
	
	$.ajax({
			type: "POST",
			dataType: "json",
			url: 'do.restore.php?'+Math.random(),
			beforeSend: function(){ 
				$('h4 span').html(LANG.MSG_4).removeAttr('class').addClass('success'); 
				__auto_hide_feedback();
			},
			success: function(d){
				var c = 'success';
				if ( d.response == false ) c = 'error';
				else {
					var l = d.board.length;
					
					$('#board li:first-child').html('');
					for( var k=0; k<l; k++ )
					{
						var b = d.board[k];
						var s = b.stories;
						var len_s = s != undefined ? s.length : 0;
						var t = b.tasks;
						var len_t = t != undefined ? t.length : 0;
						
						// stories
						for(var i=0; i<len_s; i++)
						{
							var id = s[i].id;
							var user = s[i].author;
							var description = s[i].content;
							var cls = s[i].css_class;
							
							var disabled = '';
							if ( user != author ) disabled = 'disabled';
							
							$('<div id="'+id+'" author="'+user+'" class="'+disabled+' '+cls+'">\
								'+description+'\
							</div>').appendTo('#board li:first-child');
							
							// Update the board list
							if( user != author && __is_new_author(user) )
							{
								authors.push(user);
								$('#btn-toggle-users').append('<option value="'+user+'">'+
									user.substring(0,1).toUpperCase()+user.substring(1)+'</option>');
							}
							
						}
						
						// tasks
						for(var i=0; i<len_t; i++)
						{
							var id = t[i].id;
							var user = t[i].author;
							var description = t[i].content;
							var cls = t[i].css_class;
							var parent = t[i].parent+1;
							var story = t[i].story;
							var top = t[i].top;
							
							var disabled = '';
							if ( user != author ) disabled = 'disabled';
							
							$('#board li:nth-child('+parent+')').html('');
							
							$('<div style="top:'+top+';" id="'+id+'" story="'+story+'" author="'+user+'" class="'+disabled+' '+cls+'">\
								'+description+'\
							</div>').appendTo('#board li:nth-child('+parent+')');
							$('#'+id+'[author="'+author+'"]').draggable({
								containment: '#board',
								cursor: 'move',
								opacity: 0.7,
								revert:'invalid',
								snap: '.task-container',
								snapMode: 'inner',
								snapTolerance: 25,
								grid: [10, 20],
								axis: 'x'
							});
						}
						
					}
					
				}
				$('h4 span').html( d.response ? LANG.MSG_12 : LANG.MSG_13 ).addClass( c );
			},
			complete: function(){
				authors = null;
							
				// set the default board	
				$('#btn-toggle-users option[value="'+author+'"]').attr('selected', true).trigger('change');

			},
			error: function(){ 
				$('h4 span').html(LANG.MSG_5).removeAttr('class').addClass('success'); 
				__auto_hide_feedback();
			}
		});
}
var __clear_board = function()
{
	$('.story-container[author="'+author+'"], .task-container[author="'+author+'"]').each(function(){
		$(this).animate({'background-color':'red'}, 'fast', function(){
			$(this).fadeOut('fast', function(){
				$(this).remove();
			});
		});
	});
	
	$.ajax({
		type: "POST",
		dataType: "json",
		url: 'do.save.php?'+Math.random(),
		data: { board: JSON.stringify({}) },
		beforeSend: function(){ $('#btn-save-board').val(LANG.MSG_6).attr('disabled', true); },
		success: function(d){
			var c = 'success';
			if ( d.response == false ) c = 'error';
			$('h4 span').html( d.response ? LANG.MSG_10 : LANG.MSG_11 ).removeAttr('class').addClass( c );
			__auto_hide_feedback();
		},
		complete: function(){ $('#btn-save-board').val(LANG.MSG_3).attr('disabled', false); },
		error: function(){}
	});
}
var __auto_hide_feedback = function()
{
	clearTimeout(timer);
	timer = setTimeout(function(){
		$('h4 span').fadeOut('slow', function(){
			$('h4 span').html('&nbsp;').removeAttr('class').show();
		});
	}, 5000); // 5 sec
}
var _open_dialog = function(el, o)
{
	var dialog;
	var selected_content;
	
	if ( o == undefined )
	{
		var id = el.attr('id');
		dialog = '#' + id.replace('btn-', '') + '-dialog';
	}
	else {
		dialog = '#'+o.dialog;
		selected_content = '#'+o.selected_content;
	}

	$('#modal').fadeIn(200, function(){
		
		$( dialog ).fadeIn().addClass('dialog-opened');
		$( dialog ).animate({'top':0}, 'fast', function(){
			
			// do some extra work
			switch( dialog )
			{
				case '#edit-story-dialog':
					if ( selected_content == undefined ) selected_content = ".selected";
						
					//console.log(selected_content);
	
					$('#edit-story-dialog #story-description').text( $(selected_content).find('p').text() );
				break;
				case '#edit-task-dialog':
					if ( selected_content == undefined ) selected_content = ".selected-task";
						
					//console.log(selected_content);
	
					var description = $(selected_content).find(' p').text();
					var points = $(selected_content).find(' b').text();
					$('#edit-task-dialog #task-description').val( description );
					$('#edit-task-dialog #estimated-points option[value="'+points+'"]').attr('selected', true);
				break;
			}
			
		});
	});
}
var _disableSelection = function(target)
{
	if (typeof target.onselectstart!="undefined") //IE route
		target.onselectstart=function(){return false}
	else if (typeof target.style.MozUserSelect!="undefined") //Firefox route
		target.style.MozUserSelect="none"
	else //All other route (ie: Opera)
		target.onmousedown=function(){return false}
	target.style.cursor = "default"
}
$(function(){
	
	// disable text selection
	_disableSelection(document.body);
	
	// restoring board
	__restore_board();
	
	var timer = setInterval('__autosave_board()', config.SAVE_INTERVAL);

	// openning a dialog
	$('#menu input[type="button"]:not(#btn-save-board):not(#btn-board-toggle)').click(function(){
		_open_dialog($(this));
	});
	
	// closing a dialog
	$('.close-dialog, #modal').click(function(){
		$('.dialog-opened').fadeIn('fast');
		$('.dialog-opened').animate({'top':-500}, 'slow', function(){
			$('#modal').fadeOut('fast');
		});
	});
	
	// toggle story selection
	$('.story-container:not(.fadeOut) .sub-menu-toggle-select').live('click', function(event){
		
		// event stop propagation hack for live binding
		/*
		if(event.target != this){
			return true;
		}
		//*/
		
		var el = $(this).closest('.story-container');
		
		el.removeClass('fadeOut');
		$('.task-container.fadeOut').removeClass('fadeOut');
		
		// show items
		if ( el.is('.selected') )
		{
			$('.story-container').removeClass("selected");
			
			el.find('.sub-menu').show();
			
			if ( el.attr('author') == author )
				$('#btn-edit-story, #btn-delete-story, #btn-new-task').attr('disabled', true);
			
			$('.fadeOut').removeClass('fadeOut');
			$('.task-container.fadeOut[author="'+author+'"]').draggable( "option", "disabled", true);
			$('h4 span').stop(true, false).html(LANG.MSG_7).addClass('success');
			__auto_hide_feedback();
		}
		// hide items
		else {
			
			$('.story-container').not(el).removeClass("selected");
			
			el.addClass('selected');
			
			el.find('.sub-menu').hide();
			
			if ( el.attr('author') == author )
				$('#btn-edit-story, #btn-delete-story, #btn-new-task').attr('disabled', false);
			
			$('.story-container:not(#'+el.attr('id')+'), .task-container[story!="'+el.attr('id')+'"]').addClass('fadeOut');
			$('.task-container[author="'+author+'"]:not(.fadeOut)').draggable( "option", "disabled", false);
			$('h4 span').stop(true, false).html(LANG.MSG_8).addClass('success');
			__auto_hide_feedback();
		}
	});
	
	// show/hide inline menu
	if ( config.CAN_EDIT )
	{
		$('.story-container:not(.fadeOut)').live('mouseover mouseout', function(event){
	
			if (event.type == 'mouseover') {
				$(this).find('.sub-menu').show();
			} else {
				$(this).find('.sub-menu').hide();
			}
	
		});
	}
	
	$('.sub-menu-edit').live('click', function(){
		config.current_story = $(this).closest('div[id^=story-]').attr('id');
		var o = { dialog: 'edit-story-dialog', selected_content: config.current_story };
		_open_dialog(null, o);
	})
	
	$('.sub-menu-toggle-info').live('click', function(){
		$(this).closest('div').next('div').find('.info').slideToggle(200);
	})
	
	// handling the creation of a new story
	$('#create-story').click(function(){
		var description = $('#new-story-dialog #story-description').val();
		var author = $('#new-story-dialog #story-author').val();
		var dateObject = new Date();
		var date = dateObject.getDate()+'/'+dateObject.getMonth()+'/'+dateObject.getYear();
		if ( description=="" ) alert(LANG.MSG_9);
		else {
			var id = dateObject.getTime();
			$('<div id="story-'+id+'" author="'+author+'" class="story-container">\
					<div>\
						<p>'+description+'</p>\
						<div class="sub-menu">\
							<span class="sub-menu-items sub-menu-toggle-select"></span>\
							<span class="sub-menu-items sub-menu-toggle-info"></span>\
							<span class="sub-menu-items sub-menu-edit"></span>\
						</div>\
					<div>\
					<br class="clear"/>\
					<div class="info">\
						<i>Assigned to <strong>'+author+'</strong><br/>\
						On <strong>'+date+'</strong><br/>\
						Total points <b>0</b></i>\
					<div>\
				</div>').appendTo('#board li:first-child');
				
		}
	});
	
	// handling a story edition
	$('#edit-story').click(function(){
		var description = $('#edit-story-dialog #story-description').val();
		if ( description=="" ) alert(LANG.MSG_9);
		else {
			$('#'+config.current_story).find('p').text( description );
		}
	});
	
	// deleting stories
	$('#delete-story').click(function(){
		$('.dialog-opened').fadeIn('fast');
		$('#btn-edit-story, #btn-delete-story, #btn-new-task').attr('disabled', true);
		$('.dialog-opened').animate({'top':-500}, 'fast', function(){
			$('#modal').fadeOut('fast', function(){
				$('.selected, .task-container:not(.fadeOut)').animate({'background-color':'red'}, 'fast', function(){
					$(this).fadeOut('fast', function(){
						$(this).remove();
						$('.fadeOut').removeClass('fadeOut');
						$('.dialog-opened').removeClass('dialog-opened');
					});
				});
			});
		});
	});
	
	
	// handling task creation
	$('#add-task').click(function(){
		var description = $('#new-task-dialog #task-description').val();
		var points = $('#estimated-points').val();
		var author = $('#new-task-dialog #task-author').val();
		var dateObject = new Date();
		var date = dateObject.getDate()+'/'+dateObject.getMonth()+'/'+dateObject.getFullYear();
		if ( description=="" ) alert(LANG.MSG_9);
		else {
			var id = dateObject.getTime();
			var storyID = $('.selected').attr('id');
			$('<div id="task-'+id+'" story="'+storyID+'" author="'+author+'" class="task-container">\
					<p>'+description+'</p>\
					<i>By <strong>'+author+'</strong><br/>\
					On <strong>'+date+'</strong><br />\
					Estimated Points <b>'+points+'</b></i>\
				</div>').appendTo('#board li:nth-child(2)');
			
			// update the story total points
			var p = parseInt($('#'+storyID+' b').text())+parseInt(points);
			$('#'+storyID+' b').text(p);
			
			$('#task-'+id+'[author="'+author+'"]').draggable({
				containment: '#board',
				cursor: 'move',
				opacity: 0.7,
				revert:'invalid',
				snap: '.task-container',
				snapMode: 'inner',
				snapTolerance: 25,
				grid: [10, 20],
				axis: 'x'
			});
		}
	});
	
	// handling a task edition
	$('#edit-task').click(function(){
		var description = $('#edit-task-dialog #task-description').val();
		var points = $('#edit-task-dialog #estimated-points').val();
		if ( description=="" ) alert(LANG.MSG_9);
		else {
			$('.selected-task p').text( description );
			$('.selected-task b').text( points );
			var storyID = $('.selected-task').attr('story');
			
			// update the story total points
			var p = 0;
			//console.clear();
			$('.task-container[story="'+storyID+'"]').each(function(){
				p += parseInt($(this).find('b').text());
				//console.log(p);
			});
			$('#'+storyID+' b').text(p);
		}
	});
	
	// toggle task selection
	$('.task-container[author="'+author+'"]:not(.fadeOut)').live('click', function(){
		//$('.story-container').not($(this)).removeClass("selected");
		$(this).addClass('selected-task');
		$('#btn-edit-task, #btn-delete-task').attr('disabled', false);
	});
	$('#main').click(function(){
		$('.task-container').removeClass("selected-task");
		$('#btn-edit-task, #btn-delete-task').attr('disabled', true);
	});
	
	// saving board
	$('#btn-save-board').click(function(){
		__autosave_board();
	});
	
	// saving board
	$('#clear-board').click(function(){
		$('.dialog-opened').fadeIn('fast');
		$('.dialog-opened').animate({'top':-500}, 'fast', function(){
			$('#modal').fadeOut('fast', function(){
				__clear_board();
				$('.dialog-opened').removeClass('dialog-opened');
			});
		});
	});
	
	// deleting tasks
	$('#delete-task').click(function(){
		$('.dialog').fadeIn('fast');
		$('.dialog').animate({'top':-500}, 'fast', function(){
			$('#modal').fadeOut('fast', function(){
				
				$('.selected-task').animate({'background-color':'red'}, 'fast', function(){
					$(this).fadeOut('fast', function(){
						$(this).remove();
						$('.fadeOut').removeClass('fadeOut');
						$('.dialog-opened').removeClass('dialog-opened');
					});
				});
				
			});
		});
		
	});
	
	// choosing the right board
	$('#btn-toggle-users').change(function(e){
		
		var u = $(this).val();
		if ( u == "all" )
		{
			$('.story-container, .task-container').removeClass('display-none');
		}
		else {
			$('.story-container, .task-container').each(function(){
				$(this).removeClass('display-none');
				if ( $(this).attr('author') == u )
				{
					$(this).removeClass('display-none');
				}
				else {
					$(this).addClass('display-none');
				}
			});
		}

	});
	
	// define the droppable areas
	$("li:not(:first-child)").droppable({
		drop: function(event, ui) {
			var li = $(this);//.find('.columns-containers');
			ui.helper.appendTo(li).css({'left':0});
		}
	});
	
	// choosing the language
	$('#i8ln span').click(function(){
		// deprecated
	});
	
	// disabling links that begin with a #
	$('a[href^="#"]').click(function(e){e.preventDefault();})
	
	// extra info
	$('.story-container').attr('title', LANG.MSG_7);
	
 	// extra work
  	$('.task-container.fadeOut[author="'+author+'"]').draggable( "option", "disabled", true);
  	$('.task-container:[author="'+author+'"]not(.fadeOut)').draggable( "option", "disabled", false);

});