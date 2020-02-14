const args = require('minimist')(process.argv);

module.exports = {

  "browserstackUser": process.env.BROWSERSTACK_USER,
  "browserstackKey": process.env.BROWSERSTACK_KEY,

  "sites": {

    "rar": {
      "local": "http://chew.uwmed.local",
      "dev": "https://chew.cmsdev.uwmedicine.org",
      "test": "https://chew.cmsstage.uwmedicine.org",
      "prod": "https://rightasrain.uwmedicine.org",

      get launch_url() {
        let e = args.environment || "test";
        return this[e];
      },

    },

    "huddle": {
      "local": "http://huddle.uwmed.local",
      "dev": "https://huddle.cmsdev.uwmedicine.org",
      "test": "https://huddle.cmstest.uwmedicine.org",
      "prod": "https://huddle.uwmedicine.org",

      get launch_url() {
        let e = args.environment || "test";
        return this[e];
      },

    },

    "stevie": {
      "local": "http://stevie.uwmed.local",
      "dev": "https://stevie.cmsdev.uwmedicine.org",
      "test": "https://stevie.cmsstage.uwmedicine.org",
      "prod": "https://www.uwmedicine.org",

      get launch_url() {
        let e = args.environment || "test";
        return this[e];
      },
    }

  }


};



