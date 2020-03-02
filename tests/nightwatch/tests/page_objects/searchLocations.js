// page_objects.searchLocations.js
module.exports = {
    url: 'https://www.uwmedicine.org/search/locations',
	commands:[
		{
            verifyCardLinks: function (urlValidator) {
                const cardLinks = [
                    '@harborviewNameLink',
                    '@harborviewAddressLink',
                    '@harborviewSeeDetailsLink',
                    '@montlakeNameLink',
                    '@montlakeAddressLink',
                    '@montlakeSeeDetailsLink',
                    '@nwNameLink',
                    '@nwAddressLink',
                    '@nwSeeDetailsLink',
                    '@valleyNameLink',
                    '@valleyAddressLink',
                    '@valleySeeDetailsLink',
                ];
                const cardPages = [
                    this.elements.harborviewNamePage,
                    this.elements.harborviewAddressPage,
                    this.elements.harborviewSeeDetailsPage,
                    this.elements.montlakeNamePage,
                    this.elements.montlakeAddressPage,
                    this.elements.montlakeSeeDetailsPage,
                    this.elements.nwNamePage,
                    this.elements.nwAddressPage,
                    this.elements.nwviewSeeDetailsPage,
                    this.elements.valleyNamePage,
                    this.elements.valleyAddressPage,
                    this.elements.valleyviewSeeDetailsPage
                ];

                for (let i=0; i<cardLinks.length; i++) {
                    this.verifyLinkClick(cardLinks[i], cardPages[i], this.url,
                        urlValidator, 'Search Location Result Cards - ' + cardLinks[i] + ',');
                };

				return this;
            },

            verifyLocationSearchByCityZip: function () {
                // Searching a zip code (98107) should:
                //--return a result set that contain more than 1 item. (350)
                //--pagination must be greater than 1.
                //--result set must contain a clinic called “Advanced Manual Therapy & Sports
                //  Rehabilitation at Ballard”
                const zip = '98107';
                const zipTextboxElement = '@formEnterZipOrCityTextbox';
                const submitButton = '@formSubmit';
                const resultsPageLoaded = 'body.search-with-results';
                const resultPageTitleElement = 'div#block-uwmbase-page-title h1.page-title';
                const firstResultTitle = 'article.clinic-card:nth-child(1) .field--name-title';
                const expectedText = 'Advanced Manual Therapy & Sports Rehabilitation at Ballard';

                this
                    .waitForElementVisible(zipTextboxElement)
                    .clearValue(zipTextboxElement)
                    .setValue(zipTextboxElement, zip)
                    .click(submitButton)
                this.pause(5000);

                this
                    .waitForElementVisible(resultsPageLoaded)
                    .assert.containsText(resultPageTitleElement, 'locations found', "Multiple results found.")
                    .waitForElementVisible('@pager', "There are enough results to require pagination.")
                    .waitForElementVisible('@formClinicResult')
                    .assert.containsText(firstResultTitle, expectedText, 'Results include the expected clinic.');

                return this;
            },

            verifyLocationSearchByMedSpecialtiy: function () {
                //
            },

            verifyLocationSearchByName: function () {
                //
            },

            verifyTelLinks: function (section) {
                const telLinks = [
                    '@nwTelLink',
                    '@harborviewTelLink',
                    '@montlakeTelLink',
                    '@valleyTelLink',
                ];

                for (let i=0; i<telLinks.length; i++) {
                    this.verifyLink(telLinks[i], 'Link ' + telLinks[i] + '.');
                }

                return this;
            }
		}
    ],
    elements: {
        harborviewNameLink: '.cta-to-locations-harborview-medical-center span.field--name-title',
        harborviewNamePage: 'body.path-node-23246',
        harborviewAddressLink: '.cta-parent-res-clinic:nth-child(1) .clinic-card__street-address',
        harborviewAddressPage: 'body.path-node-23246',
        harborviewTelLink: '.cta-to-206-744-3000',
        harborviewSeeDetailsLink: '.cta-to-locations-harborview-medical-center.clinic-card',
        harborviewSeeDetailsPage: 'body.path-node-23246',
        montlakeNameLink: '.cta-to-locations-uw-medical-center span.field--name-title',
        montlakeNamePage: 'body.path-node-22556',
        montlakeAddressLink: '.cta-parent-res-clinic:nth-child(2) .clinic-card__street-address',
        montlakeAddressPage: 'body.path-node-22556',
        montlakeTelLink: '.cta-to-206-598-3300',
        montlakeSeeDetailsLink: '.cta-to-locations-uw-medical-center.clinic-card',
        montlakeSeeDetailsPage: 'body.path-node-22556',
        nwNameLink: '.cta-to-locations-northwest-hospital span.field--name-title',
        nwNamePage: 'body.path-node-22956',
        nwAddressLink: '.cta-parent-res-clinic:nth-child(3) .clinic-card__street-address',
        nwAddressPage: 'body.path-node-22956',
        nwTelLink: '.cta-to-877-694-4677',
        nwSeeDetailsLink: '.cta-to-locations-northwest-hospital.clinic-card',
        nwviewSeeDetailsPage: 'body.path-node-22956',
        pager: '.pager',
        valleyNameLink: '.cta-to-locations-valley-medical-center span.field--name-title',
        valleyNamePage: 'body.path-node-22496',
        valleyAddressLink: '.cta-parent-res-clinic:nth-child(4) .clinic-card__street-address',
        valleyAddressPage: 'body.path-node-22496',
        valleyTelLink: '.cta-to-425-690-1000',
        valleySeeDetailsLink: '.cta-to-locations-valley-medical-center.clinic-card',
        valleyviewSeeDetailsPage: 'body.path-node-22496',
        formClinicResult: '.cta-parent-res-clinic',
        formEnterNameTextbox: 'input.dm-form-item-collection',
        formEnterZipOrCityTextbox: 'input#edit-l',
        formUseMyLocationDDLink: 'a#umlDropdownLink',
        formMedSpecTopDDLink: 'div.filter-option-inner-inner',
        formMedSpecDDTextBox: 'div.bs-searchbox input.form-control',
        formMedSpecDDNoResults: 'ul.dropdown-menu li:nth-child(1).no-results',
        formMedSpecTextBoxFirstResult: 'ul.dropdown-menu li:nth-child(1)',
        formMedSpecListItem8CardiologyText: 'ul.dropdown-menu li:nth-child(8) a span:nth-child(2)',
        formMedSpecListItem8CardiologyCount: 'ul.dropdown-menu li:nth-child(8) a span:nth-child(2) small',
        formReset: 'a.btn-cta-link.reset',
        formSubmit: 'a.btn-cta-link.submit'
    }
};
