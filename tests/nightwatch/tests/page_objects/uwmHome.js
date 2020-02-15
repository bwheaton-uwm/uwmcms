// Gray: TODO
// With implementing page_objects.header.uwmHeader.js, this file is redundant.
// I will update this to be a valid page object for the Home page right after I am
// done implementing the header and footer page objects

module.exports = {
	url: "https://www.uwmedicine.org/",
	elements: {
		searchBar: {
			selector: "input[type=text]"
		},
		submit: {
			//selector: "/html/body/header/nav/div[1]/div[2]/form/div/svg"
			selector: "input.form-control"
		}
	}
};

