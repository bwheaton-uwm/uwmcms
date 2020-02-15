//nightwatch.conf.js
require('dotenv').config();
module.exports = {
    custom_assertions_path: "tests/custom_assertions",
    custom_commands_path: [
        "tests/custom_commands",
        //"tests/custom_commands/footer",
        //"tests/custom_commands/header",
    ],
    globals_path: "tests/globals.js",
    page_objects_path: [
        "tests/page_objects",
        "tests/page_objects/header",
        "tests/page_objects/footer",
    ],
    src_folders: ["tests/site"],

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
                  "args": [
                    //"headless",
                    "window-size=1120,800"
                  ]
                },
            }
        }
    }
};
