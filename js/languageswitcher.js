jQuery(document).ready(function($) {
	$.languageswitcher();
});

(function($) {

	var $switches = null;
	var $singleswitches = null;
	var $multipleswitches = null;

	var $texts = null;

	var languages = new Array();
	var language = '';
	
	var configuration = {
		localStorage: 'languageswitcher.language',
		spanActive: '&#9660',
		spanInactive: '&#9654'
	}
	
	/**
	 * Toggle switches and texts to new global language.
	 */
	function toggleLanguage() {
		
		// set class and arrow direction on single switches
		$singleswitches.each(function() {
			$(this).data('language') == language ? 
				$(this).addClass('active').children('.arrow').html(configuration.spanActive) : $(this).removeClass('active').children('.arrow').html(configuration.spanInactive);
		});
		
		// set class and tag content on multiple switches
		$multipleswitches.each(function() {
			$(this).addClass('active').children('.arrow').html(configuration.spanActive);

			if ($(this).data('language') != language) {
				$(this).data('language', language);
				$(this).children('.language').html(language);
			}
		});
		
		// show or hide text
		$texts.each(function() {
			$(this).toggle($(this).data('language') == language);
		});
	};
	
	/**
	 * Languageswitcher
	 */
	$.languageswitcher = function() {

		// variables
		$switches = $('.languageswitcher.switch');
		$singleswitches = $('.languageswitcher.single.switch');
		$multipleswitches = $('.languageswitcher.multiple.switch');
		
		$texts = $('.languageswitcher.text');
		
		// find languages
		$texts.each(function() {
			var data = $(this).data('language');
			if (languages.indexOf(data) == -1) {
				languages.push(data);
			}
		});
		
		// local storage
		language = $.inArray(localStorage.getItem(configuration.localStorage), languages) != -1 ?
			localStorage.getItem(configuration.localStorage) : languages[0];

		// toggle to new language
		localStorage.setItem(configuration.localStorage, language);
		toggleLanguage();
		
		// change language on click
		$switches.click(function() {
			
			var data = $(this).data('language');
			
			if (data != language) {
				language = data
			}
			else {
				var length = languages.length;
				while (data == language) {
					var i = languages.indexOf(language);
					language = languages[(i + 1) % length];
				}
			}

			// toggle to new language
			localStorage.setItem(configuration.localStorage, language);
			toggleLanguage();
		});
	};
})(jQuery);