<?php 
/*
Plugin Name: Languageswitcher
Plugin URI: http://wordpress.org/extend/plugins/languageswitcher/
Description: After setting two tags, you can use them like normal HTML tags in the editor (only in text mode) to enter your post in different languages. Furthermore a special switch element can be inserted.
Version: 0.1.2
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

if (!class_exists(Languageswitcher)) {
	
	/**
	 * Languageswitcher Class.
	 * 
	 * @author svenhesse
	 */
	class Languageswitcher {
		
		/**
		 * Current plugin version
		 * 
		 * @var String
		 */
		const VERSION = '0.1.2';
		
		var $settings_sections = array();
		
		var $settings_fields = array();
		
		/**
		 * Constructur.
		 */
		function __construct() {
			
			// set special links on plugin pages
			add_filter('plugin_row_meta', array(&$this, 'settings_link'), 10, 2);
				
			// load scripts and styles
			add_action('wp_enqueue_scripts', array(&$this, 'scripts_and_styles'));
			add_action('admin_print_footer_scripts', array(&$this, 'quicktags'), 100 );
			
			// set admin menu and options
			add_action('admin_menu', array(&$this, 'add_settings_page'));
			add_filter('admin_init', array(&$this, 'settings'));
						
			// filter content
			add_filter('the_content', array(&$this, 'filter_content'));
		}

		/**
		 * Set special links on plugin pages.
		 *
		 * @param array $links
		 * @param string $file
		 * @return string
		 */
		function settings_link($links, $file) {
			if($file == plugin_basename(__FILE__)) {
				$links[] = '<a href="options-general.php?page=languageswitcher">Settings</a>';
			}
			return $links;
		}
		
		/**
		 * Load scripts and styles.
		 */
		function scripts_and_styles() {
			wp_enqueue_script('languageswitcher', plugins_url( '/js/languageswitcher.js', __FILE__ ), array('jquery'));
			wp_enqueue_style('languageswitcher', plugins_url( '/css/style.php', __FILE__ ));
		}
		
		/**
		 * Add quicktags to text editor.
		 */
		function quicktags() {
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
		
		/**
		 * Add settings page to wordpress menu.
		 */
		function add_settings_page() {
			add_options_page('Languageswitcher Settings', 'Languageswitcher', 'manage_options', 'languageswitcher', array(&$this, 'settings_page'));
		}
		
		/**
		 * Define settings options.
		 */
		function settings() {
			
			$this->settings_sections = array(
				array(
					'id' => 'languageswitcher_general', 
					'title' => 'General Settings',
					'callback' => 'general_info'
				),
				array(
						'id' => 'languageswitcher_behaviour',
						'title' => 'Behaviour',
						'callback' => 'behaviour_info'
				),
				array(
					'id' => 'languageswitcher_colors', 
					'title' => 'Color Settings',
					'callback' => 'color_info'
				)
			);
			
			$this->settings_fields = array(
				$this->settings_sections[0]['id'] => array(
					array(
						'id' => 'language_1',
						'label' => 'Tag for first Language',
						'default' => 'english',
						'type' => 'input',
					),
					array(
						'id' => 'language_2',
						'label' => 'Tag for second Language',
						'default' => 'german',
						'type' => 'input',
					),
					array(
							'id' => 'language_2',
							'label' => 'Tag for second Language',
							'default' => 'german',
							'type' => 'input',
					),
				),
				$this->settings_sections[1]['id'] => array(
					array(
							'id' => 'shadow',
							'label' => 'Show hover shadow',
							'default' => 'no',
							'type' => 'radio',
							'options' => array(
								'Yes',
								'No'
							)
					),
				),
				$this->settings_sections[2]['id'] => array(
					array(
						'id' => 'color_text_active',
						'label' => 'Text (active)',
						'default' => '#000000',
						'type' => 'input',
					),
					array(
						'id' => 'color_background_active',
						'label' => 'Background (active)',
						'default' => '#CCCCCC',
						'type' => 'input',
					),
					array(
						'id' => 'color_text_inactive',
						'label' => 'Text (inactive)',
						'default' => '#BBBBBB',
						'type' => 'input',
					),
					array(
						'id' => 'color_background_inactive',
						'label' => 'Background (inactive)',
						'default' => '#EEEEEE',
						'type' => 'input',
					),
				)
			);
			
			register_setting('languageswitcher_options', 'languageswitcher_options');
			
			// add settings sections
			foreach($this->settings_sections as $section) {
				add_settings_section($section['id'], $section['title'], array(&$this, $section['callback']), 'languageswitcher');
			}
		
			// add settings fields
			foreach($this->settings_fields as $key => $section) {
				foreach($section as $field) {
					add_settings_field($field['id'], $field['label'], array(&$this, 'callback_'.$field['type']), 'languageswitcher', $key, $field);
				}
			}
		}
		
		/**
		 * Set content of settings page.
		 */
		function settings_page() {
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
		
		/**
		 * Display general options information.
		 */
		function general_info() {
			$html = "";
			$html.= '<p>Set Tags. You can use the set tags to seperate your language afterwards.</p>';
			$html.= '<p>Insert your content in the editor (text mode) between &lt;english&gt; and &lt;/english&gt; for one and between &lt;german&gt; and &lt;/german&gt;. for the other set language.<br />';
			$html.= 'Enter a &lt;english-switch&gt;&lt;/english-switch&gt; to provide a switch element. In your post, a click on the switch element hides the other language.</p>';
			$html.= '<p>Replace english and german in this example with your set tags.</p>';
			
			echo $html;
		}
		
		/**
		 * Display color options info.
		 */
		function color_info() {
			echo '<p>Style the switch element individually. Use hexadezimal codes (like <i>#00FF00</i>) oder color names (like <i>red</i>).</p>';
		}
		
		/**
		 * Display behaviour options info.
		 */
		function behaviour_info() {
			echo '<p>Set some behaviour options.</p>';
		}
		
		/**
		 * Handle input options.
		 * 
		 * @param array $field
		 */
		function callback_input($field) {
			$options = get_option('languageswitcher_options');
			echo "<input id='".$field['id']."' name='languageswitcher_options[".$field['id']."]' size='40' type='text' value='".$options[$field['id']]."'>";
		}
		
		/**
		 * Handle input options.
		 *
		 * @param array $field
		 */
		function callback_radio($field) {
			$options = get_option('languageswitcher_options');
			
			foreach($field['options'] as $option) {
				$checked = $option == $options[$field['id']] ? 'checked' : 'unchecked';
				echo "<input id='".$field['id']."' name='languageswitcher_options[".$field['id']."]' type='radio' value='".$option."' ".$checked." > ".$option."<br />";
			}
		}
		
		/**
		 * Replace special plugin tags in content.
		 * 
		 * @param string $content
		 * @return string
		 */
		function filter_content($content) {

			if (is_feed()) {
				return $this->filter_content_feed($content);
			}
			else {
				$options = get_option('languageswitcher_options');
				$languages = array($options['language_1'], $options['language_2']);
				$ucFirst = true;
			
				foreach ($languages as $key => $language) {
					if (strpos($content, '<'.$language.'-switch>') && strpos($content, '</'.$language.'-switch>')) {
						
						// replace opening switch elements
						$needles = array('<'.$language.'-switch>');
						$content = str_replace($needles, '<span class="languageswitcher switch language'.($key+1).'"><span class="languageswitcher arrow">&#9654;</span>'.($ucFirst ? ucfirst($language) : $language).'', $content);
					
						// replace losing language switch elements
						$needles = array('</'.$language.'-switch><br />', '</'.$language.'-switch>');
						$content = str_replace($needles, '</span>', $content);
					}
					
					if (strpos($content, '<'.$language.'>') && strpos($content, '</'.$language.'>')) {
						
						// replace opening tag elements
						$needles = array('<'.$language.'>');
						$content = str_replace($needles, '<span class="languageswitcher text language'.($key+1).'">', $content);
						
						// replace closing tag elements
						$needles = array('</'.$language.'><br />', '</'.$language.'>');
						$content = str_replace($needles ,'</span>', $content);
					}
				}
			}
			
			return $content;
		}
		
		/**
		 * Replace special plugin tags in feed content.
		 * 
		 * @param string $content
		 * @return sting
		 */
		function filter_content_feed($content) {
			$options = get_option('languageswitcher_options');
			$languages = array($options['language_1'], $options['language_2']);
			$ucFirst = true;

			foreach ($languages as $key => $language) {
				if (strpos($content, '<'.$language.'>') && strpos($content, '</'.$language.'>')) {
					
					// replace closing tag elements
					$needles = array('<'.$language.'>');
					$content = str_replace($needles, '<div class="languageswitcher switch language'.($key+1).'"><span class="languageswitcher arrow">&#9660;</span>'.($ucFirst ? ucfirst($language) : $language).'</div>', $content);

					// replace closing tag elements
					$needles = array('</'.$language.'>');
					$content = str_replace($needles, '', $content);
				}
			}
			
			return $content;
		}
	}
}

// init plugin
if(class_exists('Languageswitcher')) new Languageswitcher();