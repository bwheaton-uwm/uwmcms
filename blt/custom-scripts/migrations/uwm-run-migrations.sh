#!/usr/bin/env bash

DRUSH='vendor/bin/drush'
ALIAS='@stevie.local'

$DRUSH $ALIAS st



migration_loop()
{
	# Better readability with separation.
	echo "========================";
	# Get the output of the drush status.
	drush_output=$($DRUSH $ALIAS ms | grep $1);

	# Split output string into an array.
	output=( $drush_output );

	# Output the status items.
	for index in "${!output[@]}"
	do

    	if [ $index == "0" ]
    	then
        	echo "Migration: ${output[index]}";
    	fi

    	if [ $index == "1" ]
    	then
        	echo "Status: ${output[index]}";
    	fi

    	if [ $index == "2" ]
    	then
        	echo "Total: ${output[index]}";
    	fi

    	if [ $index == "3" ]
    	then
        	echo "Imported: ${output[index]}";
    	fi

    	if [ $index == "4" ]
    	then
        	echo "Remaining: ${output[index]}";
    	fi

	done

	# Check if all items were imported.
	if [ "${output[4]}" == "0" ]
	then
    	echo "No items left to import.";
	else
    	echo "There are ${output[4]} remaining ${output[0]} items to be imported.";
    	echo "Running command: drush mi $1";
    	echo "...";
    	# Run the migration until it stops.
    	drush mi $1;
    	# Run the check on this migration again.
    	migration_loop $1;
	fi
}

migration_loop
