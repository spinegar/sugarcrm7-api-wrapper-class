SugarCRM REST Client For SugarCRM 7
=================================================

License: MIT


Contents
--------
About
Installation
Usage Example
Custom and/or Undefined Endpoints
Troubleshooting


About
-------
A simple PHP library interacting with the SugarCRM v10 or later REST API.

v2.0.0
-------
v2.0.0 provides some minor updates to keep the library up to date with the PHP ecosystem.

- The namespace has been updated to avoid versioning confusion with Sugar. You will be required to update the namespace to `\Spinegar\SugarRestClient\Rest` to instantiate the library.

- Support for Guzzle 6 has been added.

- The `connect` method has been deprecated and is now handled by the library when interacting with the Sugar API. 

Installation via Composer
----------------------------
Edit composer.json

```json
{
	"require": {
		"spinegar/sugar7wrapper": "^v2.0.0"
	}
}
```

Then install with composer

```bash
$ composer install
```


3.Usage Examples
---------------

```php
/* Instantiate and authenticate */
$sugar = new \Spinegar\SugarRestClient\Rest();

$sugar->setUrl('https://sugar/rest/v11/')
	->setUsername('user')
	->setPassword('password');

/* Instantiate and authenticate for a specific platform*/
$sugar->setUrl('https://sugar/rest/v11/')
    ->setUsername('user')
    ->setPassword('password')
    ->setPlatform('api');

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
	
/* Retrieve all records with filter params in the Contacts module */
$sugar->filter('Contacts', array(
    'filter' => array(
        array('first_name' => 'First Name'),
    )
));

/* Count all records with filter params in the Cases module */
$sugar->countRecords('Cases', array(
    'filter' => array(
        array('status' => 'New'),
    )
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
));

/* Delete relationship between an account and case */
$sugar->unrelate('Accounts', $record_id, 'cases', $related_record_id);

/* Update relationship data */
$sugar->updateRelationship('Opportunities', $record_id, 'contacts', $related_record_id, array(
	'contact_role' => 'Influencer'
));

/* Retrieve a list of attachments for a case */
$attachments = $sugar->related('Cases', $record_id, 'notes');

foreach($attachments['records'] as $attachment)
{
	$output[] = $sugar->files('Notes', $attachment['id']);
}

return $output;

/* Delete the file associated to the filename field of a note */
$sugar->deleteFile('Notes', $record_id, 'filename');

/* Download  the file associated to the filename field of a note to the server */
$sugar->download('Notes', $record_id, 'filename', '/path/to/destination.ext');

/* Upload a file associated to the filename field of a note to the server */
$sugar->upload('Notes', $record_id, 'filename', '/path/of/local/file.ext');

```

4. Custom & Undefined Endpoints
----------------------------
Call custom or undefined endpoints using the following methods.

```php
/* Get Endpoint*/
$parameters = array();
$sugar->getEndpoint('MyCustomEndpoint', $parameters);

/* Post Endpoint*/
$parameters = array();
$sugar->postEndpoint('MyCustomEndpoint', $parameters);

/* Put Endpoint*/
$parameters = array();
$sugar->putEndpoint('MyCustomEndpoint', $parameters);

/* Delete Endpoint*/
$parameters = array();
$sugar->deleteEndpoint('MyCustomEndpoint', $parameters);
```


5. Troubleshooting
----------------------------
If you are having trouble connecting to a secured site (https), try the following:

```php
$sugar = new \Spinegar\Sugar7Wrapper\Rest();
$sugar->setClientOption('verify', false); // This is the important part
```
