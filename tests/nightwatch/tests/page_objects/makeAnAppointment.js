// page_objects.makeAnAppointment.js
module.exports = {
	url: 'https://www.uwmedicine.org/',
	commands:[
		{
			verifyBreadcrumbLinks: function (pageUrl, testUrlValidation) {
				this
					.assert.visible('@homeLink', '"Home" breadcrumb link appears.')
					.click('@homeLink')
					.waitForElementVisible('@homePage', '"Home" breadcrumb link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@patientResourcesLink', '"Patient Resources" breadcrumb link appears.')
                    .click('@patientResourcesLink')
                    .waitForElementVisible('@patientResourcesPage',
                        '"Patient Resources" breadcrumb link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');

				return this;
            },
            verifyGridcardLinks: function (pageUrl, testUrlValidation) {
                this
                    .assert.visible('@bookLink', 'Book Online link appears.')
                    .click('@bookLink')
                    .waitForElementVisible('@bookPage', 'Book Online link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@bookInEcareLink', 'Book in eCare link appears.')
                    .click('@bookInEcareLink')
                    .waitForElementVisible('@bookInEcarePage', 'Book in eCare link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    // Telephone links attempt to open default app for telephony, we just verify the link
                    // exists, not click-through.
                    .assert.visible('@telLink', 'Phone Appointment link appears.')
                this
                    .assert.visible('@signInLink', 'Sign in to eCare link appears.')
                    .click('@signInLink')
                    .waitForElementVisible('@signInPage', 'Sign in to eCare link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@cancelLink', 'Cancel Appointment link appears.')
                    .click('@cancelLink')
                    .waitForElementVisible('@cancelPage', 'Cancel Appointment link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@cancelByFormLink', 'Cancel by Form link appears.')
                    .click('@cancelByFormLink')
                    .waitForElementVisible('@cancelByFormPage', 'Cancel by Form link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@urgentLink', 'Urgent Care Locations link appears.')
                    .click('@urgentLink')
                    .waitForElementVisible('@urgentPage', 'Urgent Care Locations link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@uwMedLink', 'UW Medicine link appears.')
                    .click('@uwMedLink')
                    .waitForElementVisible('@uwMedPage', 'UW Medicine link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@emergencyLink', 'Emergency Rooms link appears.')
                    .click('@emergencyLink')
                    .waitForElementVisible('@emergencyPage', 'Emergency Rooms link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@radiologyLink', 'Radiology appointments link appears.')
                    .click('@radiologyLink')
                    .waitForElementVisible('@radiologyPage',
                        'Radiology appointments link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@valleyLink', 'Valley Med Center appointments link appears.')
                    .click('@valleyLink')
                    .waitForElementVisible('@valleyPage', 'Valley Med Center link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@sccaLink', 'Seattle Cancer Care Alliance link appears.')
                    .click('@sccaLink')
                    .waitForElementVisible('@sccaPage',
                        'Seattle Cancer Care Alliance link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');

                return this;
            },
            verifySectionCollapse: function (pageUrl, testUrlValidation) {
                this
                    .assert.visible('@viewMoreLink', 'View More link appears.')
                    .moveToElement('@viewMoreLink', 0, 0)
                    .assert.attributeContains('@viewMoreLink', 'aria-expanded', 'false',
                        "View More is collapsed.")
                    .click('@viewMoreLink');
                this.pause(3000);
                this.assert.visible('@viewLessLink', "View Less link appears.")
                    .moveToElement('@viewLessLink', 1000, 1000)
                    .click('@viewLessLink');
                this.pause(3000);
                this.assert.attributeContains('@viewMoreLink', 'aria-expanded', 'false',
                    "Clicking View More collapses the text.");

                return this;
            },
            verifyRescheduleLinks: function (pageUrl, testUrlValidation) {
                // Telephone links attempt to open default app for telephony, we just verify the link
                // exists, not click-through.
                this
                    .assert.visible('@callLink', ' link appears.')

                return this;
            },
            verifyInterpreterLinks: function (pageUrl, testUrlValidation) {
                // Telephone links attempt to open default app for telephony, we just verify the link
                // exists, not click-through.
                this
                    .assert.visible('@amharicLink', 'Amharic link appears.')
                    .assert.visible('@arabicLink', 'Arabic link appears.')
                    .assert.visible('@cambodianLink', 'Cambodian link appears.')
                    .assert.visible('@cantoneseLink', 'Cantonese link appears.')
                    .assert.visible('@mandarinLink', 'Mandarin link appears.')
                    .assert.visible('@farsiLink', 'Farsi link appears.')
                    .assert.visible('@koreanLink', 'Korean link appears.')
                    .assert.visible('@russianLink', 'Russian link appears.')
                    .assert.visible('@somaliLink', 'Somali link appears.')
                    .assert.visible('@spanishLink', 'Spanish link appears.')
                    .assert.visible('@tigrinyaLink', 'Tigrinya link appears.')
                    .assert.visible('@vietnameseLink', 'Vietnamese link appears.')
                    .assert.visible('@TTYLink', 'TTY link appears.')

                return this;
            }
		}
	],
	elements: {
        amharicLink: '.col-lg-4:nth-child(1) .field__item div:nth-child(1) a',
        arabicLink: '.col-lg-4:nth-child(1) .field__item div:nth-child(2) a',
        bookInEcareLink: '.cta-to-prod01-scheduling',
        bookInEcarePage: 'body.md_login_login',
        bookLink: '.cta-to-prod01-openscheduling .btn__text',
        bookPage: 'body.md_openscheduling_index',
        callLink: '.mb-0 a',
        cambodianLink: '.col-lg-4:nth-child(1) .field__item div:nth-child(3) a',
        cancelByFormLink: '.cta-to-pages-responsepageaspx',
        cancelByFormPage: 'div#form-container',
        cancelLink: '#main-container :nth-child(4) .btn__text',
        cancelPage: 'body.md_login_login',
        cantoneseLink: '.col-lg-4:nth-child(1) .field__item div:nth-child(4) a',
        emergencyLink: '#ppg-53061 .btn__text',
        emergencyPage: 'body.path-node-20736',
        farsiLink: '.col-lg-4:nth-child(2) .field__item div:nth-child(1) a',
        homeLink: '.breadcrumb__item:nth-child(1) .breadcrumb__text--link',
        homePage: 'body.is-page-node-type-uwm-homepage',
        koreanLink: '.col-lg-4:nth-child(2) .field__item div:nth-child(2) a',
        mandarinLink: '.col-lg-4:nth-child(1) .field__item div:nth-child(5) a',
        patientResourcesLink: '.breadcrumb__item:nth-child(2) .breadcrumb__text--link',
        patientResourcesPage: 'body.path-node-20866',
        radiologyLink: '.mb-5 a:nth-child(1)',
        radiologyPage: 'body.path-node-20661',
        russianLink: '.col-lg-4:nth-child(2) .field__item div:nth-child(3) a',
        sccaLink: '.mb-5 a:nth-child(3)',
        sccaPage: 'body.contact-patient-appointment',
        signInLink: '.cta-to-prod01-visits-visitslist:nth-child(2) .btn__text',
        signInPage: 'body.md_login_login',
        somaliLink: '.col-lg-4:nth-child(2) .field__item div:nth-child(4) a',
        spanishLink: '.col-lg-4:nth-child(3) .field__item div:nth-child(1) a',
        telLink: '.cta-to-12065205000 .btn__text',
        tigrinyaLink: '.col-lg-4:nth-child(3) .field__item div:nth-child(2) a',
        TTYLink: '.col-lg-4:nth-child(3) .field__item div:nth-child(4) a',
        urgentLink: '#ppg-53051 .btn__text',
        urgentPage: 'body.path-node-21946',
        uwMedLink: '#ppg-53056 .btn__text',
        uwMedPage: 'body.path-node-49201',
        valleyLink: '.mb-5 a:nth-child(2)',
        valleyPage: 'body.bp-full-width',
        vietnameseLink: '.col-lg-4:nth-child(3) .field__item div:nth-child(3) a',
        viewLessLink: '.view-more-link',
        viewMoreLink: '.view-more-link'
	}
};





