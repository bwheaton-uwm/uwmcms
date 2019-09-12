#
# Shell script to prepend a file with contents from another file.
#
# This script can be used calling it, using two additional file names as paramenters. The
# example adds the contents of file_no1 to file_no2. We include a special "#" comment line,
# to check if the prepend has already happened for these two files. We exit early if already run.
#
# @example
# sh patches/drupal-htaccess/htaccess_prepend.sh \
#   'patches/drupal-htaccess/htaccess-prepend-contents.txt' \
#   'docroot/.htaccess'
#
# @endexample
#
# @see https://docs.acquia.com/blt/developer/patches/
# @see https://getcomposer.org/doc/articles/scripts.md
#

source_file=$1
destination_file=$2
destination_file_marker_comment="#### UWM-CMS: Finished prepend $1 to $2 ####"


DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

echo ""
echo ""
echo "UWM-CMS: Running htaccess_prepend.sh, to prepend one file onto another."

# If the patch has not been applied then the $? which is the exit status
# for last command would have a success status code = 0

if grep -q "$destination_file_marker_comment" $destination_file;
then

    echo "UWM-CMS: Seems contents were already prepended."

else

    echo "UWM-CMS: Prepending..."
    echo "UWM-CMS: The source is $1 and destination  $2."
    printf '%s\n%s\n%s' "$(cat $source_file)" "$destination_file_marker_comment" \
     "$(cat $destination_file)" \
     > $destination_file
    echo "UWM-CMS: Prepending done."

fi


