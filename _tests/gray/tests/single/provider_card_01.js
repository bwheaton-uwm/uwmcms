module.exports = {
	'disabled': false,

	'navigate to Providers page, validate provider card': function (browser) {
	browser
		.url('https://stevie.cmsdev.uwmedicine.org/')
		.waitForElementVisible('body')
		.assert.title("UW Medicine: Seattle's Nationally-Ranked Healthcare System",
		              "verify Homepage title")
		.waitForElementVisible("#quickLinksNavigation li:nth-child(3) a")
		.click("#quickLinksNavigation li:nth-child(3) a")
		.url("https://stevie.cmsdev.uwmedicine.org/search/providers")
		.assert.title("Find a provider | UW Medicine", "verify Provider Resource page title")
		.waitForElementVisible("article.node-res_provider:nth-child(1)",
		                       "first provider card has loaded")
		// verify the provider name
		.assert.containsText(".cta-to-bios-sheida-aalami .field--type-string",
		                     "Sheida P. Aalami M.D.", "verify Provider Name");
		// verify the specialty
		// verify appointments - calandar appears
		// verify appointments - text appears
		// verify appointments - phone number button
		// verify Full Bio button

	// Get the src attribute for the image for the provider on the first provider card,
	// and check to make sure it has the expecrted value
	browser.getAttribute('css selector', 'article.node-res_provider:nth-child(1) img',
	                     'src', function(source) {
	                     	this.assert.ok(source.value.includes("api-bioimage-sheida-aalami.jpg",
	                     	                                     "verify provider picture"));
	                     });
	},

	// verify the presence/absence of "accepting"

	// Verify the presence/absence or Location



};

