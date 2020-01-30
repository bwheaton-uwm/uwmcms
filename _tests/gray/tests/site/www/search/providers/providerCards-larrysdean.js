module.exports = {
	"disabled": false,

	"Verifying a provider card for 'Larry S. Dean'.": function (browser) {
	const name = "Larry S. Dean";

	var searchProviderPage = browser.page.searchProviders();
	searchProviderPage.navigate()
		.waitForElementVisible("@cookiesAlertButton")
		.click("@cookiesAlertButton")
		.navigate()
		.waitForElementVisible("body")
		.resizeWindow(2048,1536)
		.assert.title("Find a provider | UW Medicine",
		              "verify /search/providers page title")
		.assert.visible("@providerSearchBar")
		.setValue("@providerSearchBar", name)
		.assert.visible("@searchButton")
		.click("@searchButton")
		.waitForElementVisible("@resetButton")
		.assert.elementCount("article", 1, "Correct number of cards found");

	var card = searchProviderPage.section.providerCard;
	card
		.assert.visible("@cardTitle", "Card Title is present")
		.assert.visible("@accepting", "Accepting New Patients is present")
		.assert.visible("@specialtiesLabel", "Medical Specialties label is present")
		.assert.visible("@specialtiesItems", "Medical Specialties are listed is present")
		.assert.visible("@appointmentsLabel", "Appointments label text is present")
		.assert.visible("@calandarIcon", "Appointments calendar icon is present")
		.assert.visible("@locationsLabel", "Locations label text is present")
		.assert.visible("@locationsPinIcon", "Locations Pin icon is present")
		.assert.elementCount("@locationsItems", 2, "Correct number of Location links found")
		.assert.visible("@locationsItemOne", "First location link is present")
		.assert.visible("@locationItemsTwo", "Second location link is present")
		.assert.visible("@viewContantDetails", "View Contact Details button is present")
		.assert.visible("@seeMoreButton", "See More button is present")
	browser.end();
	},
};



