# UWMCMS - Dgtlmkt

#### [uwmedicine.org](https://uwmedicine.org)
 
***
Automated end-to-end testing framework powered by [Nightwatch](https://nightwatchjs.org) , [Node.js](http://nodejs.org) and using [W3C Webdriver](https://www.w3.org/TR/webdriver) (formerly [Selenium](https://github.com/SeleniumHQ/selenium/wiki/JsonWireProtocol)).

Nightwatch is a complete and integrated solution for end-to-end testing of web applications and websites, and also for Node.js unit and integration testing. 


## Up &amp; Running in 2 Minutes:

#### 1. Install Nightwatch and the NPM packages

```sh
npm install 
```

#### 2. This is similar to installing Nightwatch and driver dependencies 
```sh
npm install nightwatch --save-dev
npm install geckodriver --save-dev
npm install chromedriver --save-dev
```

#### 3. Run a test suite:
`npm run test -- --tag working`

To run the same tests using the BrowserStack drivers, use
`npm run test:browserstack -- --tag working`

#### 4. Or Run a Demo Test:

Nightwatch comes with an `examples` folder containing a few sample tests.

Below will run a basic test which opens the search engine [Ecosia.org](https://ecosia.org), searches for the term "nightwatch", and verifies if the term first result is the Nightwatch.js website.

```
sh ./node_modules/.bin/nightwatch node_modules/nightwatch/examples/tests/ecosia.js
```

Windows users__ might need to run `node node_modules/.bin/nightwatch`.


