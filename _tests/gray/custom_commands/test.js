module.exports = {
	command: async function() {
		var ranPage, searchUrl;

		this.waitForElementVisible("@last")
		this.getText(".pager__item--last a span:nth-child(2)", function(result) {
			// I am not sure why, but trying to use the pageObject alias ("@last") here
			// breaks, you must use the straight css selector.
			ranPage = Math.floor(Math.random() * (result.value-1));
			searchUrl = "https://www.uwmedicine.org/search/providers?s=&page=" + (ranPage);
			this.url(searchUrl);
			return this;
		})
	}
};