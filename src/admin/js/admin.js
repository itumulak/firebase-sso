/**
 * Admin JS.
 *
 * @since 1.0.0
 */

jQuery(document).ready(($) => {
	if (location.hash.substr(1))
		$(`#${location.hash.substr(1)}`).addClass('nav-tab-active');
	else $('.nav-tab-wrapper a:first-child').addClass('nav-tab-active');

	$('.nav-tab-wrapper a').each((index, element) => {
		if ($(element).hasClass('nav-tab-active') === false)
			$(`#${$(element).attr('id')}-tab`).css('display', 'none');
	});

	$('.nav-tab').on('click', (element) => {
		$('.nav-tab').removeClass('nav-tab-active');
		$(element.target).addClass('nav-tab-active');

		$('.tabs-holder .group').css('display', 'none');
		$(`#${$(element.target).attr('id')}-tab`).css('display', 'block');
	});

	$('#configuration-fields').submit((event) => {
		event.preventDefault();
		const $configuration = {};

		$('#configuration-fields :input').each((index, element) => {
			const $key = $(element).attr('id');
			const $val = $(element).val();

			$configuration[$key] = $val;
		});

		$configuration.action = 'firebase_config';

		// eslint-disable-next-line no-undef
		$.post(ajaxurl, $configuration, (e) => {
			if (e.success === true) {
				$.toast({
					heading: 'Success',
					text: 'Config updated.',
					showHideTransition: 'slide',
					icon: 'success',
					position: {
						top: 40,
						right: 80,
					},
				});
			}
		});
	});

	$('#sign-in-providers-form').submit((event) => {
		event.preventDefault();
		const $signInProviders = [];

		$('#sign-in-providers-form input:checked').each((index, element) => {
			$signInProviders.push($(element).attr('id'));
		});

		$.post(
			// eslint-disable-next-line no-undef
			ajaxurl,
			{
				action: 'firebase_providers',
				enabled_providers: $signInProviders,
			},
			(e) => {
				if (e.success === true) {
					$.toast({
						heading: 'Success',
						text: 'Sign-in providers updated.',
						showHideTransition: 'slide',
						icon: 'success',
						position: {
							top: 40,
							right: 80,
						},
					});
				}
			}
		);
	});
});
