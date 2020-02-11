//nightwatch.conf.js
require('dotenv').config();
module.exports = {
    src_folders: ["tests/site"],
    page_objects_path: ["tests/page_objects"],
    custom_assertions_path: "tests/custom_assertions",
    custom_commands_path: "tests/custom_commands",
    globals_path: "tests/globals.js",

    'webdriver': {
        'start_process': true,
        'server_path': require('chromedriver').path,
        'port': 9515
    },

    'test_settings': {
        'default': {
            'screenshots': {
                'enabled': true,
                'on_failure': true,
                'on_error': true,
                'path': 'tests_output/screenshots'
            },
            'desiredCapabilities': {
                'browserName': 'chrome',
                'chromeOptions': {
                    //'args': ['--headless']
                },
            }
        }
    }
};