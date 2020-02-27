// page_objects.makeAnAppointment.js
module.exports = {
    url: 'https://www.uwmedicine.org/services/urgent-care',
	commands:[
		{
			verifyBreadcrumbLinks: function (urlValidator) {
                return this.verifyLinkClick('@homeLink', this.elements.homePage, this.url,
                    urlValidator, '"Home" breadcrumb');
            },
            verifyLocationSiteLinks: function(urlValidator) {
                locationSiteLink = [
                    'a.cta-to-locations-primary-care-ballard span',
                    'a.cta-to-locations-primary-care-federal-way span',
                    'a.cta-to-locations-primary-care-issaquah span',
                    'a.cta-to-locations-primary-care-ravenna span',
                    'a.cta-to-locations-primary-care-shoreline span',
                ];
                locationSitePage = [
                    'body.path-node-22271',
                    'body.path-node-22241',
                    'body.path-node-22251',
                    'body.path-node-22266',
                    'body.path-node-22221',
                ];

                for (let i=0; i<locationSiteLink.length; i++){
                    this.verifyLinkClick(locationSiteLink[i], locationSitePage[i], this.url, urlValidator, 'Location Site');
                }
            },
            verifyLocationAddressLinks: function (urlValidator) {
                locationAddressLink = [
                    '.cta-parent-res-clinic:nth-child(1) .clinic-card__street-address',
                    '.cta-parent-res-clinic:nth-child(2) .clinic-card__street-address',
                    '.cta-parent-res-clinic:nth-child(3) .clinic-card__street-address',
                    '.cta-parent-res-clinic:nth-child(4) .clinic-card__street-address',
                    '.cta-parent-res-clinic:nth-child(5) .clinic-card__street-address',
                ];
                locationAddressPage = [
                    'body.path-node-22271',
                    'body.path-node-22241',
                    'body.path-node-22251',
                    'body.path-node-22266',
                    'body.path-node-22221',
                ];

                for (let i = 0; i < locationAddressLink.length; i++) {
                    this.verifyLinkClick(locationAddressLink[i], locationAddressPage[i], this.url,
                        urlValidator, 'Location Address');
                }
            },
            verifySeeFullHoursLinks: function (urlValidator) {
                hoursSeeFullLink = [
                    '',
                    '',
                    '',
                    '',
                    '',
                ];
                hoursSeeFullPage = [
                    '',
                    '',
                    '',
                    '',
                    '',
                ];

                for (let i = 0; i < hoursSeeFullLink.length; i++) {
                    this.verifyLinkClick(hoursSeeFullLink[i], hoursSeeFullPage[i], this.url,
                        urlValidator, 'See Full Location Hours');
                }
            },
            verifyTelephoneLinks: function (urlValidator) {
                telLink = [
                    '',
                    '',
                    '',
                    '',
                    '',
                ];
                telPage = [
                    '',
                    '',
                    '',
                    '',
                    '',
                ];

                for (let i = 0; i < telLink.length; i++) {
                    this.verifyLinkClick(telLink[i], telLinkPage[i], this.url,
                        urlValidator, 'Telephone Link Button');
                }
            },
            verifyBookOnlineLinks: function (urlValidator) {
                bookOnlineLink = [
                    '',
                    '',
                    '',
                    '',
                    '',
                ];
                bookOnlinePage = [
                    '',
                    '',
                    '',
                    '',
                    '',
                ];

                for (let i = 0; i < bookOnlineLink.length; i++) {
                    this.verifyLinkClick(bookOnlineLink[i], bookOnlinePage[i], this.url,
                        urlValidator, 'Book Online Button');
                }
            },
            verifyGetInLineLinks: function (urlValidator) {
                getInLineLink = [
                    '',
                    '',
                    '',
                    '',
                    '',
                ];
                getInLinePage = [
                    '',
                    '',
                    '',
                    '',
                    '',
                ];

                for (let i = 0; i < getInLineLink.length; i++) {
                    this.verifyLinkClick(getInLineLink[i], getInLinePage[i], this.url,
                        urlValidator, 'Get In Line');
                }
            },
            verifySeeDertailsLinks: function (urlValidator) {
                seeDetailsLink = [
                    '',
                    '',
                    '',
                    '',
                    '',
                ];
                seeDetailsPage = [
                    '',
                    '',
                    '',
                    '',
                    '',
                ];

                for (let i = 0; i < seeDetailsLink.length; i++) {
                    this.verifyLinkClick(seeDetailsLink[i], seeDetailsPage[i], this.url,
                        urlValidator, 'See Details');
                }
            },

		}
	],
	elements: {
        homeLink: {
            selector: '//div[@class="half-wide-hero__breadcrumb-nonmobile"]//a[@class=' +
                '"breadcrumb__text breadcrumb__text--link"][contains(text(),"Home")]',
            locateStrategy: 'xpath'
        },
        homePage: 'body.path-node-48106',
        //virtualClinicGetCareLink: '',
        //virtualClinicGetCarePage: ''
	}
};




