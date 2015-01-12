/**
 * Original code from https://bitbucket.org/tenon-io/tenon-js-embed-code released under MIT license
 * Modified (lines 7, 15, 69, 72) for Silverstripe by govtnz@dia.govt.nz
 */
(function ($) {

    $(document).ready(function (options) {

        // To understand what some of these parameters do, please RTFM:
        // https://bitbucket.org/tenon-io/tenon.io-documentation
        var defaults = {
            contents: $('html').html(), // defaults to the entire web page
            fragment: 0, // whether or not it is a fragment or not. If you use anything for 'contents' *other* than $('html').html(), you should set this to '1'
            thisURL: window.location.href, // current URL
            postFile: '/tenon/analyse', // location of the PHP script that fires off the CURL post to Tenon API
            level: 'AAA', // minimum WCAG Level
            certainty: 0, // minimum test certainty
            priority: 0, // minimum priority
            docID: '', // document ID - left blank by default
            systemID: '', // system ID - left blank by default
            reportID: '', // report ID - left blank by default
            viewPortHeight: $(window).height(), //height of the viewport for Tenon to test in
            viewPortWidth: $(window).width(), //width of the viewport for Tenon to test in
            uaString: navigator.userAgent, // user agent string for Tenon to use
            importance: 1, // how important is this specific page?
            ref: 0, // whether to include reference material in the response
            store: 0 // whether to store the response data on Tenon's servers
        };

        var settings = {
            contents: options.contents || defaults.contents,
            fragment: options.fragment || defaults.fragment,
            thisURL: options.thisURL || defaults.thisURL,
            postFile: options.postFile || defaults.postFile,
            level: options.level || defaults.level,
            certainty: options.certainty || defaults.certainty,
            priority: options.priority || defaults.priority,
            docID: options.docID || defaults.docID,
            systemID: options.systemID || defaults.systemID,
            reportID: options.reportID || defaults.reportID,
            viewPortHeight: options.viewPortHeight || defaults.viewPortHeight,
            viewPortWidth: options.viewPortWidth || defaults.viewPortWidth,
            uaString: options.uaString || defaults.uaString,
            importance: options.importance || defaults.importance,
            ref: options.ref || defaults.ref,
            store: options.store || defaults.store,
        };

        // do the stuff
        return $.post(settings.postFile, {
                src: settings.contents,
                tURL: settings.thisURL,
                fragment: settings.fragment,
                level: settings.level,
                certainty: settings.certainty,
                priority: settings.priority,
                docID: settings.docID,
                systemID: settings.systemID,
                reportID: settings.reportID,
                viewPortHeight: settings.viewPortHeight,
                viewPortWidth: settings.viewPortWidth,
                uaString: settings.uaString,
                importance: settings.importance,
                ref: settings.ref,
                store: settings.store
            },
            function (data) {
                //console.log("Tenon success");
            })
            .fail(function () {
                //console.log("Tenon error");
            });
    });

}(jQuery));

