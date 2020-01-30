module.exports = {
	'disabled': true,

	'quick and dirty test to play with some syntax or other': function (browser) {
	var google = browser.page.google();

	google.navigate()
		.waitForElementVisible('body')
		.assert.title('Google')
		.assert.visible('@searchBar')
		.setValue('@searchBar', 'nightwatch')
		.click('@submit')
		.waitForElementVisible('body');

	browser.end();
	},
};

