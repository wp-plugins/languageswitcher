<?php
/*
 Plugin Name: Languageswitcher
Description: After setting two tags, you can use them like normal HTML tags in the editor (only in text mode) to enter your post in different languages. Furthermore a special switch element can be inserted.
Version: 0.1.1
Author: Sven Hesse
Author URI: http://svenhesse.de
License: GPL v2 or later
*/

/*  Copyright 2013  Sven Hesse  (email : languageswitcher@svenhesse.de)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('init', 'languageswitcher_init');

function languageswitcher_init() {
	add_action('wp_enqueue_scripts', 'enqueue_scripts_and_styles');

	add_action( 'admin_menu', 'languageswitcher_add_settings_page' );
	add_filter( 'admin_init', 'languageswitcher_settings');
	
	add_action( 'admin_print_footer_scripts', 'appthemes_add_quicktags', 100 );
	
	if (!is_feed()) {
		add_filter('the_content', 'filter_content');
	} else {
		add_filter('the_content_feed', 'filter_content_feed');
	}
	//add_action('the_title', array($this, 'filter_title'));
}

function appthemes_add_quicktags() {
	$options = get_option('languageswitcher_options');
	$languages = array($options['language_1'], $options['language_2']);
	
	$js = '';
    $js.= '<script type="text/javascript">';
    foreach ($languages as $key => $language) {
		$js.= 'QTags.addButton("eg_language'.($key+1).'", "'.$language.'", "<'.$language.'>", "</'.$language.'>");';
		$js.= 'QTags.addButton("eg_language'.($key+1).'_switch", "'.$language.'-switch", "<'.$language.'-switch>", "</'.$language.'-switch>");';
    }	
	$js.= '</script>';
    
    echo $js;
}

function enqueue_scripts_and_styles() {
	wp_enqueue_script('languageswitcher', plugins_url( '/js/languageswitcher.js', __FILE__ ), array('jquery'));
	wp_enqueue_style('languageswitcher', plugins_url( '/css/style.php', __FILE__ ));
}

function languageswitcher_add_settings_page() {
	add_options_page('Languageswitcher Settings', 'Languageswitcher', 'manage_options', 'languageswitcher', 'languageswitcher_settings_page');
}

function languageswitcher_settings_page() {
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2>Languageswitcher Settings</h2>
		<form method="post" action="options.php">
			<?php settings_fields('languageswitcher_options'); ?>
			<?php do_settings_sections('languageswitcher'); ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

function languageswitcher_settings() {
	register_setting('languageswitcher_options', 'languageswitcher_options');

	add_settings_section('languageswitcher_general', 'General settings', 'languageswitcher_options_general_info', 'languageswitcher');
	add_settings_field('language_1', 'Tag for first Language', 'languageswitcher_language_1', 'languageswitcher', 'languageswitcher_general');
	add_settings_field('language_2', 'Tag for second Language', 'languageswitcher_language_2', 'languageswitcher', 'languageswitcher_general');

	add_settings_section('languageswitcher_colors', 'Color settings', 'languageswitcher_options_color_info', 'languageswitcher');
	add_settings_field('color_text_active', 'Text (active)', 'languageswitcher_color_text_active', 'languageswitcher', 'languageswitcher_colors');
	add_settings_field('color_background_active', 'Background (active)', 'languageswitcher_color_background_active', 'languageswitcher', 'languageswitcher_colors');
	add_settings_field('color_text_inactive', 'Text (inactive)', 'languageswitcher_color_text_inactive', 'languageswitcher', 'languageswitcher_colors');
	add_settings_field('color_background_inactive', 'Background (inactive)', 'languageswitcher_color_background_inactive', 'languageswitcher', 'languageswitcher_colors');
}

function languageswitcher_options_general_info() {
	$html = "";
	$html.= '<p>Set Tags. You can use the set tags to seperate your language afterwards.</p>';
	$html.= '<p>Insert your content in the editor (text mode) between &lt;english&gt; and &lt;/english&gt; for one and between &lt;german&gt; and &lt;/german&gt;. for the other set language.<br />';
	$html.= 'Enter a &lt;english-switch&gt;&lt;/english-switch&gt; to provide a switch element. In your post, a click on the switch element hides the other language.</p>';
	$html.= '<p>Replace english and german in this example with your set tags.</p>';
	
	echo $html;
}

function languageswitcher_options_color_info() {
	echo '<p>Style the switch element individually. Use hexadezimal codes (like <i>#00FF00</i>) oder color names (like <i>red</i>).</p>';
}

function languageswitcher_options_input($id = '', $default = '') {
	$options = get_option('languageswitcher_options');
	if (strlen($options[$id]) == 0) {
		$options[$id] = $default;
	}
	echo "<input id='{$id}' name='languageswitcher_options[{$id}]' size='40' type='text' value='".$options[$id]."' />";
}

function languageswitcher_language_1() {
	languageswitcher_options_input('language_1', 'english');
}
function languageswitcher_language_2() {
	languageswitcher_options_input('language_2', 'german');
}
function languageswitcher_color_text_active() {
	languageswitcher_options_input('color_text_active', '#000000');
}
function languageswitcher_color_text_inactive() {
	languageswitcher_options_input('color_text_inactive', '#CCCCCC');
}
function languageswitcher_color_background_active() {
	languageswitcher_options_input('color_background_active', '#BBBBBB');
}
function languageswitcher_color_background_inactive() {
	languageswitcher_options_input('color_background_inactive', '#EEEEEE');
}

function filter_content($content) {
	$options = get_option('languageswitcher_options');
	$languages = array($options['language_1'], $options['language_2']);
	$ucFirst = true;

	foreach ($languages as $key => $language) {
		if (strpos($content, '<'.$language.'-switch>') && strpos($content, '</'.$language.'-switch>')) {

			$needles = array('<p><'.$language.'-switch>', '<'.$language.'-switch>');
			$content = str_replace($needles, '<div class="languageswitcher switch language'.($key+1).'"><span>&#9654;</span>'.($ucFirst ? ucfirst($language) : $language).'', $content);
		
			$needles = array('</'.$language.'-switch><br />', '</'.$language.'-switch>');
			$content = str_replace($needles, '</div>', $content);
		}
		
		if (strpos($content, '<'.$language.'>') && strpos($content, '</'.$language.'>')) {
		
			$needles = array('<'.$language.'></p>', '<'.$language.'>');
			$content = str_replace($needles, '<div class="languageswitcher text language'.($key+1).'">', $content);
			
			$needles = array('</'.$language.'></p>', '</'.$language.'>');
			$content = str_replace($needles ,'</div>', $content);
		}
	}

	return $content;
}

function filter_content_feed($content) {
	$options = get_option('languageswitcher_options');
	$languages = array($options['language_1'], $options['language_2']);
	$ucFirst = true;

	foreach ($languages as $key => $language) {
		if (strpos($content, '<'.$language.'>') && strpos($content, '</'.$language.'>')) {
			$content = str_replace('<'.$language.'>', '<div class="languageswitcher text language'.($key+1).'"><span>&#9660;</span>'.($ucFirst ? ucfirst($language) : $language).'</div>', $content);
			$content = str_replace('</'.$language.'>', '</div>', $content);
		}
	}
	
	return $content;
}