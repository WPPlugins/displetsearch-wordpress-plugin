;(function ($) {
	$.widget('displetreader.displetreader_admin', {
		'_create': function () {
			var el = this.element;
			var o = this.options;
			var form = $('form', el);

			$('.ui-buttonset', el).buttonset();
			form.validate();
		}
	});

	$(function () {
		$('#displetreader-admin').displetreader_admin();
	});
})(jQuery);

jQuery(document).ready(function($) {
	$('input[name="displetreader[enabled]"]').click(function() {
		updateOptionEnabled();
	});
	
	$('input[name="displetreader[displet_pro_search]"]').click(function() {
		toggleLandingPage();
	});
	
	updateOptionEnabled();
	
});

function updateOptionEnabled() {
	if (jQuery('#enableddispletreader').is(':checked')) {
		jQuery('.displet_option_row input, .displet_option_row select, ').prop("disabled",false);
		toggleLandingPage();
	} else {
		jQuery('.displet_option_row input, .displet_option_row select, ').prop("disabled",true);
	}
}

function toggleLandingPage() {
	if (jQuery('#displetreaderpro').is(':checked')) {
		jQuery('#ondispletlandingpage').hide();
		//jQuery('.displetprooption').show();
	} else {
		jQuery('#ondispletlandingpage').show();
		//jQuery('.displetprooption').hide();
	}
}