module.exports = {

  "disabled": false,
  "@tags": [
    "rar",
    "basic",
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

  'Verifying we can open RAR\'s home page': function (browser) {

    browser
      .url('https://rightasrain.uwmedicine.org/')
      .assert.titleContains('Rain')
      .waitForElementVisible('.region-content')
      .assert.containsText('.region-content', 'Latest');


  },

  'Verifying we have a featured article, and it\'s image and summary': function (browser) {

    browser
      .assert.containsText('.region-content ' +
      '.block-views-blockhighlighted-article-block ' +
      '.field--name-field-long-summary',
      '.')
      .assert.visible('.region-content ' +
      '.block-views-blockhighlighted-article-block ' +
      '.field--name-field-primary-media img');


  },

  'Verifying we can search for "heart" results': function (browser) {
    browser
      .assert.visible('.region-header input[name=search]')
      .setValue('.region-header input[name=search]', 'Sleep')
      .assert.visible('.region-header button[type=submit]');

    browser.click('.region-header button[type=submit]', function (result) {
      console.log('Click result', result);
    });


  },

  'Verifying a search result is clickable': function (browser) {

    let linkSelector = '.view-content .views-row:last-of-type a';
    let articleUrl;
    browser
      .waitForElementVisible('.region-content .view-content  .views-row')
      .assert.containsText('.region-content .view-content', 'Sleep');

    browser.assert.visible(linkSelector);

    browser.getAttribute('css selector', linkSelector, 'href', function(result) {

      articleUrl = result.value;
      console.log('linkSelector href', result);
      console.log('articleUrl', articleUrl);

      browser.url(articleUrl);

    });




  },

  'Verifying we have article content and an author': function (browser) {


    browser.waitForElementVisible('.main-container')
      .getText('.field--name-field-author', function (result) {
        browser.assert.ok(result.value.length > 10);
        console.log(result.value, result.value.length);
      })
      .getText('.field--name-field-sections .field--name-field-body', function (result) {
        browser.assert.ok(result.value.length > 400);
        console.log(result.value, result.value.length);
      });


  }


};
