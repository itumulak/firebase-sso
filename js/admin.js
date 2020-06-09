jQuery(document).ready((_) => {
    wp.codeEditor.initialize(_('#fancy-textarea'), cm_settings);

    console.log(location.hash);

    if (location.hash.substr(1))
        _(`#${location.hash.substr(1)}`).addClass('nav-tab-active');
    else
        _('.nav-tab-wrapper a:first-child').addClass('nav-tab-active');

    _('.nav-tab-wrapper a').each((index, element) => {
        if ( _(element).hasClass('nav-tab-active') == false )
            _(`#${_(element).attr('id')}-tab`).css('display', 'none');
    })

    _('.nav-tab').on('click', (element) => {
        _('.nav-tab').removeClass('nav-tab-active');
        _(element.target).addClass('nav-tab-active');

        _('.tabs-holder .group').css('display', 'none');
        _(`#${_(element.target).attr('id')}-tab`).css('display', 'block');

        return;
    })
});