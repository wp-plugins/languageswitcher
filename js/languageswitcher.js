jQuery(document).ready(function($) {
	$.languageswitcher();
});

(function($) {

	var isLanguage1 = true;
	var $languageSwitcher;
		
	function showText(isLanguage1) {
		
		$language1 = $('.languageswitcher.language1');
		$language2 = $('.languageswitcher.language2');
		
		isLanguage1 ? $language1.addClass('active') : $language1.removeClass('active');
		isLanguage1 ? $('span', $language1).html('&#9660;') : $('span', $language1).html('&#9654;');
		$('div.text.language1').toggle(isLanguage1);
		
		isLanguage1 ? $language2.removeClass('active') : $language2.addClass('active');
		isLanguage1 ? $('span', $language2).html('&#9654;') : $('span', $language2).html('&#9660;');
		$('div.text.language2').toggle(!isLanguage1);

	};
	
	$.languageswitcher = function() {
		$languageSwichter = $('.languageswitcher');

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