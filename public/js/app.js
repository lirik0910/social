document.addEventListener("DOMContentLoaded", function() {

	// toggle content functions
	var content = document.getElementsByClassName("content");
	content[0].addEventListener('click', toggleQuestion);

	function toggleQuestion(event) {
		var target = event.target;

		if (target.classList.contains("content__question")) {
			target.classList.toggle("question__active");
			var panel = target.nextElementSibling;
			if (panel.style.maxHeight) {
				panel.style.maxHeight = null;
			} else {
				panel.style.maxHeight = panel.scrollHeight + "px";
			}
		}
	}


	// -----start search-----
	var searchForm = document.getElementsByClassName("search__form")[0];
	searchForm.addEventListener("submit", sendSearchRequest, true);
	function sendSearchRequest(e) {
		e.preventDefault();
		let search_param = document.getElementsByClassName("search__input")[0].value;
		if (search_param.length > 0) {
			let xhttp = new XMLHttpRequest();
			let token = document.querySelector('meta[name="csrf-token"]').content;
			let body = 'search_param=' + encodeURIComponent(search_param) + '&locale=' + document.documentElement.lang;
			xhttp.onreadystatechange = function() {
				if (this.readyState === 4 && this.status === 200) {
					let content = document.getElementsByClassName("content")[0];
					content.innerHTML = this.responseText;
				}
			};
			xhttp.open("POST", "/faq/search", true)
			xhttp.setRequestHeader('X-CSRF-TOKEN', token);
			xhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send(body);
		}
	}
	

	// navigation items
	var navigationItem = document.getElementsByClassName("navigation__item");

	for (var i = 0; i < navigationItem.length; i++) {
		navigationItem[i].addEventListener("click", function() {
			removeAllNavigationActiveClass(navigationItem)

			this.classList.add("navigation__item-active");
		});
	}

	function removeAllNavigationActiveClass(item) {
		for (var i = 0; i < navigationItem.length; i++) {
			navigationItem[i].classList.remove("navigation__item-active");
		}
	}


	// language choosers
	var headerLanguageChooser = document.querySelector(".header__language_chooser");
	headerLanguageChooser.addEventListener('click', LanguageToogler("header"));

	var footerLanguageChooser = document.querySelector(".footer__language_chooser");
	footerLanguageChooser.addEventListener('click', LanguageToogler("footer"));


	window.onclick = function(event) {
		closeLangChooser(event, "header");
		closeLangChooser(event, "footer");
	}

	function LanguageToogler(prefix) {
		return function() {
			var optionsList = document.querySelector("." + prefix + "__language_chooser__options_list");
			var languageChooser = document.querySelector("." + prefix + "__language_chooser");
			optionsList.classList.toggle("show");
			languageChooser.classList.toggle(prefix + "__language_chooser__expanded");
		}
	}

	function closeLangChooser(event, prefix) {
		if (!event.target.closest("." + prefix + "__language_chooser")) {
			var dropdowns = document.getElementsByClassName(prefix + "__language_chooser__options_list");
			var i;
			for (i = 0; i < dropdowns.length; i++) {
				var openDropdown = dropdowns[i];
				if (openDropdown.classList.contains('show')) {
					openDropdown.classList.remove('show');
				}
			}
		}
	}
	
});
