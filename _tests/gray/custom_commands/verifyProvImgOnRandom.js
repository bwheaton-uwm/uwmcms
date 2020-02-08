module.exports = {
	command: async function() {
		// verifyProvImageOnRandom()
        // When called from a Providers search results page, it verifies that a randomly
        // selected provider card has a valid provider Image or Default image
        self = this;
        this.elements("css selector", "article", function (result) {
            let ranCard = Math.floor(Math.random() * (result.value.length - 1));
            ranCard = 10;
            let imageCss = "article:nth-child(" + ranCard + ") .img-fluid";
            let defaultCss = "article:nth-child(" + ranCard + ") img";

            console.log(imageCss);
            this.waitForElementVisible("article", "Provider card visible.");
            self.element("css selector", imageCss, function (imgResult) {
                if (imgResult.status != 0) {
                    // we didn't find it.
                    self.waitForElementVisible(defaultCss, "Provider card Default Image Icon found");
                } else {
                    self.waitForElementVisible(imageCss, "Provider card Image found.");
                }
            });
        });
        return this;
    }
};
