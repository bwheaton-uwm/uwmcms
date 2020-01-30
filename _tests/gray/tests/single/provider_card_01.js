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
		.assert.containsText(".cta-to-bios-sheida-aalami .field--type-string",
		                     "Sheida P. Aalami M.D.", "verify Provider Name")
		.assert.containsText(".full-wide:nth-child(1) h3", "Medical Specialty",
		                     "Verify Medical Specialty label text")
		.assert.containsText(".col-xxl-3:nth-child(1) .items", "Internal medicine",
		                     "Medical Specialty type is correct")
		.assert.elementPresent(".col-xxl-3:nth-child(1) .uwm-icon--calendar",
		                       "Calendar Icon is present")
		.assert.containsText(".col-xxl-3:nth-child(1) .uwm-accent-color__purple h3", "Appointments",
		                     "Appointments label is present")
		.assert.elementPresent(".col-xxl-3:nth-child(1) .provider-card__phone-number",
		                       "Verify Appointments Phone number button is present.")
		.assert.containsText(".col-xxl-3:nth-child(1) .provider-card__phone-number", "206.520.5000",
		                     "Appointment phone number is correct")
		.assert.elementPresent(".cta-to-bios-sheida-aalami.btn-cta-link",
		                       "See Full Bio button is present")
		.asert.containsText(".cta-to-bios-sheida-aalami.btn-cta-link", "SEE FULL BIO",
		                    "See Full Bio button text is correct");

	// Get the src attribute for the image for the provider on the first provider card,
	// and check to make sure it has the expecrted value
	browser.getAttribute('css selector', 'article.node-res_provider:nth-child(1) img', 'src',
	                     function(source) {
	                     	this.assert.ok(source.value.includes("api-bioimage-sheida-aalami.jpg",
	                     	                                     "verify provider picture"));
	    				});
	},
	// verify the presence/absence of "accepting"
	// Verify the presence/absence or Location
};

