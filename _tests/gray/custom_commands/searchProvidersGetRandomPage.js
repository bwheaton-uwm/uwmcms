module.exports = {
	command: async function() {
		// searchProvidersGetRandomPageArray()
		// When called from a Providers search results page, it checks the "last" page found
		// then returns an aray of randomly generated page urls for search results.
		let ranPage, searchUrl;

		this.waitForElementVisible(".pager__item--last a span:nth-child(2)")
		this.getText(".pager__item--last a span:nth-child(2)", function(result) {
			// Due to the way the cxommand queue is built, you cannot use pageObject "aliass" here.
			// You need to use the underlying css selector.
			ranPage = Math.floor(Math.random() * (result.value-1));
			//searchUrl = "https://www.uwmedicine.org/search/providers?s=&page=" + (ranPage);
			searchUrl = "https://www.uwmedicine.org/search/providers?s=&page=5";
			this.url(searchUrl);
			return this;
		})
	}
};