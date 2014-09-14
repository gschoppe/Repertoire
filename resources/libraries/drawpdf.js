function drawPDF(element, url) {
    // a really tiny debounce function that i stole from a tutorial
    function debounce(a,b,c){var d;return function(){var e=this,f=arguments;clearTimeout(d),d=setTimeout(function(){d=null,c||a.apply(e,f)},b),c&&!d&&a.apply(e,f)}}
    // variables
    var thePDF           = null;  // reference to the pdf.js object
    var numPages         = 0;     // number of pages in the pdf
    var elementWidth     = 0;
    var rendering        = false; // flag - are we currently rendering pages to canvas?
    var cancelRendering  = false; // flag - has another process requested that we stop rendering?
    var renderWaitTimer  = null;  // reference to timeout to deduplicate calls to render
    // make pdf.js object from url of pdf (not CORS compliant)
    PDFJS.getDocument(url).then(function(pdf) {
        // store a couple variables
        thePDF   = pdf;
        numPages = pdf.numPages;
        // make canvases for the pages
        for(i = 0; i < numPages; i++ ) {
            var canvas    = document.createElement( "canvas" );
            canvas.width  = $(element).width()*2;
            canvas.height = 20;
            $(element).append( canvas );
        }
        //populate the canvases
        renderPDF();
        
        // register event handlers
        //debounce input to reduce memory usage
        var debouncedRender = debounce(function(){renderPDF();},250);
        $(window).on("orientationchange resize zoomPDF", debouncedRender);
    });
    
    // a call to put the contents of the pdf.js object into the canvases we prepared
    function renderPDF() {
        // if the element width hasn't changed, do nothing
        if(elementWidth == $(element).width()) return;
        // end wait timer for any other processes
        clearTimeout(renderWaitTimer);
        // if we are currently rendering pages
        if(rendering) {
            // set a flag to tell the renderer to quit
            cancelRendering = true;
            // wait 100ms and try again
            renderWaitTimer = setTimeout(function(){renderPDF();}, 100);
        } else {
            //set the new element width
            elementWidth = $(element).width();
            // set the flag to say we are rendering
            rendering = true;
            // start the asynchronous render task with page 1
            thePDF.getPage( 1 ).then( function(page) {drawPages(page, 1);} );
        }
    }
    
    // recursive rendering task
    function drawPages(page, pageNum) {
        // check the cancel flag, and if it's true, stop rendering and clear flags
        if(cancelRendering) {
            cancelRendering = false;
            rendering       = false;
            return;
        }
        // get the canvas for this page as both a jQuery element and a DOM element
        var canvasEl  = $(element).find('canvas:nth-child('+pageNum+')');
        var canvas    = canvasEl[0];
        // set the canvas native width to match computed width (x2 for retina)
        canvas.width  = canvasEl.width()*2;
        // compute the optimal viewport
        var viewport  = getFitViewport(page, canvas);
        // set height to proportional
        canvas.height = viewport.height;
        var context   = canvas.getContext('2d');
        // clear the canvas
        context.clearRect ( 0 , 0 , canvas.width , canvas.height );
        // render the page in memory to the canvas
        page.render({canvasContext: context,viewport: viewport});
        // go to the next page
        pageNum++;
        // if we havent been cancelled, and there are more pages
        if ( !cancelRendering && thePDF !== null && pageNum <= numPages ) {
            // start the rendering task for the next page
            thePDF.getPage( pageNum ).then( function(page) {drawPages(page, pageNum);} );
        } else {
            // clear flags, we're done rendering
            cancelRendering = false;
            rendering       = false;
        }
    }
    
    // gets the pdf.js viewport necessary to fill the width of the supplied canvas
    function getFitViewport(page, canvas) {
        var initial = page.getViewport(1.0);
        var ratioW = canvas.width  / initial.width;
        // this code was to do fit to page, rather than fit to width
        //var ratioH = canvas.height / initial.height;
        //return( page.getViewport(Math.min(ratioW, ratioH)));
        return( page.getViewport(ratioW));
    }
}