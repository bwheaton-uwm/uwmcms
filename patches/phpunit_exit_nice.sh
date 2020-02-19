#!/usr/bin/env bash

set -e

#
# Shell script to have phpunit exit quietly if called with no options.
#
# BLT is running this command during execution on TRavis-CI:
# `$ $TRAVIS_BUILD_DIR/vendor/bin/phpunit`
# Because phpunit is called without any options, it shows help text and
# exits with exit code 2. That breaks the build.
#
# This script/ patch can be removed once linting passes and BLT knows phpunit
# is called upstream during other build test steps.
#
# Until then, make phpunit exit 0 if called without args.
#
#

echo ""
echo ""
echo "UWM-CMS: Making phpunit exit 0, if called with no options."

FILE=vendor/bin/phpunit
if [ ! -f $FILE ]; then
    FILE=../vendor/bin/phpunit
fi
if [ ! -f $FILE ]; then
    FILE=../../vendor/bin/phpunit
fi

MATCH='require PHPUNIT_COMPOSER_INSTALL;'
REPLACE='/*** UWMCMS Patch ***/\
if(empty($argv) || count($argv) < 2) exit (0);\
\
'

sed -i '' "s/${MATCH}/${REPLACE}${MATCH}/g" $FILE


echo ""
echo ""





set +v
