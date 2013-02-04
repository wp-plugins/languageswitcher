jQuery(document).ready(function($) {
	$.languageswitcher();
});

(function($) {
	
	var $switches = null;

	var languages = new Array();
	var language = '';
	
	function toggleLanguage() {
		
		var $switches = $('.languageswitcher.switch');
		var $texts = $('.languageswitcher.text');
		
		$switches.each(function() {
			$(this).data('language') == language ? 
					$(this).addClass('active').children('.arrow').html('&#9660') : 
						$(this).removeClass('active').children('.arrow').html('&#9654;');
		});
		
		$texts.each(function() {
			$(this).toggle($(this).data('language') == language);
		});
	};
	
	$.languageswitcher = function() {
		
		var $switches = $('.languageswitcher.switch');
		
		$switches.each(function() {
			var data = $(this).data('language');
			if (languages.indexOf(data) == -1) {
				languages.push(data);
			}
		});
		
		language = localStorage.getItem("languageswitcher.language") ?
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