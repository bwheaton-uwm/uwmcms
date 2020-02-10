module.exports = {
	'disabled': true,

	'navigate to Providers page, validate provider card': function (browser) {
	const url = "https://google.com";
	browser
		.url(url)
		.waitForElementVisible('body')
		.assert.titleContains("Google");
	},
};

