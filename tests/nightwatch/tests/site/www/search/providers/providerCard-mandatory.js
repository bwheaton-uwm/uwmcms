module.exports = {
	"disabled": false,
	"@tags": [
        //"working",
		"provider",
		"search",
		"providercards"
	],

	"www.search.providers.providerCard-mandatory.js": function (browser) {
    // This test verifies all the mandatory fields on a randomly picked provider card.
    //		Name, specialties label, specialty text, calendar icon, appointments label,
    //      and See Full Bio button
	var searchProviderPage = browser.page.searchProviders();
	let maxCardsToTest = 100;

	searchProviderPage
		.loadPage();

	for (let i = 0; i<maxCardsToTest; i++) {
		searchProviderPage
			.searchProvidersGetRandomPage()
			.verifyProviderMandatoryFields();
	}
	searchProviderPage
		.end();
	},
};