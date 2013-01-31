jQuery(document).ready(function($) {
	$.languageswitcher();
});

(function($) {

	var isLanguage1 = true;
	var $languageSwitcher;
		
	function showText(isLanguage1) {
		
		$language1 = $('.languageswitcher.language1');
		$language2 = $('.languageswitcher.language2');
		
		if (isLanguage1) {
			$language1.addClass('active');
			$language2.removeClass('active');
			$('span.languageswitcher.arrow', $language1).html('&#9660;');
			$('span.languageswitcher.arrow', $language2).html('&#9654;');
		}
		else {
			$language1.removeClass('active');
			$language2.addClass('active')
			$('span.languageswitcher.arrow', $language1).html('&#9654;');
			$('span.languageswitcher.arrow', $language2).html('&#9660;');
		}
		
		$('.languageswitcher.text.language1').toggle(isLanguage1);
		$('.languageswitcher.text.language2').toggle(!isLanguage1);

	};
	
	$.languageswitcher = function() {
		$languageSwichter = $('.languageswitcher.switch');

		if (localStorage.getItem("isLanguage1")) {
			isLanguage1 = localStorage.getItem("isLanguage1") == "true";
		} 
		
		localStorage.setItem("isLanguage1", isLanguage1);
		showText(isLanguage1);

		$languageSwichter.click(function() {
			isLanguage1 = !isLanguage1;
			localStorage.setItem("isLanguage1", isLanguage1);
			showText(isLanguage1);
		});
	};
})(jQuery);