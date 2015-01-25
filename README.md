#silverstripe-tenon
Check the accessibility of your SilverStripe site with this module that integrates with Tenon (www.tenon.io).

Read the Tenon Quick Start Guide for full details (http://www.tenon.io/documentation/quick-start.php).

##Installation
1. Install this module in the /tenon subdirectory of your web site.
2. Include /tenon/javascript/tenon_post.js on every public page.
3. Run /dev/build?flush=all
4. Register with www.tenon.io to get your own API key.
5. Open the *Tenon* tab on the CMS Settings page and paste your key into the *Tenon API Key* text box.
6. Adjust the other CMS settings as desired.

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
Whenever a page is loaded a hash of the DOM is generated. The page URL and the hash are queried in the Tenon Hash table in your Silverstripe database. 
If there's a match, nothing happens. Otherwise the page is analysed and the hash is added to the Tenon Hash table so it's not analysed again until it changes.
 
Analysis involves sending either the page URL or the page HTML (configurable in *Settings->Tenon*) to http://tenon.io/api/ and processing the results in four steps:

1. Any HTTP response other than 200 is ignored.
2. If Tenon itself returns a response other than *200 Success* this is logged to the Tenon Error table in your Silverstripe database and processing ends.
3. If the response **is** *200 Success*, any existing entries for the page URL are deleted from the Tenon Error table and steps 3 and 4 begin.
4. Any reported Javascript errors are added: these can block Tenon's page analysis and effort should be made to resolve them.
5. Finally accessibility errors and warnings are put into the Tenon Error table.

To view the results, open the *Tenon Results* tab in your Silverstripe Admin area.

##Authors
This module was written for Govt.nz, a *Common Web Platform* project of the New Zealand Government. 
It's made available to the wider Silverstripe community in the interests of web accessibility.


