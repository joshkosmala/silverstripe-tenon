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

##Authors
**Josh Kosmala** josh@novaweb.co.nz
Props to **Leigh Harrison** leigh@elseapps.com for her input on this module
Developed for NZ Government. Made available to the wider Silverstripe and Common Web Platform community in the interests of web accessibility.
