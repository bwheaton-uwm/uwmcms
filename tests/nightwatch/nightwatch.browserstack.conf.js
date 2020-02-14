//nightwatch.browserstack.conf.js
require('dotenv').config();
module.exports = {
    src_folders: ["tests/site"],
    page_objects_path: ["tests/page_objects"],
    custom_assertions_path: "tests/custom_assertions",
    custom_commands_path: "tests/custom_commands",
    globals_path: "tests/globals.js",

    "webdriver": {
        'start_process': false,
        'host': 'hub-cloud.browserstack.com',
        'port': 443
    },

    "test_settings": {
        "default": {
            'screenshots': {
                'enabled': true,
                'on_failure': true,
                'on_error': true,
                'path': 'tests_output/screenshots'
            },
            "desiredCapabilities": {
                "browserstack.user": process.env.BROWSERSTACK_USER,
                "browserstack.key": process.env.BROWSERSTACK_KEY,
                "os": "Windows",
                "os_version": "10",
                "browser": "IE",
                "browser_version": "11.0",
                "resolution": "2048x1536",
                ['browserstack.local']: false
            }
        }
    }
};