#!/usr/bin/env bash

set -e


#
# Copy default for defaul install.
#

echo ""
echo ""
echo "UWM-CMS: Seeting up vendor SimpleSAMLphp default configurations."

SAMLDIR=vendor/simplesamlphp/simplesamlphp
if [ ! -d $SIMPSAML ]; then
    SAMLDIR=../../vendor/simplesamlphp/simplesamlphp

fi

cp -rf $SAMLDIR/config-templates/* $SAMLDIR/config/
cp -rf $SAMLDIR/metadata-templates/* $SAMLDIR/metadata/



#
# Copy custom configuration to four files needed.
#

echo "UWM-CMS: Copying custom SimpleSAMLphp configuration to default configuration files."


DESTINATION=$SAMLDIR/config/config.php
CODE=`cat <<"EOF"

    /***
     * Include UWMCMS configurations.
     *
     ***/
    if(!empty($_ENV['AH_SITE_ENVIRONMENT'])) {

        $config_file = sprintf('/mnt/gfs/%s.%s/nobackup/cms.uwmedicine.org/simplesamlphp/%s/config/acquia_config.php',
            $_ENV['AH_SITE_GROUP'],
            $_ENV['AH_SITE_ENVIRONMENT'],
            $_ENV['AH_SITE_ENVIRONMENT']);
        if (!file_exists($config_file) || !require_once($config_file)) {

            throw new Exception('Missing config file: '. \$config_file);

        }
    }

EOF
`
echo "$CODE" >> $DESTINATION


#
# Next:
#
DESTINATION=$SAMLDIR/config/authsources.php
CODE=`cat <<"EOF"

    /***
     * Include UWMCMS configurations.
     *
     ***/
    if(!empty($_ENV['AH_SITE_ENVIRONMENT'])) {

        $config_file = sprintf('/mnt/gfs/%s.%s/nobackup/cms.uwmedicine.org/simplesamlphp/%s/config/authsources.php',
            $_ENV['AH_SITE_GROUP'],
            $_ENV['AH_SITE_ENVIRONMENT'],
            $_ENV['AH_SITE_ENVIRONMENT']);
        if (!file_exists($config_file) || !require_once($config_file)) {

            throw new Exception('Missing config file: '. $config_file);

        }
    }

EOF
`
echo "$CODE" >> $DESTINATION



#
# Next:
#
DESTINATION=$SAMLDIR/metadata/saml20-idp-remote.php
CODE=`cat <<"EOF"

    /***
     * Include UWMCMS configurations.
     *
     ***/
    if(!empty($_ENV['AH_SITE_ENVIRONMENT'])) {

        $config_file = sprintf('/mnt/gfs/%s.%s/nobackup/cms.uwmedicine.org/simplesamlphp/%s/metadata/saml20-idp-remote.php',
            $_ENV['AH_SITE_GROUP'],
            $_ENV['AH_SITE_ENVIRONMENT'],
            $_ENV['AH_SITE_ENVIRONMENT']);
        if (!file_exists($config_file) || !require_once($config_file)) {

            throw new Exception('Missing config file: '. $config_file);

        }
    }

EOF
`
echo "$CODE" >> $DESTINATION

echo ""
echo ""





set +v
