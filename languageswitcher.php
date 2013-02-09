<?php 
/*
Plugin Name: Languageswitcher
Plugin URI: http://wordpress.org/extend/plugins/languageswitcher/
Description: After setting two tags, you can use them like normal HTML tags in the editor (only in text mode) to enter your post in different languages. Furthermore a special switch element can be inserted.
Version: 0.2
Author: Sven Hesse
Author URI: http://svenhesse.de
Text Domain: languageswitcher
Domain Path: /i18n
License: GPL v2 or later
*/

/*  
 * Copyright 2013  Sven Hesse  (email : languageswitcher@svenhesse.de)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!class_exists(Languageswitcher)) {
	
	/**
	 * Languageswitcher Class.
	 * 
	 * @author svenhesse
	 */
	class Languageswitcher {
		
		/**
		 * Current plugin version.
		 * 
		 * @var string
		 */
		const VERSION = '0.2';
		
		/**
		 * Debug mode.
		 * 
		 * @var boolean
		 */
		const DEBUG = false;
		
		/**
		 * Textdomain for i18n.
		 * 
		 * @var string
		 */
		private $textdomain = 'languageswitcher';
		
		/**
		 * Name of plugins options.
		 * 
		 * @var string
		 */
		private $settings_name = 'languageswitcher_options';
		
		/**
		 * Singleton instance.
		 *
		 * @var object
		 */
		private static $_instance;
		
		/**
		 * Singleton.
		 *
		 * @return object
		 */
		public static function getInstance() {
			if (!self::$_instance) {
				self::$_instance = new Languageswitcher();
			}
			return self::$_instance;
		}
		
		/**
		 * Constructor.
		 */
		private function __construct() {

			// translation
			load_plugin_textdomain($this->textdomain, false, dirname(plugin_basename(__FILE__)).'/i18n/' );
			
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
			$options = get_option($this->settings_name);
			$languages = array($options['language_1'], $options['language_2']);
		
			$js = '';
			$js.= '<script type="text/javascript">';
			foreach ($languages as $language) {
				$js.= 'QTags.addButton("tag_'.$language.'", "'.$language.'", "<'.$language.'>", "</'.$language.'>");';
				$js.= 'QTags.addButton("tag_'.$language.'_switch", "'.$language.'-switch", "<'.$language.'-switch>", "</'.$language.'-switch>");';
			}
			$js.= 'QTags.addButton("tag_multiple_switch", "multiple-switch", "<multiple-switch>", "</multiple-switch>");';
			$js.= '</script>';
		
			echo $js;
		}
		
		/**
		 * Add settings page to wordpress menu.
		 */
		function add_settings_page() {
			add_options_page(__('Languageswitcher settings', $this->textdomain), 'Languageswitcher', 'manage_options', 'languageswitcher', array(&$this, 'settings_page'));
		}
		
		/**
		 * Define settings options.
		 */
		function settings() {
			
			$settings_sections = array(
				array(
					'id' => 'languageswitcher_general', 
					'title' => __('General', $this->textdomain),
					'callback' => 'general_info'
				),
				array(
						'id' => 'languageswitcher_behaviour',
						'title' => __('Behaviour', $this->textdomain),
						'callback' => 'behaviour_info'
				),
				array(
					'id' => 'languageswitcher_colors', 
					'title' => __('Color', $this->textdomain),
					'callback' => 'color_info'
				)
			);
			
			$settings_fields = array(
				$settings_sections[0]['id'] => array(
					array(
						'id' => 'language_1',
						'label' => __('Tag for first language', $this->textdomain),
						'default' => 'english',
						'type' => 'input',
					),
					array(
							'id' => 'language_2',
							'label' => __('Tag for second language', $this->textdomain),
							'default' => 'german',
							'type' => 'input',
					),
				),
				$settings_sections[1]['id'] => array(
					array(
							'id' => 'shadow',
							'label' => __('Enable hover shadow', $this->textdomain),
							'default' => 'no',
							'type' => 'radio',
							'options' => array(
								'no' => __('No', $this->textdomain),
								'yes' => __('Yes', $this->textdomain),
							)
					),
					array(
							'id' => 'ucfirst',
							'label' => __('Uppercase first letter', $this->textdomain),
							'default' => 'yes',
							'type' => 'radio',
							'options' => array(
								'no' => __('No', $this->textdomain),
								'yes' => __('Yes', $this->textdomain),
							)
					),
				),
				$settings_sections[2]['id'] => array(
					array(
						'id' => 'color_text_active',
						'label' => __('Text (active)', $this->textdomain),
						'default' => '#000000',
						'type' => 'input',
					),
					array(
						'id' => 'color_background_active',
						'label' => __('Background (active)', $this->textdomain),
						'default' => '#CCCCCC',
						'type' => 'input',
					),
					array(
						'id' => 'color_text_inactive',
						'label' => __('Text (inactive)', $this->textdomain),
						'default' => '#BBBBBB',
						'type' => 'input',
					),
					array(
						'id' => 'color_background_inactive',
						'label' => __('Background (active)', $this->textdomain),
						'default' => '#EEEEEE',
						'type' => 'input',
					),
				)
			);
			
			register_setting($this->settings_name, $this->settings_name);
			
			// add settings sections
			foreach($settings_sections as $section) {
				add_settings_section($section['id'], $section['title'], array(&$this, $section['callback']), 'languageswitcher');
			}
		
			// add settings fields
			foreach($settings_fields as $key => $section) {
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
				<h2><?php _e('Languageswitcher settings', $this->textdomain); ?></h2>
				
				<?php 
				if (self::DEBUG) {
					var_dump(get_option($this->settings_name));
				}
				?>
				<form method="post" action="options.php">
					<?php settings_fields($this->settings_name); ?>
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
			echo '<p>'._e('Set your tags to use them in the editor (text mode) afterwards.', $this->textdomain).'</p>';
		}
		
		/**
		 * Display color options info.
		 */
		function color_info() {
			echo '<p>'._e('Set styling options. Use hexadezimal codes (like <i>#00FF00</i>) or color names (like <i>red</i>).', $this->textdomain).'</p>';
		}
		
		/**
		 * Display behaviour options info.
		 */
		function behaviour_info() {
			echo '<p>'._e('Set some behaviour options.', $this->textdomain).'</p>';
		}
		
		/**
		 * Handle input options.
		 * 
		 * @param array $field
		 */
		function callback_input($field) {
			$option = get_option($this->settings_name)[$field['id']];
			echo "<input id='".$field['id']."' name='".$this->settings_name."[".$field['id']."]' size='40' type='text' value='".$option."'>";
		}
		
		/**
		 * Handle input options.
		 *
		 * @param array $field
		 */
		function callback_radio($field) {
			$options = get_option($this->settings_name);
			foreach($field['options'] as $key => $option) {
				$checked = $key == $options[$field['id']] ? 'checked' : 'unchecked';
				echo "<input id='".$field['id']."' name='".$this->settings_name."[".$field['id']."]' type='radio' value='".$key."' ".$checked." > ".$option."<br />";
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
				$options = get_option($this->settings_name);
				$languages = array($options['language_1'], $options['language_2']);
				$ucFirst = $options['ucfirst'] == 'yes';

				if (strpos($content, '<multiple-switch>') && strpos($content, '</multiple-switch>')) {
					
					// reple opening switch elements
					$needles = array('<multiple-switch>');
					$content = str_replace($needles, '<div class="languageswitcher multiple switch" data-language="'.($ucFirst ? ucfirst($languages[0]) : $languages[0]).'"><span class="arrow">&#9660;</span><span class="language">'.($ucFirst ? ucfirst($languages[0]) : $languages[0]).'</span>', $content);
	
					// replace closing language switch elements
					$needles = array('</multiple-switch><br />', '</multiple-switch>');
					$content = str_replace($needles, '</div>', $content);
				};				
				
				foreach ($languages as $key => $language) {
					if (strpos($content, '<'.$language.'-switch>') && strpos($content, '</'.$language.'-switch>')) {
						
						// replace opening switch elements
						$needles = array('<'.$language.'-switch>');
						$content = str_replace($needles, '<div class="languageswitcher single switch" data-language="'.($ucFirst ? ucfirst($language) : $language).'"><span class="arrow">&#9654;</span><span class="language">'.($ucFirst ? ucfirst($language) : $language).'</span>', $content);
					
						// replace closing language switch elements
						$needles = array('</'.$language.'-switch><br />', '</'.$language.'-switch>');
						$content = str_replace($needles, '</div>', $content);
					}
					
					if (strpos($content, '<'.$language.'>') && strpos($content, '</'.$language.'>')) {
						
						// replace opening tag elements
						$needles = array('<'.$language.'>');
						$content = str_replace($needles, '<div class="languageswitcher text" data-language="'.($ucFirst ? ucfirst($language) : $language).'">', $content);
						
						// replace closing tag elements
						$needles = array('</'.$language.'><br />', '</'.$language.'>');
						$content = str_replace($needles ,'</div>', $content);
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
			$options = get_option($this->settings_name);
			$languages = array($options['language_1'], $options['language_2']);
			$ucFirst = $options['ucfirst'] == 'yes';
			
			foreach ($languages as $key => $language) {
				if (strpos($content, '<'.$language.'>') && strpos($content, '</'.$language.'>')) {
					
					// replace closing tag elements
					$needles = array('<'.$language.'>');
					$content = str_replace($needles, '<div class="languageswitcher switch"><span class="arrow">&#9660;</span><span class="language">'.($ucFirst ? ucfirst($language) : $language).'</span></div>', $content);

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
Languageswitcher::getInstance();;