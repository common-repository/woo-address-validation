=== Global Address Validation for WooCommerce ===
Contributors: snapcx,ubercx
Tags: woocommerce, global address validation, address correction, delivery address, shipping address validation, avs
Requires at least: 4.0
Tested up to: 4.9.7
WC requires at least: 3.2.0
WC tested up to: 3.4.4
Stable tag: 1.3.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
USA and Global Address Validation. FREE WooCommerce plugin providing real time global address validation and correction at checkout.
== Description ==

Global Address Validation is WooCommere Plugin that validates your customers shipping address at time of checkout. Don't waste your time with undelivered packages or having to reach out to a customers to get the correct address - have it entered correctly the first time. We support USA, Canada, UK, Europe and rest of world addresses. Your customers will have satisfactory and upscaled checkout experience. All you need to install this free plugin and open FREE Trial account [here](https://snapcx.io/pricing?solution=avs&utm_source=wordpress&utm_medium=landing&utm_campaign=avs).  [Note: Active paid subscription required, after trial period is over.]

**To get started** 

1. First install this FREE plugin and Activate it. (You need FREE TRIAL API Subscription though. See plans [here](https://snapcx.io/pricing?solution=avs&utm_source=wordpress&utm_medium=landing&utm_campaign=avs))
1. <a href="https://snapcx.io/pricing?solution=avs">Sign up for an snapCX subscription plan</a> to get an API key, and 
1. (Subscription is monthly PAID plan but you can try out with FREE TRIAL with No Credit Card required.)
1. Go to your Plugin configuration page (inside woocommerce settings menu), and save your API key & enable features.
1. [Remember] After TRIAL period is over, automatic invoice will be sent for next billing cycle.


**Key Features:**

* Automatically verify the customers address at checkout.
* Provide real time validated address suggestions to the customer.
* It will validate for both registered customer and guest customer checkout. 
* You don't need to do any customization or post install steps after installing plugin. It automatically integrates with WooCommerce checkout pages.
* It doesn't use workarounds of using google map API, like many other plugins.
* It doesn't force you to open USPS or UPS account etc.
* End undelivered packages.
* List of countries supported [here](https://snapcx.io/docs/avs_countries)

**Demo server with installed plugin**
<a href="http://woocommerce-demo.snapcx.io/shop/awesome-blue-shirt?utm_source=wordpress&utm_medium=landing&utm_campaign=avs" target="_blank">Click here</a>

= Demonstration Video, How it works =
[youtube https://www.youtube.com/watch?v=q_Tvzf3kU_g]

= Related Plugins = 
Compliment your post-sale experience of your customers by providing them embedded shipping tracking. See our other plugin for [snapCX free shipping tracking](https://wordpress.org/plugins/ubercx-shipping-tracking/)

== Frequently Asked Questions ==

= What countries does the plugin support? =
Recently we added support almost countries all over the world. For few countries, premises or street level validation is not possible. Please see list of all countries and their validation level [here](https://snapcx.io/docs/avs_countries)

= Why do I need an snapCX Developer Account? =

Firstly the accounts are free, no credit card required for trial period. We use an account as we have a set of back-end services that provide the address information. We have all plans with TRIAL period. We have pricing plans, suitable for all store sizes, whether you are just starting out or well estabilised store. See pricing plans and follow links to sign up [here](https://snapcx.io/pricing?solution=avs&utm_source=wordpress&utm_medium=landing&utm_campaign=avs).  

= How can I get help for this plugin? =  

snapCX provides email level support. Simply send us any message using [contact us form](https://snapcx.io/contact?utm_source=wordpress&utm_medium=landing&utm_campaign=avs) and we'll get back to you as soon as possible.


== Installation ==
= Manual Installation =
1.	Download and unzip the plugin
2.	Go to your website's WordPress Dashboard and click on the menu "Plugins" -> "Add New"
3.	Click the "Upload Plugin" link at the top of the page.
4.	Choose the file you downloaded and click "Install Now". Remaining instructions are covered in the section titled "Activation" below.
		
= Automatic Installation =
1.	Go to your website's WordPress Dashboard and click on "Plugins" -> "Add New"
2.	In the "Search Plugins" bar enter "Address validation" or only "snapCX" to find the Global Address validation Tracking Plugin by snapCX.
3.	Click "Install Now" to install the plugin.
		
= Activation =
1.	Before activation please make sure that WooCommerce is activated. 
2.	Upon installation you will see a link titled "Activate Plugin". Click it to activate the plugin. 
3.	Locate the "snapCX Address Validation" sub-menu under WooCommerce menu on the admin dashboard and enter the User Key. You need to use your developer key from snapcx.io account or open FREE account to get the User Key [here](https://snapcx.io/pricing?solution=avs&utm_source=wordpress&utm_medium=landing&utm_campaign=avs). 
4.	Select Yes for the Enabled field and click "Submit".
5. If you subscription plan, supports outside USA countries, then select checkbox for global address lookups too. 

**Demo server with installed plugin**
<a href="http://woocommerce.snapcx.io?utm_source=wordpress&utm_medium=landing&utm_campaign=avs" target="_blank">Click here</a>

= Demonstration Video, How it works =
[youtube https://www.youtube.com/watch?v=q_Tvzf3kU_g]


== Upgrade Notice ==

= 1.3.3 =
Minor bug fix release. Prev version didn't have all files.

= 1.3.2 =
Now plugin validates API key (user key), every time it is changed. 
Testing against latest version of woocommerce & wordpress.

= 1.3.1 =
Moved settings to WooCommerce settings menu. 
Testing against latest version of woocommerce & wordpress.

= 1.2.5 =
Now settings link is visible on plugin page and WooCommerce v3.3.x

= 1.2.4 =
Now compatible with latest wordpress (4.0+) and latest WooCommerce v3.2.6

= 1.2.3 =
Now compatible with latest wordpress (2.8+) and latest WooCommerce v3.1.1

= 1.2.2 = 
Fixed one bug of outside USA country validation, where user was stuck on validation screen due to hard coded country code to US. 

= 1.2.1 = 
Fixed one edge case, where due to spaces in zipcode (or postcode), checkout flow got stuck in address validation. 

= 1.2.0 =
Major upgrade, now with global address validation. And it's not compatible with latest wordpress and woocommerce versions. 

== Screenshots ==

1. Plugin Settings page. Enable/disable the plugin and enter your user key.
2. Example Verification. In this example the plugin has provided two corrected address for the customer.
3. Another example, where customer entered non-existent address and service is not able to find suggestions.
4. My account page on snapcx.io, where you get your user_key. 

== Changelog ==

= 1.3.3 =
1. Minor bug fix release. Prev version didn't have all files.

= 1.3.2 =
1. Now plugin validates API key (user key), every time it is changed. 
2. Testing against latest version of woocommerce & wordpress.

= 1.3.1 =
1. Moved settings to WooCommerce settings menu. 
2. Testing against latest version of woocommerce & wordpress.

= 1.2.5 =
1. Now settings link is visible on plugin page
2. Tested with latest WooCommerce and Wordpress versions. 

= 1.2.4 =
1. Tested with wordpress 4.9.2 version
2. Tested with WooCommerce v3.2.6

= 1.2.3 =
1. Tested with wordpress 2.8 version
2. Tested with WooCommerce v3.1.1

= 1.2.2 = 
Fixed one bug of outside USA country validation, where user was stuck on validation screen due to hard coded country code to US. 

= 1.2.1 = 
* Fixed one defect, due to spaces in zipcode (or postcode), checkout flow got stuck in address validation. 

= 1.2.0 =
* Removed deprecated functions from the old version.
* Added capabilities for GLOBAL address lookups. (Total 220+ countries)

= 1.1.0 =
* Rebranding of uberCX to snapCX. No functionality changed and no bugs fixed, compare to previous release.

= 1.0.1 =
* Bug fixed. Now it does address validation for unregistered or guest customers too.

= 1.0.0 =
* First release of plugin!
* Fully functional for validating USA addresses.
