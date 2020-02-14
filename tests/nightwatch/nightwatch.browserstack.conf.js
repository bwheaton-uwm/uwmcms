//nightwatch.browserstack.conf.js
require('dotenv').config();
module.exports = {
    custom_assertions_path: "tests/custom_assertions",
    custom_commands_path: [
        "tests/custom_commands",
        "tests/custom_commands/footer",
        "tests/custom_commands/header",
    ],
    globals_path: "tests/globals.js",
    page_objects_path: [
        "tests/page_objects",
        "tests/page_objects/header",
        "tests/page_objects/footer",
    ],
    src_folders: ["tests/site"],

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