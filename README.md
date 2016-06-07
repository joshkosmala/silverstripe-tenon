#silverstripe-tenon
Check the accessibility of your SilverStripe site with this module that integrates with Tenon (www.tenon.io).

Read the Tenon Quick Start Guide for full details (http://www.tenon.io/documentation/quick-start.php).

##Requirements
composer require normann/gridfieldpaginatorwithshowall (works with af6b9ee2effcda4d351cacd3b6f1796026355240)

##Installation

```
composer require silverstripe/tenon
```

1. Install this module in the /tenon subdirectory of your web site.
2. Run /dev/build?flush=all
3. Register with www.tenon.io to get your own API key.
4. Open the *Tenon* tab on the CMS Settings page and paste your key into the *Tenon API Key* text box.
5. Pat yourself on the back

##Settings
The following settings are available in *Settings->Tenon*:
+ **Tenon API URL** At the time of writing this should be http://www.tenon.io/api/ - check the Tenon website if this doesn't work.
+ **Tenon API Key** You get this after registering with Tenon.
+ **Tenon Certainty Threshold** Each warning or error has a level of certainty. You can change this threshold to filter the results.
+ **Tenon WCAG Level** Read the details here: http://www.w3.org/WAI/intro/wcag
+ **Tenon Priority Cutoff** Some errors are more important than others. Adjust this value to filter the results.
+ **Tenon Source** The module can either send the page URL to Tenon, or send the HTML. The latter is particularly useful for development.
+ **Tenon JSON Response** For debugging your initial Tenon setup, you can send a summary log back to the web page and inspect it in the console.

##How it works

TenonPageExtension is automatically added to SiteTree, and triggers a checking process after a page write in draft (on CMS save). This process generates a copy of the saved page in draft mode, and sends this content to Tenon API to perform it's checks.

TenonResult objects are created in the database from Tenon's response, with an association to the page. In this way, Tenon results can be viewed directly on the page that was saved (under "Accessibility" tab.)  Only the most recent Tenon results are saved, as older results are no actionable.

The call to tenon can be configured to be synchronous or asynchronous. Synchronous calls have the advantage that when the page has finished saving the results are already there. Asynchronous calls have the advantage that the save is faster, and the page will generally need to be reloaded.

Note: this behaviour differs from earlier versions of the module, where requests to the front end of the site triggered requests to Tenon. This behaviour has been removed, as it gives content authors no ability to fix the reported issues before publishing.

## To do

 *  Determine how to reduce requests. Not every save requires a call to Tenon,
    especially in multiple-save edits of large content.
 *  Under some circumstances, there may be issues that Tenon reports
    repeatedly, but which are deemed acceptible by the product owner. It would
    be good to have a way to automatically exclude certain patterns.
    Consideration needs to be given to the fact that this is not black and
    white, in that the same error may be actionable in one circumstance but
    not another.
 *  Add a checkbox to the accessibility tab on each page that controls whether
    that page is checked with Tenon or not. This would be useful in the case
    of pages that are mostly composed of dynamically generated content (e.g.
    search pages), where the results may not be meaningful.

##Authors
**Josh Kosmala** josh@novaweb.co.nz
Props to **Leigh Harrison** leigh@elseapps.com for her input on this module
Developed for NZ Government. Made available to the wider Silverstripe and Common Web Platform community in the interests of web accessibility.
