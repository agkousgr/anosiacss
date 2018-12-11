$(document).ready(function () {
    // $('#speedex-iframe').load(function () {
        // let iFrameContents = $('iframe[id="#speedex-iframe"]').contents();
        let iFrameContents = $('#speedex-iframe').contents();
        iFrameContents.find('#footer').css('display', 'none');
        let mainContent = iFrameContents.find('div.smallgaps');
        console.log(mainContent);
        iFrameContents.find('html').replaceWith(mainContent);
    // });
});