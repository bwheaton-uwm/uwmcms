// page_objects.uwmHome.js
module.exports = {
	url: 'https://www.uwmedicine.org/',
	commands:[
		{
			verifyHeroCtaLinks: function (pageUrl, testUrlValidation) {
				//heroCtaBtn1
				this
					.assert.visible('@heroCtaBtn1Lnk', 'Make an appointment CTA Hero link appears.')
					.click('@heroCtaBtn1Lnk')
					.waitForElementVisible('@heroCtaBtn1Val', 'Make an appointment CTA Hero link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				//heroCtaBtn2
				this
					.assert.visible('@heroCtaBtn2Lnk', 'Find Urgent Care CTA Hero link appears.')
					.click('@heroCtaBtn2Lnk')
					.waitForElementVisible('@heroCtaBtn2Val', 'Find Urgent Care CTA Hero link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				//heroCtaBtn3
				this
					.assert.visible('@heroCtaBtn3Lnk', 'Find a Provider CTA Hero link appears.')
					.click('@heroCtaBtn3Lnk')
					.waitForElementVisible('@heroCtaBtn3Val', 'Find a Provider CTA Hero link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				//heroCtaBtn4
				this
					.assert.visible('@heroCtaBtn4Lnk', 'Find a Location CTA Hero link appears.')
					.click('@heroCtaBtn4Lnk')
					.waitForElementVisible('@heroCtaBtn4Val', 'Find a Location CTA Hero link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');

					return this;
			},
			verifySectionLinks: function (pageUrl, testUrlValidation) {
				// Section 1
				this
					.assert.visible('@homeSection1Btn1Lnk', 'Billing and Insurance Section 1 link appears.')
					.click('@homeSection1Btn1Lnk')
					.waitForElementVisible('@homeSection1Btn1Val', 'Billing and Insurance Section 1 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				this
					.assert.visible('@homeSection1Btn2Lnk', 'eCare patient portal Section 1 link appears.')
					.click('@homeSection1Btn2Lnk')
					.waitForElementVisible('@homeSection1Btn2Val', 'eCare patient portal Section 1 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				this
					.assert.visible('@homeSection1Btn3Lnk', 'Patient and Family Section 1 link appears.')
					.click('@homeSection1Btn3Lnk')
					.waitForElementVisible('@homeSection1Btn3Val', 'Patient and Family Section 1 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				this
					.assert.visible('@homeSection1Btn4Lnk', 'Available med services Section 1 link appears.')
					.click('@homeSection1Btn4Lnk')
					.waitForElementVisible('@homeSection1Btn4Val', 'Available med services Section 1 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				this
					.assert.visible('@homeSection1Btn5Lnk', 'Patient Care Section 1 link appears.')
					.click('@homeSection1Btn5Lnk')
					.waitForElementVisible('@homeSection1Btn5Val', 'Patient Care Section 1 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				// Section 2
				this
					.assert.visible('@homeSection2Btn1Lnk', 'Latest research Section 2 link appears.')
					.click('@homeSection2Btn1Lnk')
					.waitForElementVisible('@homeSection2Btn1Val', 'Latest research Section 2 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				this
					.assert.visible('@homeSection2Btn2Lnk', 'Departments Section 2 link appears.')
					.click('@homeSection2Btn2Lnk')
					.waitForElementVisible('@homeSection2Btn2Val', 'Departments Section 2 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				this
					.assert.visible('@homeSection2Btn3Lnk', 'Participate Section 2 link appears.')
					.click('@homeSection2Btn3Lnk')
					.waitForElementVisible('@homeSection2Btn3Val', 'Participate Section 2 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				this
					.assert.visible('@homeSection2Btn4Lnk', 'Accomplishments Section 2 link appears.')
					.click('@homeSection2Btn4Lnk')
					.waitForElementVisible('@homeSection2Btn4Val', 'Accomplishments Section 2 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				this
					.assert.visible('@homeSection2Btn5Lnk', 'Research Section 2 link appears.')
					.click('@homeSection2Btn5Lnk')
					.waitForElementVisible('@homeSection2Btn5Val', 'Research Section 2 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				this
					.assert.visible('@homeSection2Btn6Lnk', 'Faculty Section 2 link appears.')
					.click('@homeSection2Btn6Lnk')
					.waitForElementVisible('@homeSection2Btn6Val', 'Faculty Section 2 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				// Section 3
				this
					.assert.visible('@homeSection3Btn1Lnk', 'Admissions Section 3 link appears.')
					.click('@homeSection3Btn1Lnk')
					.waitForElementVisible('@homeSection3Btn1Val', 'Admissions Section 3 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				this
					.assert.visible('@homeSection3Btn2Lnk', 'Departments Section 3 link appears.')
					.click('@homeSection3Btn2Lnk')
					.waitForElementVisible('@homeSection3Btn2Val', 'Departments Section 3 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				this
					.assert.visible('@homeSection3Btn3Lnk', 'WWAMI Section 3 link appears.')
					.click('@homeSection3Btn3Lnk')
					.waitForElementVisible('@homeSection3Btn3Val', 'WWAMI Section 3 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				this
					.assert.visible('@homeSection3Btn4Lnk', 'Award-winning Section 3 link appears.')
					.click('@homeSection3Btn4Lnk')
					.waitForElementVisible('@homeSection3Btn4Val', 'Award-winning Section 3 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				this
					.assert.visible('@homeSection3Btn5Lnk', 'School of Medicine Section 3 link appears.')
					.click('@homeSection3Btn5Lnk')
					.waitForElementVisible('@homeSection3Btn5Val', 'School of Medicine Section 3 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				this
					.assert.visible('@homeSection3Btn6Lnk', 'Faculty Resources Section 3 link appears.')
					.click('@homeSection3Btn6Lnk')
					.waitForElementVisible('@homeSection3Btn6Val', 'Faculty Resources Section 3 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');

				return this;
			},
			verifySpotlightLinks: function (pageUrl, testUrlValidation) {
				this
					.assert.visible('@spotlightBtn1Lnk', 'Spotlight 1 link appears.')
					.click('@spotlightBtn1Lnk')
					.waitForElementVisible('@spotlightBtn1Val', 'Spotlight 1 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				this
					.assert.visible('@spotlightBtn2Lnk', 'Spotlight 2 link appears.')
					.click('@spotlightBtn2Lnk')
					.waitForElementVisible('@spotlightBtn2Val', 'Spotlight 2 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				this
					.assert.visible('@spotlightBtn3Lnk', 'Spotlight 3 link appears.')
					.click('@spotlightBtn3Lnk')
					.waitForElementVisible('@spotlightBtn3Val', 'Spotlight 3 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');
				this
					.assert.visible('@spotlightBtn4Lnk', 'Spotlight 4 link appears.')
					.click('@spotlightBtn4Lnk')
					.waitForElementVisible('@spotlightBtn4Val', 'Spotlight 4 link loads correct page.')
					.navigate(pageUrl)
					.waitForElementVisible(testUrlValidation, 'Returned to ' + pageUrl + '.');

				return this;
			}
		}
	],
	elements: {
		heroBackgroundImg: 'section.hero--background-image',
		heroCtaBtn1Lnk: 'div.hero__ctas a:nth-child(1)',
		heroCtaBtn1Val: 'body.path-node-20836',
		heroCtaBtn2Lnk: 'div.hero__ctas a:nth-child(2)',
		heroCtaBtn2Val: 'body.path-node-21946',
		heroCtaBtn3Lnk: 'div.hero__ctas a:nth-child(3)',
		heroCtaBtn3Val: 'body.path-search-providers',
		heroCtaBtn4Lnk: 'div.hero__ctas a:nth-child(4)',
		heroCtaBtn4Val: 'body.path-search-locations',
		heroHeading: 'div.hero__content h1',
		homeSection1Btn1Lnk: '.uwm-accent-color__teal .links-with-icon__item:nth-child(1) span',
		homeSection1Btn1Val: 'body.path-node-50051',
		homeSection1Btn2Lnk: '.uwm-accent-color__teal .links-with-icon__item:nth-child(2) span',
		homeSection1Btn2Val: 'body.path-node-20841',
		homeSection1Btn3Lnk: '.uwm-accent-color__teal .links-with-icon__item:nth-child(3) span',
		homeSection1Btn3Val: 'body.path-node-20866',
		homeSection1Btn4Lnk: '.uwm-accent-color__teal .links-with-icon__item:nth-child(4) span',
		homeSection1Btn4Val: 'body.path-node-21746',
		homeSection1Btn5Lnk: '.uwm-accent-color__teal .btn-cta',
		homeSection1Btn5Val: 'body.path-node-20866',
		homeSection2Btn1Lnk: '.uwm-accent-color__orange .links-with-icon__item:nth-child(1) .btn',
		homeSection2Btn1Val: 'header.headroom--top',
		homeSection2Btn2Lnk: '.uwm-accent-color__orange .links-with-icon__item:nth-child(2) .btn',
		homeSection2Btn2Val: 'body.path-node-21651',
		homeSection2Btn3Lnk: '.uwm-accent-color__orange .links-with-icon__item:nth-child(3) .btn',
		homeSection2Btn3Val: '.wpb_wrapper h2',
		homeSection2Btn4Lnk: '.uwm-accent-color__orange .links-with-icon__item:nth-child(4) .btn',
		homeSection2Btn4Val: 'body.path-node-21506',
		homeSection2Btn5Lnk: '.uwm-accent-color__orange .col-lg:nth-child(1) .btn-cta',
		homeSection2Btn5Val: 'body.path-node-21421',
		homeSection2Btn6Lnk: '.uwm-accent-color__orange .col-lg:nth-child(2) .btn-cta',
		homeSection2Btn6Val: 'body.page-id-33',
		homeSection3Btn1Lnk: '.uwm-accent-color__blue .links-with-icon__item:nth-child(1) .btn',
		homeSection3Btn1Val: 'body.path-node-20881',
		homeSection3Btn2Lnk: '.uwm-accent-color__blue .links-with-icon__item:nth-child(2) .btn',
		homeSection3Btn2Val: 'body.path-node-20916',
		homeSection3Btn3Lnk: '.uwm-accent-color__blue .links-with-icon__item:nth-child(3) .btn',
		homeSection3Btn3Val: 'body.path-node-21501',
		homeSection3Btn4Lnk: '.uwm-accent-color__blue .links-with-icon__item:nth-child(4) .btn',
		homeSection3Btn4Val: 'body.path-node-21671',
		homeSection3Btn5Lnk: '.uwm-accent-color__blue .col-lg:nth-child(1) .btn-cta',
		homeSection3Btn5Val: 'body.path-node-20876',
		homeSection3Btn6Lnk: '.uwm-accent-color__blue .col-lg:nth-child(2) .btn-cta',
		homeSection3Btn6Val: 'body.page-id-33',
		spotlightBtn1Lnk: '.content-spotlight__item:nth-child(1) .btn-cta',
		spotlightBtn1Val: 'body.page-node-type-article',
		spotlightBtn2Lnk: '.content-spotlight__item:nth-child(2) .btn-cta',
		spotlightBtn2Val: 'body.page-node-type-article',
		spotlightBtn3Lnk: '.content-spotlight__item:nth-child(3) .btn-cta',
		spotlightBtn3Val: 'body.page-node-5157',
		spotlightBtn4Lnk: '.content-spotlight__item:nth-child(4) .btn-cta',
		spotlightBtn4Val: 'header.headroom--top'
	}
};
