module.exports = {
	"disabled": false,

	"In Development: randomSet-allFieldsAggregate.js": function (browser) {
	// This test case will examine a set of randomly selected providers (provider cards) from
	// the Find a Provider page(s). It will continue to examine random cards until it has found
	// at least one occurence of every mandatory & optional provider card field. A configurable
	// max number of cards to examine will be provided.
		const maxCards = 10;
		let ranPage, ranCard;
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

		pager // pick a random page from the list of resulting pages
			.test();

		searchProviderPage	// pick a random card on the loaded page
			.perform(function() {
				browser.elements("css selector", "article", function(result){
					ranCard = Math.floor(Math.random() * (result.value.length-1));
					return this;
				})
			})
			.assert.equal("X", ranCard);

	browser.end();
	},
};

