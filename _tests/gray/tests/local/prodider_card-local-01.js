module.exports = {
	'disabled': true,

	'navigate to Providers page, validate provider card': function (browser) {
	browser
		.url('https://stevie.cmsdev.uwmedicine.org/search/providers/')
		.waitForElementVisible('body')
		.assert.title('Find a provider | UW Medicine"', "This is a message")

		.assert.visible("article.node-res_provider:nth-child(1)")


		.click("article.node-res_provider:nth-child(1)")
		.assert.title("UW Medicine: Seattle's Nationally-Ranked Healthcare System")
	},
};

