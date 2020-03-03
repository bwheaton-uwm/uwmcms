// page_objects.virtualClinic.js
module.exports = {
    url: 'https://www.uwmedicine.org/services/virtual-clinic',
	commands:[
		{
            verifyCTALinks: function () {
                // Verifies the three blue CTA links"
                // Start Visit Button: appears, clicking it's link loads correct page, returns to this page
                // Get the Virtual App link scrolls the page to the correct anchor, near the download buttons
                // The telephone number link appears (cannot really validate it opens a phone app)
                this
                    .verifyLinkClick('@startVisitLink', this.elements.startVisitPage,
                        this.url, this.elements.myValidator, 'Start Visit CTA link')
                    .waitForElementVisible('@getVirtualAppCtaLink')
                    .click('@getVirtualAppCtaLink')
                    .pause(3000)
                    .assert.urlContains('#appDL---', 'Clicking link moved to anchor.')
                    .verifyLink('@telLink', "Telephone CTA link");

                return this;
            },
            verifyDownloadLinks: function () {
                // Download link for Apple Store exisits, clicking it loads the correct page
                // url should also contain 'apps.apple.com/us/app/uw-medicine-virtual-clinic'

                // Download link for the Google Play store exists, clicking it loads the correct page

                return this;
            },
            verifyLooseLinks: function () {
                //

                return this;
            },
		}
    ],
    elements: {
        myValidator: 'body.path-node-49201',
        startVisitLink: '.cta-to-uwmedicinevirtualclinicorg.btn-cta-link .btn__text',
        startVisitPage: 'body#landingBody',
        getVirtualAppCtaLink: '.cta-to-services-virtual-clinic .btn__text',
        getVirtualAppCtaPage: 'body',
        telLink: '.cta-to-1-855-520-5250 .btn__text',
        downloadContainer: '.uwm-featured-story__content',
        downloadAppleLink: 'a.cta-to-itunesapplecom',
        downloadApplePage: 'body.ember-application',
        downloadGoogleLink: 'a.cta-to-playgooglecom',
        downloadGooglePage: 'body#yDmH0d'
    }
};
