$(document).ready(function(){	
	$('.datetimepicker').datetimepicker({
		dateFormat: 'yy-mm-dd',
		timeFormat: 'hh:mm:ss',
		separator: ' '
	});
	
	$('.datepicker').datepicker({
		dateFormat: 'yy-mm-dd'
	});
	
	$('.timepicker').timepicker({
		timeFormat: 'hh:mm:ss'
	});
	
	$('.slide.close').hide();
	
	$('.slide_activator').click(function(e){
		if($($(this).attr('rel')).hasClass('open')) {
			$($(this).attr('rel')).slideUp(200);
			$($(this).attr('rel')).removeClass('open');
		}
		else {
			$($(this).attr('rel')).slideDown(200);
			$($(this).attr('rel')).addClass('open');
		}
		e.preventDefault();
	})
	
	$('.options').blur(function(){
		if(document.all) { // IE
		var options = $(this).val().split("\r\n");
		}
		else { //Mozilla
		var options = $(this).val().split("\n");
		}
		var html = '';
		for(var i = 0; i<options.length; i++) {
			html += '<option value="'+options[i]+'">'+options[i]+'</option>';
		}
		
		$(this).parent().parent().find('.default_hide select.default_select').html(html);
		$(this).parent().parent().find('.default_hide input.default').val($(this).parent().parent().find('.default_hide select.default_select').val());
	})
	
	$('.default_generator').change(function(){
		var value = $(this).val();
		if($(this).hasClass('default_checkbox') && !$(this).attr('checked')) {
			value = '';
		}
		$(this).parent().find('.default').val(value);
	})
	
	$('.select_type').change(function(){
		//Si c'est un textarea
		if($(this).val() == 'text') {
			$(this).parent().parent().find('.default_hide').slideUp(200);
			$(this).parent().parent().find('.wysiwyg').slideDown(200);
		}
		else {
			$(this).parent().parent().find('.default_hide').slideDown(200);
			$(this).parent().parent().find('.wysiwyg').slideUp(200);
		}

		// Si c'est un checkbox
		if($(this).val() == 'tinyint(1)') {
			$(this).parent().parent().find('.options').slideUp(200);
			$(this).parent().parent().find('.default_hide input.default_text').hide();
			$(this).parent().parent().find('.default_hide select.default_select').hide();
			$(this).parent().parent().find('.default_hide input.default_checkbox').show();
			$(this).parent().parent().find('.default_hide').addClass('checkbox');
			$(this).parent().parent().find('.default_hide').removeClass('text');
			$(this).parent().parent().find('.default_hide').removeClass('select');
			if($(this).parent().parent().find('.default_hide input.default_checkbox').attr('checked')) {
				var value = 'on';
			}
			else {
				var value = '';
			}
			$(this).parent().parent().find('.default').val(value);
		}
		else {
		    // Select
			if($(this).val() == 'enum') {
				$(this).parent().parent().find('.options').slideDown(200);
				$(this).parent().parent().find('.default_hide input.default_text').hide();
				$(this).parent().parent().find('.default_hide input.default_checkbox').hide();
				$(this).parent().parent().find('.default_hide select.default_select').show();
				$(this).parent().parent().find('.default_hide').addClass('select');
				$(this).parent().parent().find('.default_hide').removeClass('text');
				$(this).parent().parent().find('.default_hide').removeClass('checkbox');
				$(this).parent().parent().find('.default').val($(this).parent().parent().find('.default_hide select.default_select').val());
			}
			// C'est du texte
			else {
				$(this).parent().parent().find('.options').slideUp(200);
				$(this).parent().parent().find('.default_hide input.default_checkbox').hide();
				$(this).parent().parent().find('.default_hide select.default_select').hide();
				$(this).parent().parent().find('.default_hide input.default_text').show();
				$(this).parent().parent().find('.default_hide').addClass('text');
				$(this).parent().parent().find('.default_hide').removeClass('checkbox');
				$(this).parent().parent().find('.default_hide').removeClass('select');
				$(this).parent().parent().find('.default_hide input.default_text').val('')
				$(this).parent().parent().find('.default_hide input.default').val('');
			}
		}		
		
		//Si c'est datetime
		if($(this).val() == 'datetime') {
			$(this).parent().parent().find('.default_text').datetimepicker("destroy");
			$(this).parent().parent().find('.default_text').datetimepicker({
				dateFormat: 'yy-mm-dd',
				timeFormat: 'hh:mm:ss',
				separator: ' '
			});
		}
		else {
			if($(this).val() == 'date') {
				$(this).parent().parent().find('.default_text').datetimepicker("destroy");
				$(this).parent().parent().find('.default_text').datepicker({
					dateFormat: 'yy-mm-dd'
				});
			}
			else {
				if($(this).val() == 'time') {
					$(this).parent().parent().find('.default_text').datetimepicker("destroy");
					$(this).parent().parent().find('.default_text').timepicker({
						timeFormat: 'hh:mm:ss'
					});
				}
				else {
					$(this).parent().parent().find('.default_text').datetimepicker("destroy");
					if($(this).val() == 'varchar(255)') {
						$(this).parent().parent().find('.upload').slideDown(200);
					}
					else {
						$(this).parent().parent().find('.upload').slideUp(200);
					}
				}
			}
		}
	})
})