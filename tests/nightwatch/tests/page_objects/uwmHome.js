// page_objects.uwmHome.js
module.exports = {
	url: 'https://www.uwmedicine.org/',
	elements: {
		heroBackgroundImg: {
			selector: 'section.hero--background-image'
		},
		heroCtaBtn1: {
			selector: 'div.hero__ctas a:nth-child(1)'
		},
		heroCtaBtn2: {
			selector: 'div.hero__ctas a:nth-child(2)'
		},
		heroCtaBtn3: {
			selector: 'div.hero__ctas a:nth-child(3)'
		},
		heroCtaBtn4: {
			selector: 'div.hero__ctas a:nth-child(4)'
		},
		heroHeading: {
			selector: 'div.hero__content h1'
		}
	}
};

