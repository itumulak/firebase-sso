jQuery(document).ready((_) => {
    _('#configuration-textarea').val(_.trim(_('#configuration-textarea').val()));
    wp.codeEditor.initialize(_('#configuration-textarea'), cm_settings);

    if ( location.hash.substr(1) )
        _(`#${location.hash.substr(1)}`).addClass('nav-tab-active');
    else
        _('.nav-tab-wrapper a:first-child').addClass('nav-tab-active');

    _('.nav-tab-wrapper a').each((index, element) => {
        if ( _(element).hasClass('nav-tab-active') == false )
            _(`#${_(element).attr('id')}-tab`).css('display', 'none');
    });

    _('.nav-tab').on('click', (element) => {
        _('.nav-tab').removeClass('nav-tab-active');
        _(element.target).addClass('nav-tab-active');

        _('.tabs-holder .group').css('display', 'none');
        _(`#${_(element.target).attr('id')}-tab`).css('display', 'block');
    });

    _('#configuration-code').submit((event) => {
        event.preventDefault();

        const $configuration = _('#configuration-textarea').val();

        _.post(ajaxurl, { action: 'firebase_config', config: $configuration }, (e, textStatus, jqXHR) => {
            if ( e.success == true ) {
                _.toast({
                    heading: 'Success',
                    text: 'Config updated.',
                    showHideTransition: 'slide',
                    icon: 'success',
                    position: {
                        top: 40,
                        right: 80
                    }
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

        _.post(ajaxurl, {
            action: 'firebase_providers',
            enabled_providers: $signInProviders
        }, (e, textStatus, jqXHR) => {
            if ( e.success == true ) {
                _.toast({
                    heading: 'Success',
                    text: 'Sign-in providers updated.',
                    showHideTransition: 'slide',
                    icon: 'success',
                    position: {
                        top: 40,
                        right: 80
                    }
                });
            }
        });
    });
});