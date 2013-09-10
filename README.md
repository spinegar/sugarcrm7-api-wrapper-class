SugarCRM REST API Wrapper Class For SugarCRM 7
=================================================

License: MIT


Contents
--------
1. About
2. Installation
3. Usage Example


1.About
-------
- PHP wrapper class for interacting with a SugarCRM 7 REST API
- Designed to work with SugarCRM 7 and the v10 REST API

2. Installation via Composer
----------------------------
Edit composer.json

	{
		"require": {
			"spinegar/sugar7wrapper": "dev-master"
		},
		"minimum-stability": "dev"
	}

Then install with composer

	$ composer install



3.Usage Examples
---------------

	/* Instantiate and authenticate */
	$sugar = new \Spinegar\Sugar7Wrapper\Rest();

	$sugar->setUrl('https://sugar/rest/v10/');
	$sugar->setUsername('restUser');
	$sugar->setPassword('password');

	$sugar->connect();

	/* Retrieve all records in the Cases module */
	$sugar->search('Cases');

	/* Retrieve all records in the Cases module where the name = 'Case1 Name' or 'Case2 Name' */
	$sugar->search('Cases', array(
		'q' => '"Case1 Name" "Case2 Name"'
	)); 

	/* Retrieve the name field for all records in the Cases module */
	$sugar->search('Cases', array(
		'fields' => 'name'
	)); 

	/* Retrieve a specific record from the Cases module */
	$sugar->retrieve('Cases', $record_id);

	/* Create a case */
	$sugar->create('Cases', array(
		'name' => 'Case Name',
		'status' => 'Assigned'
	));

	/* Update a case */
	$sugar->update('Cases', $record_id, array(
	    	'status' => 'Closed'
	));

	/* Favorite a case */
	$sugar->favorite('Cases', $record_id);

	/* Unfavorite a case */
	$sugar->unfavorite('Cases', $record_id);

	/* Retrieve cases related to an account */
	$sugar->related('Accounts', $record_id, 'cases');

	/* Relate a case to an account */
	$sugar->relate('Accounts', $record_id, 'cases', $related_record_id);

	/*Relate a contact to an opportunity and set relationship data */
	$sugar->relate('Opportunities', $record_id, 'contacts', $related_record_id, array(
		'contact_role' => 'Influencer'
	))

	/* Delete relationship between an account and case */
	$sugar->unrelate('Accounts', $record_id, 'cases', $related_record_id);

	/* Update relationship data */
	$sugar->updateRelationship('Opportunities', $record_id, 'contacts', $related_record_id, array(
		'contact_role' => 'Influencer'
	))

	/* Retrieve a list of attachments for a case */
	$attachments = $sugar->related('Cases', $record_id, 'notes');

	foreach($attachments['records'] as $attachment)
	{
		$output[] = $sugar->files('Notes', $attachment['id'])
	}

	return $output;

	/* Delete the file associated to the filename field of a note */
	$sugar->deleteFile('Notes', $record_id, 'filename')

	/* Download  the file associated to the filename field of a note to the server */
	$sugar->download('Notes', $record_id, 'filename', '/path/to/destination');
