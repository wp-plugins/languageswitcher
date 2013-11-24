=== Languageswitcher ===
Contributors: svenhesse
Donate link: 
Tags: language, switcher, bilingual, human, i18n, l10n, multilanguage, multilingual, admin,
Requires at least: 3.3
Tested up to: 3.8
Stable tag: 0.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides tags in the editor to enter text in different languages. Readers kann toggle between these languages by using the included switch element.

== Description ==

After setting two tags, you can use them like normal HTML tags in the editor (only in text mode) to enter your post in different languages. Furthermore a special switch element can be inserted.
 
* saving the selected language using local storage
* individual styling for switch element
* special output in feeds

== Installation ==

1. Upload the whole unpacked directory `languageswitcher` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to 'Languageswitcher' in 'Settings' and save your tags and styling.

== Frequently Asked Questions ==

= Why isn't it working?  =

Go to 'Languageswitcher' in 'Settings' and save your tags and styling initially.

== Screenshots ==

1. Selected first language
2. Selected second language
3. Using tags in text editor
4. Options

== Changelog ==

= 0.2.2 =
* [feedback] improved layout on feeds
* [bug] minor code typo fixed

= 0.2.1 =
* [bug] seldom php error fixed

= 0.2 =
* [feedback] multiple switch element
* [feedback] option to upper case first letter in switch element
* [feature] i18n with german language support
* [code] singleton pattern
* [code] javascript prepared to handle more than two languages

= 0.1.3 =
* [bug] hiding wrong elements when using spans disabled

= 0.1.2 =
* [bug] incompatibily to other plugins improved
* [feedback] option to active/deactiver hover shadow
* [code] improved settings arrays

= 0.1.1 = 
* [bug] minor html paragraph bugs fixed
* [feature] improved feed output
* [code] class structure
* [code] refactoring

= 0.1 = 
* [general] first release