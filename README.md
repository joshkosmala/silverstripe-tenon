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

##How it works
Whenever a page is loaded a hash of the DOM is generated. The page URL and the hash are queried in the Tenon Hash table in your Silverstripe database. 
If there's a match, nothing happens. Otherwise the page is analysed and the hash is added to the Tenon Hash table so it's not analysed again until it changes.
 
Analysis involves sending the page URL to http://tenon.io/api/ and processing the results in four steps:

1. If there's a response other than *200 Success* this is logged to the Tenon Error table in your Silverstripe database and processing ends.
2. If the response **is** *200 Success*, any existing entries for the page URL are deleted from the Tenon Error table and steps 3 and 4 begin.
3. Any reported Javascript errors are added: these can block Tenon's page analysis and effort should be made to resolve them.
4. Finally accessibility errors and warnings are put into the Tenon Error table.

To view the results, open the *Tenon Results* tab in your Silverstripe Admin area.

##Authors
This module was written for Govt.nz, a *Common Web Platform* project of the New Zealand Government. 
It's made available to the wider Silverstripe community in the interests of web accessibility.


