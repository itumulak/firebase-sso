import $ from 'jquery';

$(document).ready((_) => {
	console.log('hello');

	if (location.hash.substr(1))
		_(`#${location.hash.substr(1)}`).addClass('nav-tab-active');
	else _('.nav-tab-wrapper a:first-child').addClass('nav-tab-active');

	_('.nav-tab-wrapper a').each((index, element) => {
		if (_(element).hasClass('nav-tab-active') === false)
			_(`#${_(element).attr('id')}-tab`).css('display', 'none');
	});

	_('.nav-tab').on('click', (element) => {
		_('.nav-tab').removeClass('nav-tab-active');
		_(element.target).addClass('nav-tab-active');

		_('.tabs-holder .group').css('display', 'none');
		_(`#${_(element.target).attr('id')}-tab`).css('display', 'block');
	});

	_('#configuration-fields').submit((event) => {
		event.preventDefault();
		const $configuration = {};

		_('#configuration-fields :input').each((index, element) => {
			const $key = _(element).attr('id');
			const $val = _(element).val();

			$configuration[$key] = $val;
		});

		$configuration.action = 'firebase_config';

		// eslint-disable-next-line no-undef
		_.post(ajaxurl, $configuration, (e) => {
			if (e.success === true) {
				_.toast({
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

	_('#sign-in-providers-form').submit((event) => {
		event.preventDefault();
		const $signInProviders = [];

		_('#sign-in-providers-form input:checked').each((index, element) => {
			$signInProviders.push(_(element).attr('id'));
		});

		_.post(
			// eslint-disable-next-line no-undef
			ajaxurl,
			{
				action: 'firebase_providers',
				enabled_providers: $signInProviders,
			},
			(e) => {
				if (e.success === true) {
					_.toast({
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
