var pdfRenderingTask = false;

function initializePDF(element, pagebar, url, layout) {
    PDFJS.getDocument(url).then(function(pdf) {
        $(element).data('pdf',pdf);
        $(element).data('num-pages', pdf.numPages);
        $(element).data('current-page', 0);
        $(element).data('last-width', $(element).width());
        $(element).attr('class', numberToLayout(layout));
        setActiveLayout(pagebar, layout);
        renderPDF(pdf, element, pagebar, 0);
        // Register events
        // - Layout Events
        $(pagebar).find('.pdf-layout-single').click(function(e) {
            $(element).removeClass();
            $(element).addClass('one-page');
            renderPDF(pdf, element, pagebar, 0);
            updateDefaultLayout(0);
            setActiveLayout(pagebar, 0);
            e.preventDefault();
        });
        $(pagebar).find('.pdf-layout-double-odd').click(function(e) {
            $(element).removeClass();
            $(element).addClass('two-page-odd');
            renderPDF(pdf, element, pagebar, 0);
            updateDefaultLayout(1);
            setActiveLayout(pagebar, 1);
            e.preventDefault();
        });
        $(pagebar).find('.pdf-layout-double-even').click(function(e) {
            $(element).removeClass();
            $(element).addClass('two-page-even');
            renderPDF(pdf, element, pagebar, 0);
            updateDefaultLayout(2);
            setActiveLayout(pagebar, 2);
            e.preventDefault();
        });
        // - Resize Events
        $(window).bind('orientationchange resize', function() {
            if($(element).width() != $(element).data('last-width')) {
                $(element).data('last-width', $(element).width());
                renderPDF(pdf, element, pagebar, 0);
            }
        });
        $(element).bind('pdf-page-next', function() {
            renderPDF(pdf, element, pagebar, 1);
        });
        $(element).bind('pdf-page-prev', function() {
            renderPDF(pdf, element, pagebar, -1);
        });
        $(pagebar).find('.pdf-page-next').click(function(e) {
            renderPDF(pdf, element, pagebar, 1);
            e.preventDefault();
        });
        $(pagebar).find('.pdf-page-prev').click(function(e) {
            renderPDF(pdf, element, pagebar, -1);
            e.preventDefault();
        });
    });
}

function numberToLayout(number) {
    switch(number) {
        case 0:
            return "one-page";
        case 1:
            return "two-page-odd";
        case 2:
            return "two-page-even";
    }
    return "error";
}

function setActiveLayout(pagebar, number) {
    $(pagebar).find('.pdf-layout-single'     ).removeClass('active');
    $(pagebar).find('.pdf-layout-double-odd' ).removeClass('active');
    $(pagebar).find('.pdf-layout-double-even').removeClass('active');
    switch(number) {
        case 0:
            $(pagebar).find('.pdf-layout-single').addClass('active');
            break;
        case 1:
            $(pagebar).find('.pdf-layout-double-odd').addClass('active');
            break;
        case 2:
            $(pagebar).find('.pdf-layout-double-even').addClass('active');
    }
}

function renderPDF(pdf, element, pagebar, change) {
    var numPages    = $(element).data('num-pages');
    var currentPage = $(element).data('current-page');
    var layout      = $(element).attr('class');
    if(layout == 'one-page') {
        currentPage += change;
        if(currentPage < 1) currentPage = 1;
    } else {
        currentPage = 2*(Math.floor(currentPage/2)+change);
        if(currentPage < 0) currentPage = 0;
        if(layout == 'two-page-even') {
            currentPage++;
        }
    }
    console.log(currentPage);
    if(currentPage > numPages) {
        if(layout == 'one-page') {
            currentPage = numPages;
        } else if(layout == 'two-page-even') {
            currentPage = 2*(Math.ceil(numPages/2))-1;
        } else {
            currentPage = 2*(Math.floor(numPages/2));
        }
    }
    
    $(pagebar).find('.pdf-page-num').text(currentPage+'/'+numPages);
    
    // cancel any current rendering
    if(typeof pdfRenderingTask.cancel == 'function') {
        pdfRenderingTask.cancel();
    }
    
    // here is where we render the pages
    $(element).html("");
    $(element).append($('<canvas/>'),$('<canvas/>'));
    var canvasSet   = $(element).find('canvas');
    var leftCanvas  = canvasSet.first();
    var rightCanvas = canvasSet.last();
    var callback = function(){};
    if((layout != 'one-page') && (currentPage < numPages)) {
        callback = (function(canvas, pdf, page, callback) {
            return function(){renderPage(canvas, pdf, page, callback);};
        })(rightCanvas, pdf, currentPage+1, function(){});
    }
    renderPage(leftCanvas, pdf, currentPage, callback);
    $(element).data('current-page', currentPage);
}

function renderPage(canvas, pdf, page, callback) {
    if(page == 0) {
        callback();
    } else {
        $(canvas)[0].width = $(canvas).width()*2;
        pdf.getPage( page ).then( function(page) {
            var canvasDOM   = $(canvas)[0];
            canvasDOM.width = $(canvas).width()*2;
            var viewport  = getFitViewport(page, canvasDOM);
            canvasDOM.height = viewport.height;
            $(canvas).height(viewport.height/2);
            var context   = canvasDOM.getContext('2d');
            pdfRenderingTask = page.render({
                canvasContext: context,
                viewport: viewport
            }).then(function() {
                callback();
            });
        });
    }
}

// gets the pdf.js viewport necessary to fill the width of the supplied canvas
function getFitViewport(page, canvas) {
    var initial = page.getViewport(1.0);
    var ratioW = canvas.width  / initial.width;
    return( page.getViewport(ratioW));
}