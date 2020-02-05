module.exports = {
	"disabled": false,

	"In Development: randomSet-allFieldsAggregate.js": function (browser) {
	// This test case will examine a set of randomly selected providers (provider cards) from
	// the Find a Provider page(s). It will continue to examine random cards until it has found
	// at least one occurence of every mandatory & optional provider card field. A configurable
	// max number of cards to examine will be provided.
		const maxCards = 10;
		let numPages, ranPage, numCards, ranCard;
		var searchUrl;
		var foundFields = {
			provImgDefault: false,
			provImgPicture: false,
			provName: false,
			acceptingNew: false,
			medSpecLabel: false,
			medSpec: false,
			appCalIcon: false,
			appLabel: false,
			appPhoneBtn: false,
			appViewContactBtn: false,
			appBookOnlineBtn: false,
			locPinIcon: false,
			locLabel: false,
			locaMultiLinks: false,
			locSingleLink: false,
			seeFullBioBtn: false
		};
		var baseUrl = "https://www.uwmedicine.org/search/providers?s=&page=";
		var searchProviderPage = browser.page.searchProviders();
		var pager = searchProviderPage.section.pager;

		searchProviderPage
			.loadPage();
		pager
			.waitForElementVisible("@last")
			.perform( function() {
				browser.getText(".pager__item--last a span:nth-child(2)", function(result) {
					// I am not sure why, but trying to use the pageObject alias ("@last") here
					// breaks, you must use the straight css selector.
					numPages = result.value;
					searchUrl = "https://www.uwmedicine.org/search/providers?s=&page=" + (numPages-1);
					browser.url(searchUrl);
					return this;
				})
			});



			// get numPages
			// set ranPage = random(numPages)
			// get numCards
			// set ranCard = random(cardsDisplayed)
			// verify card

	browser.end();
	},
};

