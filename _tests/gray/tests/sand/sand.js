module.exports = {
	"disabled": false,

	"In Development: randomSet-allFieldsAggregate.js": function (browser) {
	// This test case will examine a set of randomly selected providers (provider cards) from
	// the Find a Provider page(s). It will continue to examine random cards until it has found
	// at least one occurence of every mandatory & optional provider card field. A configurable
	// max number of cards to examine will be provided.
		let ranPage, ranCard;
		var searchUrl;

		var searchProviderPage = browser.page.searchProviders();
		var pager = searchProviderPage.section.pager;

		searchProviderPage
		.loadPage()
		.perform(function() {
			const maxCards = 1;

			for(let i=0; i<maxCards; i++){
						let foundFields = {
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

				// Get a random page
				// Currently set in ~/custom_commands/searchProvidersGetRandomPage.js to always
				// load the 5th page of results, for debugging
				browser
				.searchProvidersGetRandomPage()
				.elements("css selector", "article", function(result) {
					// then a random card on that page
					ranCard = Math.floor(Math.random() * (result.value.length-1));
					// Setting this to the 2nd card on the page for debugging.
					//let cardCss = "article:nth-child(" + ranCard + ")";
					let cardCss = "article:nth-child(" + 2 + ")";
					// then walk the card, setting
					browser.element("css selector", cardCss + " img", function(result) {
						if(result.status != -1) {
							browser.assert.visible(cardCss + " img",
							    "Default Provider image verified");
							foundFields.provImgDefault = true;
						} else {
							browser.assert.visible(cardCss + " img-fluid",
							    "Non-default Provider image verified")
							foundFields.provImgPicture = true;
						}
					})
					browser.assert.visible(cardCss + " .provider-card__title", "Card Title verified");
					foundFields.provName = true;
					return this;
				})
				.assert.equal(foundFields.provName, true, "foundFields set correctly.")
			}
		});

	}
};
