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
	
	function toggleLanguage() {
		
		$singleswitches.each(function() {
				$(this).data('language') == language ? 
					$(this).addClass('active').children('.arrow').html('&#9660') : 
						$(this).removeClass('active').children('.arrow').html('&#9654;');
		});
		
		$multipleswitches.each(function() {
			$(this).addClass('active');

			if ($(this).data('language') != language) {
				$(this).data('language', language);
				$(this).children('.language').html(language);
			}
		});
		
		$texts.each(function() {
			$(this).toggle($(this).data('language') == language);
		});
	};
	
	$.languageswitcher = function() {

		$switches = $('.languageswitcher.switch');
		$singleswitches = $('.languageswitcher.single.switch');
		$multipleswitches = $('.languageswitcher.multiple.switch');
		
		$texts = $('.languageswitcher.text');
		
		$texts.each(function() {
			var data = $(this).data('language');
			if (languages.indexOf(data) == -1) {
				languages.push(data);
			}
		});
		
		language = $.inArray(localStorage.getItem("languageswitcher.language"), languages) != -1 ?
				localStorage.getItem("languageswitcher.language") : languages[0];
		
		localStorage.setItem("languageswitcher.language", language);
		toggleLanguage();
		
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

			localStorage.setItem("languageswitcher.language", language);
			toggleLanguage();
		});
	};
})(jQuery);