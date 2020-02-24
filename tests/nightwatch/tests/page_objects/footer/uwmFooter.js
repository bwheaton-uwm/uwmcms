// page_objects.uwmFooter.js
module.exports = {
	url: 'https://www.uwmedicine.org/',
    commands: [
        {
            verifySocialLinks: function (pageUrl, testUrlValidation) {
                // Verify the Social Media link icons.
                // Note: It seems that if you hit any of these social pages enough with automation
                // they start displayingh CAPTCHA pages, but not reliably. Since this is on their
                // end, I'm just verifying the page link appears, not that the page loads when
                // clicked.
                this
                    .assert.visible('@iconFacebookLnk', 'Facebook icon link appears.');
                this
                    .assert.visible('@iconLinkedInLnk', 'LinkedIn icon link appears.');
                this
                    .assert.visible('@iconTwitterLnk', 'Twitter icon link appears.');
                this
                    .assert.visible('@iconInstagramLnk', 'Instagram icon link appears.');
                this
                    .assert.visible('@iconYoutubeLnk', 'YouTube icon link appears.');

                return this;
            },
            verifyT1FooterLinks: function (pageUrl, testUrlValidation) {
                this
                    .assert.visible('@aboutLnk', 'About UW Medicine link appears.')
                    .click('@aboutLnk')
                    .waitForElementVisible('@aboutVal', 'Abount link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@mediaInqLnk', 'Media Inquiries link appears.')
                    .click('@mediaInqLnk')
                    .waitForElementVisible('@mediaInqVal', 'Media Inquiries link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@contactLnk', 'Contact Us link appears.')
                    .click('@contactLnk')
                    .waitForElementVisible('@contactVal', 'Contact Us link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@volunteerLnk', 'Volunteer link appears.')
                    .click('@volunteerLnk')
                    .waitForElementVisible('@volunteerVal', 'Volunteer link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@uweduLnk', 'UW.edu link appears.')
                    .click('@uweduLnk')
                    .waitForElementVisible('@uweduVal', 'UW.edu link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@providerLnk', 'Provider Resource link appears.')
                    .click('@providerLnk')
                    .waitForElementVisible('@providerVal', 'Provider Resource link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    // Fact Book loads a PDF page. At this point. I'm not sure how to validate it since
                    // it doesn't seemt to have an HTML-like DOM.
                    .assert.visible('@factBookLnk', 'Fact Book link appears.');
                this
                    .assert.visible('@careersLnk', 'Careers link appears.')
                    .click('@careersLnk')
                    .waitForElementVisible('@careersVal', 'Careers link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@donateLnk', 'Donate link appears.')
                    .click('@donateLnk')
                    .waitForElementVisible('@donateVal', 'Donate link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@feedbackLnk', 'Feedback link appears.')
                    .click('@feedbackLnk')
                    .waitForElementVisible('@feedbackVal', 'Feedback link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@harborviewLnk', 'Harborview Medical Center link appears.')
                    .click('@harborviewLnk')
                    .waitForElementVisible('@harborviewVal', 'Harborview link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@valleyMedLnk', 'Valley Medical Center link appears.')
                    .click('@valleyMedLnk')
                    .waitForElementVisible('@valleyMedVal', 'Valley Medical Center link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@uwNeighborhoodLnk', 'UW Neighborhood Clinics link appears.')
                    .click('@uwNeighborhoodLnk')
                    .waitForElementVisible('@uwNeighborhoodVal', 'UW Neighborhood Clinics link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@uwSchoolLnk', 'UW School of Medicine link appears.')
                    .click('@uwSchoolLnk')
                    .waitForElementVisible('@uwSchoolVal', 'UW School of Medicine link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@rightAsRainLnk', 'Right as Rain link appears.')
                    .click('@rightAsRainLnk')
                    .waitForElementVisible('@rightAsRainVal', 'Right as Rain link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@uwMedCenterNWLnk', 'UW Med Center NW link appears.')
                    .click('@uwMedCenterNWLnk')
                    .waitForElementVisible('@uwMedCenterNWVal', 'UW Med Center NW link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@uwMedCenterMontlakeLnk', 'UW Med Center Montlake link appears.')
                    .click('@uwMedCenterMontlakeLnk')
                    .waitForElementVisible('@uwMedCenterMontlakeVal', 'UW Med Center Montlake link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@uwPhysiciansLnk', 'UW Physicians link appears.')
                    .click('@uwPhysiciansLnk')
                    .waitForElementVisible('@uwPhysiciansVal', 'UW Physicians link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@airliftLnk', 'Airlift Northwest link appears.')
                    .click('@airliftLnk')
                    .waitForElementVisible('@airliftVal', 'Airlift NW link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@uwMedNewsroomLnk', 'UW Med Newsroom link appears.')
                    .click('@uwMedNewsroomLnk')
                    .waitForElementVisible('@uwMedNewsroomVal', 'UW Med Newsroom link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');

                return this;
            },
            verifyT2FooterLinks: function (pageUrl, testUrlValidation) {
                this
                    .assert.visible('@onlinePrivacyLnk', 'Online Privacy Statement link appears.')
                    .click('@onlinePrivacyLnk')
                    .waitForElementVisible('@onlinePrivacyVal', 'Online Privacy link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@policiesLnk', 'Policies And Notices link appears.')
                    .click('@policiesLnk')
                    .waitForElementVisible('@policiesVal', 'Policies link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@copyrightLnk', 'Copyright link appears.')
                    .click('@copyrightLnk')
                    .waitForElementVisible('@copyrightVal', 'Copyright link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@websiteTermsLnk', 'Website Terms and Conditions link appears.')
                    .click('@websiteTermsLnk')
                    .waitForElementVisible('@websiteTermsVal', 'Website Terms and Conditions link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');

                return this;
            }
        }
    ],
    elements: {
		aboutLnk: '.foot-first li:nth-child(1) a',
        aboutVal: 'body.path-node-20851',
        airliftLnk: '.foot-second li:nth-child(8) a',
        airliftVal: 'body.path-node-21546',
        careersLnk: '.foot-first li:nth-child(6) a',
        careersVal: 'body.path-node-21461',
        contactLnk: '.foot-first li:nth-child(5) a',
        contactVal: 'body.path-node-21466',
        copyrightLnk: 'ul.list-inline li:nth-child(3)',
        copyrightVal: 'body.path-node-22091',
        donateLnk: '.foot-first li:nth-child(8) a',
        donateVal: 'body.page-template-make-a-gift-php',
        factBookLnk: '.foot-first li:nth-child(4) a',
        factBookVal: 'Validate the link should be PDF',
        feedbackLnk: '.foot-first li:nth-child(10) a',
        feedbackVal: 'body.path-webform-website_feedback',
        harborviewLnk: '.foot-second li:nth-child(1) a',
        harborviewVal: 'body.path-node-23246',
        iconFacebookLnk: '.uwmed-icon__facebook',
        iconFacebookVal: 'div.lfloat i.fb_logo',
        iconInstagramLnk: 'div.service-icons a:nth-child(3)',
        iconInstagramVal: 'main.SCxLW',
        iconLinkedInLnk: '.uwmed-icon__linkedin',
        iconLinkedInVal: 'body#pagekey-auth_wall_desktop_company',
        iconTwitterLnk: '.uwmed-icon__twitter',
        iconTwitterVal: 'div#react-root',
        iconYoutubeLnk: '.uwmed-icon__youtube',
        iconYoutubeVal: 'div#channel-header-container img.yt-img-shadow',
        mediaInqLnk: '.foot-first li:nth-child(3) a',
        mediaInqVal: 'header.headroom--top',
        onlinePrivacyLnk: 'ul.list-inline li:nth-child(1)',
        onlinePrivacyVal: 'body.site-online',
        policiesLnk: 'ul.list-inline li:nth-child(2)',
        policiesVal: 'body.path-node-21821',
        providerLnk: '.foot-first li:nth-child(2) a',
        providerVal: '.specialties',
        rightAsRainLnk: '.foot-second li:nth-child(9) a',
        rightAsRainVal: 'div.dialog-off-canvas-main-canvas',
        uweduLnk: '.foot-first li:nth-child(9) a',
        uweduVal: 'body.home.blog.site-home',
        uwMedCenterMontlakeLnk: '.foot-second li:nth-child(4) a',
        uwMedCenterMontlakeVal: 'body.path-node-22556',
        uwMedCenterNWLnk: '.foot-second li:nth-child(2) a',
        uwMedCenterNWVal: 'body.path-node-22956',
        uwMedNewsroomLnk: '.foot-second li:nth-child(10) a',
        uwMedNewsroomVal: 'div#block-views-whats-new-block',
        uwNeighborhoodLnk: '.foot-second li:nth-child(5) a',
        uwNeighborhoodVal: 'body.path-search-locations',
        uwPhysiciansLnk: '.foot-second li:nth-child(6) a',
        uwPhysiciansVal: 'body.path-node-21861',
        uwSchoolLnk: '.foot-second li:nth-child(7) a',
        uwSchoolVal: 'body.path-node-20916',
        valleyMedLnk: '.foot-second li:nth-child(3) a',
        valleyMedVal: 'body.path-node-22496',
        volunteerLnk: '.foot-first li:nth-child(7) a',
        volunteerVal: 'body.path-node-22171',
        websiteTermsLnk: 'ul.list-inline li:nth-child(4)',
        websiteTermsVal: 'body.site-online.page-id-5'
	}
};

