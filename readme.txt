=== Theme to Browser (T2B) Control ===
Contributors: federico_jacobi
Donate link: http://www.federicojacobi.com/
Tags: themes, browser, design, control, browser control, mobile, blackberry, ps3, ie, internet explorer
Requires at least: 3.4
Tested up to: 4.0
Stable tag: 1.0

Displays different themes based on the browser used.

== Description ==

Helps you use a different theme depending on the browser your site is viewed on. This is great for things like having a dedicated theme for mobile devices, but also is a great way to cheat your way out of CSS hacks and browser specific annoyances ( IE anybody? ). Even though themes are usually designed to be cross browser and CSS fine tuning is part of it, sometimes you do not have the time to spend trying to figure out a way to make the pages behave across browsers.

PLEASE vote and/or rate if it works for you or let me know if there's a fix needed at web[at]federicojacobi.com.

Supported browsers for now: Internet Explorer, FireFox, Chrome, Opera, iPad/iPhone/iPod, Safari, Playstation 3, and BlackBerries.

Thanks to Drazen Mokic, Nicholas McQuillin and Paul Gregory for their help and suggestions.

== Installation ==

1. Upload `t2b.php` to the `/wp-content/plugins/t2b/` directory (create if necessary)
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Now there will be a new option on the Appearance tab ... that's where the magic works

== Frequently Asked Questions ==
What browsers are detected?
Internet Explorer, FireFox, Opera, iPad/iPhone/iPod, Safari, BlackBerry

Can I add other browsers?
If you know PHP yes ... just got to the code and modify the first few lines, otherwise, drop me a line at web[at]federicojacobi.com and I'll take care of it for you.

What about browser versions (ie5, ie6, operamini) ?
Not yet, but you can add your own. See question 2. You can alternatively use the Theme to Browser Control - IE Pack to handle versions of IE. More packs to come!

What if the browser is not detected?
It will show the default theme.

What is the default theme?
Its the theme selected in the Appearance / Theme menu.

How do I change what browser gets what theme?
In the Appearance tab under T2B Control ... that's where the magic happends.



== Screenshots ==

1. Easy selection based on browser

== Changelog ==
= 1.0 =
A couple of security fixes (nothing huge) and minor logic change. Also added filters so the plugin is pluggable itself. Moved to 1.0 as this is now fairly mature :-)
Removed BlackBerry8310 from the list of browsers.

= 0.5 =
Complete modernization and rewrite. Cleared a bunch of notices and deprecated functions. 
Added debug mode so y'all can test your custom regex against the current browser, and verify in the frontend if the proper theme is getting loaded.

= 0.4 =
A couple of bug fixes.

= 0.3 =
Added "Default theme" behavior. A little bit of beautification work. Playstation3 added.

= 0.21 =
Child themes now supported. Important fix. iPad added to browser list.

= 0.2 =
BlackBerry8310 and All BlackBerry models added.

= 0.1 =
First version published. Yay!