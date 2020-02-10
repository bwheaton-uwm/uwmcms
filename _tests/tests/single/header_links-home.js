module.exports = {
	'disabled': true,

	'step one: verify T1 Header - UW Med imgLink (Home)': function (browser) {
	browser
		.url(browser.launch_url)
		.waitForElementVisible('body', "<body> tage became visible")
		.assert.titleContains('UW Medicine', "Title Contains: 'UW Medicine'")
		.assert.visible(".navbar-brand img", "Homepage image is visible.")
		.click(".navbar-brand img")
		.assert.title("UW Medicine: Seattle's Nationally-Ranked Healthcare System",
		              "Newly load page title is: UW Medicine: Seattle's Nationally-Ranked " +
		              "Healthcare System")
	},
};
