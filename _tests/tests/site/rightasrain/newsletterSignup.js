module.exports = {

  "disabled": false,
  "@tags": [
    "rar",
    "basic",
    "form"
  ],

  //--window-position=-32000,-32000

  url: "https://rightasrain.uwmedicine.org/",

  before: function (browser) {

    console.log('Setting up...');

  },

  after: function (browser) {

    console.log('Closing down...');
    browser.end();

  },

  beforeEach: function (browser) {

    console.log('beforeEach up...');

    browser.url(function (result) {
      console.log(result);
    });

  },

  afterEach: function (browser) {

    console.log('afterEach up...');

    browser.url(function (result) {
      console.log(result);
    });

  },

  'Verifying we see RAR\'s subscribe widget': function (browser) {

    let blockRegion = '.region-sidebar-second';

    browser
      .url('https://rightasrain.uwmedicine.org/')
      .assert.titleContains('Rain')
      .waitForElementVisible(blockRegion)
      .assert.containsText(blockRegion, 'Subscribe to our newsletter');


  },

  'Verifying we can submit our email address to sign up': function (browser) {

    let emailInput = '.region-sidebar-second input[name="Email Address"]';
    let submitSelector = '.region-sidebar-second button.btn-primary';
    let emailValue = 'uwmweb@uw.edu';

    browser
      .assert.visible(emailInput)
      .setValue(emailInput, emailValue)
      .click(submitSelector);


  },

  'Verifying submit takes us to thank you page': function (browser) {

    browser
      .assert.titleContains('Thank you for subscribing')
      .assert.containsText('.page-header', 'Thank you');

  }

};
