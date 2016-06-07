(function ($) {

	$.entwine(function ($) {

		// Add a button to the Accessibility tab to trigger sending page to Tenon manually
		$('#TenonCheckOnSave').entwine({
			onmatch: function () {
				var TennonTrigger = $('<input id="tenon-trigger" type="button" value="Send page to Tenon now" ' +
					'style="top: -40px;" ' +
					'class="ss-ui-alternate ui-button ui-widget ui-state-default ui-button-text-icon-primary ss-ui-action-constructive ss-ui-button" />');

				var container = $('#Root_Accessibility');

				TennonTrigger.appendTo(container);
			}
		});
	});

	// Click the button to trigger the manual Tenon check
	$('body').on('click', '#tenon-trigger', function (e) {

		$('#tenon-trigger').prop('disabled', true);
		$('#tenon-trigger').addClass('ui-state-disabled');
		$('#Root_Accessibility').append('<img id="ajax-waiting" style="vertical-align: middle; margin-left: -100px;" src="/tenon/images/ajax_waiting.gif">');

		var pageID = $('#Form_EditForm_ID').val();

		var request = $.ajax({
			url: $('base').attr('href') + "TenonProcessor/analysePage/" + pageID,
			type: 'POST',

			success: function () {
				$('#tenon-trigger').prop('disabled', false);
				$('#tenon-trigger').removeClass('ui-state-disabled');
				$('#ajax-waiting').remove();
				location.reload();
			},
			failure: function () {
				$('#tenon-trigger').prop('disabled', false);
				$('#tenon-trigger').removeClass('ui-state-disabled');
				$('#ajax-waiting').remove();
			}
		});
		e.preventDefault;
	});

})(jQuery);