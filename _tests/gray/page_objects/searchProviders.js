module.exports = {
	url: "https://www.uwmedicine.org/search/providers",
	elements: {
		cookiesAlertButton: {
			selector: ".eu-cookie-compliance-default-button"
		},
		pageTitle: {
			selector: ".page-title"
		},
		providerSearchBar: {
			selector: "#edit-s"
		},
		resetButton: {
			selector: "//a[@class='btn btn-cta btn-cta-link reset']",
			locateStrategy: "xpath"
		},
		searchButton: {
			selector: ".submit"
		}
	},
	sections: {
		providerCard: {
			selector: "article",
			elements: {
				accepting: {
					selector: ".provider-card__accepting-patients"
				},
				appointmentsLabel: {
					selector: ".btn-group h3"
				},
				calandarIcon: {
					selector: ".uwm-icon--calendar"
				},
				cardTitle: {
					selector: ".provider-card__title"
				},
				locationsLabel: {
					selector: ".provider-card__locations h3"
				},
				locationsPinIcon: {
					selector: ".uwm-icon--locations-pin"
				},
				locationsItems: {
					selector: ".items a"
				},
				locationsItemOne: {
					selector: ".items a:nth-child(1)"
				},
				locationItemsTwo: {
					selector: ".items a:nth-child(1)"
				},
				seeMoreButton: {
					selector: ".provider-card__see-more a.btn"
				},
				specialtiesLabel: {
					selector: ".provider-card__specialties h3",
				},
				specialtiesItems: {
					selector: ".provider-card__specialties .items"
				},
				viewContantDetails: {
					selector: ".provider-card__contact-information"
				}
			}
		}
	}
};

//
// .uwm-header form svg.svg-inline--fa.fa-search


