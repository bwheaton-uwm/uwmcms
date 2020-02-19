// page_objects/footer/uwmHeader.js
module.exports = {
    commands: [
        {
            headerSearchFor: function (searchText) {
                // Verifies that the search bar is being shown, clears it, searches for the text
                // provided, then waits for the results. This is a helper function for when other
                // tests would like to search for a given string.
                return this
                    .assert.visible('@t1headerSearchBar', 'Search bar is present.')
                    .clearValue('@t1headerSearchBar')
                    .setValue('@t1headerSearchBar', searchText)
                    .assert.visible('@t1headerSubmitBtn', 'Search submit button is present.')
                    .click('css selector', 'form.header-search-form svg.fa-search:nth-child(2)');
            },
            loadPageAndAcceptCookies: function (pageUrl, testUrlValidation) {
                // loads a page then attempts to handle the Cookies Alert popup.
                // This is a helper function for loading most pages. It requires two arguments:
                // pageUrl -- URL of the page to load
                // testUrlValidation -- a css selector string use to check that the correct page
                //                      has loaded. It should be unique to that page.
                return this
                    .navigate(pageUrl)
                    .waitForElementVisible('@t1cookiesAlertButton', 'Cookies alert button found.')
                    .click('@t1cookiesAlertButton')
                    .waitForElementVisible(testUrlValidation, 'Requested page loaded.');
            },
            verifyT1HeaderLinks: function (pageUrl, testUrlValidation) {
                // Abstracts out the tests for the T1 Header links.
                // This is a helper function for testing the T1 Header links for other pages under test.
                // It requires two arguments:
                // pageUrl -- URL of the page to load
                // testUrlValidation -- a css selector string use to check that the correct page
                //                      has loaded. It should be unique to that page.
                // See site.www.general.uwmHeader.js for an example of how to use this within other
                // test pages
                var UWLogoImageSrc = 'https://www.uwmedicine.org/themes/custom/uwmbase/dist/images/uw-medicine-logo.svg';

                this
                    .assert.visible('@t1headerImage', 'UW Medicine Image link appears.')
                    .getAttribute('@t1headerImage', 'src', function (result) {
                        this.assert.equal(result.value, UWLogoImageSrc, 'UW Medicine Image has correct source.');
                    });
                this
                    .assert.visible('@t1eCareLink', 'eCare Patient Portal link appears.')
                    .click('@t1eCareLink')
                    .waitForElementVisible('@t1eCareValidation', 'eCare Patient Portal link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to requested page.');
                this
                    .assert.visible('@t1providersLink', 'For Providers link appears.')
                    .click('@t1providersLink')
                    .waitForElementVisible('@t1providersValidation', 'For Providers link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to requested page.');
                this
                    .assert.visible('@t1researchLink', 'Research link appears.')
                    .click('@t1researchLink')
                    .waitForElementVisible('@t1researchValidation', 'Research link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to requested page.');
                this
                    .assert.visible('@t1schoolLink', 'School of Medicine link appears.')
                    .click('@t1schoolLink')
                    .waitForElementVisible('@t1schoolValidation', 'School of Medicine link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to requested page.');
                this
                    .assert.visible('@t1contactLink', 'Contact Us link appears.')
                    .click('@t1contactLink')
                    .waitForElementVisible('@t1contactValidation', 'Contact Us link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '');

                    return this;
            },
            verifyT2HeaderLinks: function (pageUrl, testUrlValidation) {
                // Abstracts out the tests for the T2 Dropdowns and Header links.
                // This is a helper function for testing the T2 Header links for other pages under test.
                // It requires two arguments:
                // pageUrl -- URL of the page to load
                // testUrlValidation -- a css selector string use to check that the correct page
                //                      has loaded. It should be unique to that page.
                // See site.www.general.uwmHeader.js for an example of how to use this within other
                // test pages
                this
                    .assert.visible('@t2findCareDD', 'Find care drop-down appears.')
                    .click('@t2findCareDD')
                    .waitForElementVisible('@t2urgentCareLnk', 'Urgent Care link appears.')
                    .click('@t2urgentCareLnk')
                    .waitForElementVisible('@t2urgentCareVal', 'Urgent Care link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible('@t2findCareDD', 'Returned to ' + pageUrl + '.');
                this
                    .click('@t2findCareDD')
                    .waitForElementVisible('@t2primaryCareLnk', 'Primary Care link appears.')
                    .click('@t2primaryCareLnk')
                    .waitForElementVisible('@t2primaryCareVal', 'Primary Care link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible('@t2findCareDD', 'Returned to ' + pageUrl + '.');
                this
                    .click('@t2findCareDD')
                    .waitForElementVisible('@t2virtualClinicLnk', 'Virtual Clinic link appears.')
                    .click('@t2virtualClinicLnk')
                    .waitForElementVisible('@t2virtualClinicVal', 'Virtual Clinic link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible('@t2findCareDD', 'Returned to ' + pageUrl + '.');
                this
                    .click('@t2findCareDD')
                    .waitForElementVisible('@t2emergencyLnk', 'Emergency Rooms link appears.')
                    .click('@t2emergencyLnk')
                    .waitForElementVisible('@t2emergencyVal', 'Emergency Rooms link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible('@t2findCareDD', 'Returned to ' + pageUrl + '.');
                this
                    .click('@t2findCareDD')
                    .waitForElementVisible('@t2medSpecialtiesLnk', 'Medical Specialties link appears.')
                    .click('@t2medSpecialtiesLnk')
                    .waitForElementVisible('@t2medSpecialtiesVal', 'Medical Specialties link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible('@t2findCareDD', 'Returned to ' + pageUrl + '.');
                this
                    .click('@t2findCareDD')
                    .waitForElementVisible('@t2findProvider1Lnk', 'Find a Provider (under Find Care dropdown) link appears.')
                    .click('@t2findProvider1Lnk')
                    .waitForElementVisible('@t2findProviderVal', 'Find a Provider (under Find Care dropdown) link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible('@t2findCareDD', 'Returned to ' + pageUrl + '.');
                this
                    .click('@t2findCareDD')
                    .waitForElementVisible('@t2findLocation1Lnk', 'Find Location (under Find Care dropdown) link appears.')
                    .click('@t2findLocation1Lnk')
                    .waitForElementVisible('@t2findLocationVal', 'Find Location (under Find Care dropdown) link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible('@t2findCareDD', 'Returned to ' + pageUrl + '.');
                this
                    .waitForElementVisible('@t2makeAppt1Lnk', 'Make an appointment link appears.')
                    .click('@t2makeAppt1Lnk')
                    .waitForElementVisible('@t2makeApptVal', 'Make an appointment link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@t2findHealthDD', 'Find Health... drop-down appears.')
                    .click('@t2findHealthDD')
                    .waitForElementVisible('@t2conditionsLnk', 'Conditions and symptoms link appears.')
                    .click('@t2conditionsLnk')
                    .waitForElementVisible('@t2conditionsVal', 'Conditions and symptoms link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible('@t2findHealthDD', 'Returned to ' + pageUrl + '.');
                this
                    .click('@t2findHealthDD')
                    .waitForElementVisible('@t2preventionLnk', 'Prevention and wellness link appears.')
                    .click('@t2preventionLnk')
                    .waitForElementVisible('@t2preventionVal', 'Prevention and wellness link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible('@t2findCareDD', 'Returned to ' + pageUrl + '.');
                this
                    .click('@t2findHealthDD')
                    .waitForElementVisible('@t2patientResourcesLnk', 'Patient resources link appears.')
                    .click('@t2patientResourcesLnk')
                    .waitForElementVisible('@t2patientResourcesVal', 'Patient resources link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible('@t2findCareDD', 'Returned to ' + pageUrl + '.');
                this
                    .click('@t2findHealthDD')
                    .waitForElementVisible('@t2patientEducationLnk', 'Patient education link appears.')
                    .click('@t2patientEducationLnk')
                    .waitForElementVisible('@t2patientEducationVal', 'Patient education link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .assert.visible('@t2iWantToDD', 'I want to... drop-down appears.')
                    .click('@t2iWantToDD')
                    .waitForElementVisible('@t2makeAppt2Lnk', 'Make an appointment link appears.')
                    .click('@t2makeAppt2Lnk')
                    .waitForElementVisible('@t2makeApptVal', 'Make and appointment link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible('@t2iWantToDD', 'Returned to ' + pageUrl + '.');

                this
                    .click('@t2iWantToDD')
                    .waitForElementVisible('@t2signUpLnk', 'Sign up or Sign in... link appears.')
                    .click('@t2signUpLnk')
                    .waitForElementVisible('@t2signUpVal', 'Sign up or Sign in... link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .click('@t2iWantToDD')
                    .waitForElementVisible('@t2payBillLnk', 'Pay my bill link appears.')
                    .click('@t2payBillLnk')
                    .waitForElementVisible('@t2payBillVal', 'Pay my bill link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible('@t2iWantToDD', 'Returned to ' + pageUrl + '.');
                this
                    .click('@t2iWantToDD')
                    .waitForElementVisible('@t2accessLnk', 'Access medical... link appears.')
                    .click('@t2accessLnk')
                    .waitForElementVisible('@t2accessVal', 'Access medical... link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible('@t2iWantToDD', 'Returned to ' + pageUrl + '.');
                this
                    .click('@t2iWantToDD')
                    .waitForElementVisible('@t2findInterpreterLnk', 'Find an interpreter link appears.')
                    .click('@t2findInterpreterLnk')
                    .waitForElementVisible('@t2findInterpreterVal', 'Find an interpreter link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible('@t2iWantToDD', 'Returned to ' + pageUrl + '.');
                this
                    .click('@t2iWantToDD')
                    .waitForElementVisible('@t2findLocation2Lnk', 'Find a location link appears.')
                    .click('@t2findLocation2Lnk')
                    .waitForElementVisible('@t2findLocationVal', 'Find a location link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible('@t2iWantToDD', 'Returned to ' + pageUrl + '.');
                this
                    .click('@t2iWantToDD')
                    .waitForElementVisible('@t2iWantToDD', 'Find a pharmacy link appears.')
                    .click('@t2findPharmaLnk')
                    .waitForElementVisible('@t2findPharmaVal', 'Find a pharmacy link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible('@t2iWantToDD', 'Returned to ' + pageUrl + '.');
                this
                    .click('@t2iWantToDD')
                    .waitForElementVisible('@t2viewPatientLnk', 'View all patient... link appears.')
                    .click('@t2viewPatientLnk')
                    .waitForElementVisible('@t2viewPatientVal', 'View all patient... link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible('@t2iWantToDD', 'Returned to ' + pageUrl + '.');
                this
                    .click('@t2iWantToDD')
                    .waitForElementVisible('@t2referPatientLnk', 'Refer a patient link appears.')
                    .click('@t2referPatientLnk')
                    .waitForElementVisible('@t2referPatientVal', 'Refer a patient link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
                this
                    .waitForElementVisible('@t2makeGiftLnk', 'Make a Gift link appears.')
                    .click('@t2makeGiftLnk')
                    .waitForElementVisible('@t2makeGiftVal', 'Make a Gift link loads correct page.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');

                return this;
            },
            verifyHeaderSearch: function (searchText='location', pageUrl, testUrlValidation) {
                // Abstracts out testing the search bar functionality for the T1 Header.
                // Verifies the search bar is present, clears it, adds supplied test (or 'location' if not
                // provided), clicks the search button, and verifies the search resuilt page is loaded and
                // that there are results found, then returns to the base page under test.
                // See site.www.general.uwmHeader.js for an example of how to use this within other
                // test pages
                this
                    .assert.visible('@t1headerSearchBar', 'Search bar is present.')
                    .clearValue('@t1headerSearchBar')
                    .setValue('@t1headerSearchBar', searchText)
                    .assert.visible('@t1headerSubmitBtn', 'Search submit button is present.')
                    .click('css selector', 'form.header-search-form svg.fa-search:nth-child(2)')
                    .waitForElementVisible('@resetButton', 'Results page loaded.')
                    .waitForElementVisible('@results', 'Found search results.')
                    .navigate(pageUrl)
                    .waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');

                return this;
            }
        }
    ],
    elements: {
        resetButton: {
            selector: '//a[@class="btn btn-cta btn-cta-link reset"]',
            locateStrategy: 'xpath'
        },
        results: 'article.search-result-card',
        t1contactLink: '#quickLinksNavigation li:nth-child(9) a',
        t1contactValidation: 'body.path-node-21466',
        t1cookiesAlertButton: '.eu-cookie-compliance-default-button',
        t1eCareLink: '#quickLinksNavigation li:nth-child(1) a',
        t1eCareValidation: 'body.path-node-20841',
        t1headerImage: '.uwm-header a.navbar-brand img',
        t1headerSearchBar: 'input[type=text]:nth-child(1)',
        t1headerSubmitBtn: 'form.header-search-form svg.fa-search:nth-child(2)',
        t1homeValidation: 'body.is-path-frontpage',
        t1providersLink: '#quickLinksNavigation li:nth-child(3) a',
        t1providersValidation: '.homepage-header-inset li:nth-child(1)',
        t1researchLink: '#quickLinksNavigation li:nth-child(5) a',
        t1researchValidation: 'body.path-node-21421',
        t1schoolLink: '#quickLinksNavigation li:nth-child(7) a',
        t1schoolValidation: 'body.path-node-20876',
        t2accessLnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[4]/div[1]/a[4]',
            locateStrategy: 'xpath'
        },
        t2accessVal: 'body.path-node-21516',
        t2conditionsLnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[3]/div[1]/a[1]',
            locateStrategy: 'xpath'
        },
        t2conditionsVal: 'body#ICP .bx-viewport',
        t2emergencyLnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[1]/div[1]/a[4]',
            locateStrategy: 'xpath'
        },
        t2emergencyVal: 'body.path-node-20736',
        t2findCareDD: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[1]/a[1]',
            locateStrategy: 'xpath'
        },
        t2findHealthDD: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[3]/a[1]',
            locateStrategy: 'xpath'
        },
        t2findInterpreterLnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[4]/div[1]/a[5]',
            locateStrategy: 'xpath'
        },
        t2findInterpreterVal: 'body.path-node-21371',
        t2findLocation1Lnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[1]/div[1]/a[7]',
            locateStrategy: 'xpath'
        },
        t2findLocation2Lnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[4]/div[1]/a[6]',
            locateStrategy: 'xpath'
        },
        t2findLocationVal: 'body.path-search-locations',
        t2findPharmaLnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[4]/div[1]/a[7]',
            locateStrategy: 'xpath'
        },
        t2findPharmaVal: 'body.path-node-21376',
        t2findProvider1Lnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[1]/div[1]/a[6]',
            locateStrategy: 'xpath'
        },
        t2findProviderVal: 'body.path-search-providers',
        t2iWantToDD: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[4]/a[1]',
            locateStrategy: 'xpath'
        },
        t2makeAppt1Lnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[2]/a[1]',
            locateStrategy: 'xpath'
        },
        t2makeApptVal: 'body.path-node-20836',
        t2makeAppt2Lnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[4]/div[1]/a[1]',
            locateStrategy: 'xpath'
        },
        t2makeGiftLnk: 'div#makeAGift a',
        t2makeGiftVal: 'body.page-id-1096',
        t2medSpecialtiesLnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[1]/div[1]/a[5]',
            locateStrategy: 'xpath'
        },
        t2medSpecialtiesVal: 'body.path-node-21746',
        t2patientEducationLnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[3]/div[1]/a[4]',
            locateStrategy: 'xpath'
        },
        t2patientEducationVal: 'body.path-node-21386',
        t2patientResourcesLnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[3]/div[1]/a[3]',
            locateStrategy: 'xpath'
        },
        t2patientResourcesVal: 'body.path-node-20866',
        t2payBillLnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[4]/div[1]/a[3]',
            locateStrategy: 'xpath'
        },
        t2payBillVal: 'body.path-node-50051',
        t2preventionLnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[3]/div[1]/a[2]',
            locateStrategy: 'xpath'
        },
        t2preventionVal: 'body#ICP .BreadCrumbs ',
        t2primaryCareLnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[1]/div[1]/a[2]',
            locateStrategy: 'xpath'
        },
        t2primaryCareVal: 'body.path-node-20701',
        t2referPatientLnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[4]/div[1]/a[9]',
            locateStrategy: 'xpath'
        },
        t2referPatientVal: 'body.path-node-21401',
        t2signUpLnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[4]/div[1]/a[2]',
            locateStrategy: 'xpath'
        },
        t2signUpVal: 'body.path-node-20841',
        t2urgentCareLnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[1]/div[1]/a[1]',
            locateStrategy: 'xpath'
        },
        t2urgentCareVal: 'body.path-node-21946',
        t2viewPatientLnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[4]/div[1]/a[8]',
            locateStrategy: 'xpath'
        },
        t2viewPatientVal: 'body.path-node-20866',
        t2virtualClinicLnk: {
            selector: '/html[1]/body[1]/header[1]/nav[1]/div[3]/div[1]/ul[1]/li[1]/div[1]/a[3]',
            locateStrategy: 'xpath'
        },
        t2virtualClinicVal: 'body.path-node-49201'
    }
};
