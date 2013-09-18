<?php namespace Spinegar\Sugar7Wrapper;

use Guzzle\Common\Event;
use Guzzle\Http\Client;
use Guzzle\Http\Query;

/**
 * SugarCRM 7 REST API Class
 *
 * @package   Sugar7Wrapper
 * @category  Libraries
 * @author  Sean Pinegar
 * @license MIT License
 * @link    https://github.com/spinegar/sugar7wrapper
 */

class Rest {

  /**
  * Variable: $url
  * Description:  The URL of the SugarCRM REST API
  * Example:  https://sugar/rest/v10/
  */
  private $url = null;

  /**
  * Variable: $username
  * Description:  A SugarCRM User. 
  */
  private $username = null;

  /**
  * Variable: $password
  * Description:  The password for the $username SugarCRM account
  */
  private $password = null;

  /**
  * Variable: $token
  * Description:  OAuth 2.0 token
  */
  private $token = null;

  /**
  * Variable: $client
  * Description:  Guzzle Client
  */
  public $client = null;

  /**
  * Variable: $authenticated
  * Description:  Tells us if we are authenticated or not
  */
  public $authenticated = false;

  /**
  * Function: __destruct()
  * Parameters:   none    
  * Description:  OAuth2 Logout
  * Returns:  TRUE on success, otherwise FALSE
  */
  function __destruct()
  {
    if(!$this->client)
      return false;

    $request = $this->client->post('oauth2/logout');
    $request->setHeader('OAuth-Token', $this->token);    
    $result = $request->send()->json();

    return $result;
  }
  
  /**
  * Function: connect()
  * Parameters:   none    
  * Description:  Authenticate and set the oAuth 2.0 token
  * Returns:  TRUE on login success, otherwise FALSE
  */
  function connect()
  {
    $this->client = new Client($this->url);

    $request = $this->client->post('oauth2/token', null, array(
        'grant_type' => 'password',
        'client_id' => 'sugar',
        'username' => $this->username,
        'password' => $this->password,
    ));

    $results = $request->send()->json();
   
    if(!$results['access_token'])
      return false;

    $this->token = $results['access_token'];

    $this->client->getEventDispatcher()->addListener('request.before_send', function(Event $event) {
      $event['request']->setHeader('OAuth-Token', $this->token);
    });

    $this->authenticated = true;
    
    return true;
  }

  /**
  * Function: setUrl()
  * Parameters:   $value = URL for the REST API    
  * Description:  Set $url
  * Returns:  returns $url
  */
  function setUrl($value)
  {
    $this->url = $value;

    return $this;
  }

  /**
  * Function: setUsername()
  * Parameters:   $value = Username for the REST API User    
  * Description:  Set $username
  * Returns:  returns $username
  */
  function setUsername($value)
  {
    $this->username = $value;

    return $this;
  }

  /**
  * Function: setPassword()
  * Parameters:   none    
  * Description:  Set $password
  * Returns:  returns $passwrd
  */
  function setPassword($value)
  {
    $this->password = $value;

    return $this;
  }

  /**
  * Function: create()
  * Parameters:   $module = Record Type
  *   $fields = Record field values    
  * Description:  This method creates a new record of the specified type
  * Returns:  returns Array if successful, otherwise FALSE
  */
  function create($module, $fields)
  {
    $request = $this->client->post($module, null, $fields);
    $result = $request->send()->json();

    if(!$result)
      return false;

    return $result;
  }

  /**
  * Function: search()
  * Parameters:  $module - The module to work with
  *   $params = [
  *     q - Search the records by this parameter, if you don't have a full-text search engine enabled it will only search the name field of the records.  (Optional)
  *     maxResult - A maximum number of records to return Optional
  *     offset -  How many records to skip over before records are returned (Optional)
  *     fields -  Comma delimited list of what fields you want returned. The field date_modified will always be added  (Optional)
  *     order_by -  How to sort the returned records, in a comma delimited list with the direction appended to the column name after a colon. Example: last_name:DESC,first_name:DESC,date_modified:ASC (Optional)
  *     favorites - Only fetch favorite records (Optionall)
  *     deleted - Show deleted records in addition to undeleted records (Optional)
  *   ]
  * Description:  Search records in this module
  * Returns:  returns Object if successful, otherwise FALSE
  */
  function search($module, $params = array())
  {
    // return $params;
    $request = $this->client->get($module);

    $query = $request->getQuery();
    foreach($params as $key=>$value)
    {
      $query->add($key, $value);
    }

    $result = $request->send()->json();

    if(!$result)
      return false;

    return $result;
  }

  /**
  * Function: delete()
  * Parameters: $module = Record Type
  *   $record = The record to delete
  * Description:  This method deletes a record of the specified type
  * Returns:  returns Object if successful, otherwise FALSE
  */
  function delete($module, $record)
  {
    $request = $this->client->delete($module . '/' . $record);
    $result = $request->send();

    if(!$result)
      return false;

    return true;
  }

  /**
  * Function: retrieve()
  * Parameters: $module = Record Type
  *   $record = The record to retrieve
  * Description:  This method retrieves a record of the specified type
  * Returns:  Returns a single record
  */
  function retrieve($module, $record)
  {
    $request = $this->client->get($module . '/' . $record);
    $result = $request->send()->json();

    if(!$result)
      return false;

    return $result;
  }

  /**
  * Function: update()
  * Parameters: $module = Record Type
  *   $record = The record to update
  *   $fields = Record field values
  * Description:  This method updates a record of the specified type
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  function update($module, $record, $fields)
  {
    $request = $this->client->put($module . '/' . $record, null, json_encode($fields));
    $result = $request->send()->json();

    if(!$result)
      return false;

    return $result;
  }

  /**
  * Function: favorite()
  * Parameters: $module = Record Type
  *   $record = The record to favorite
  * Description:  This method favorites a record of the specified type
  * Returns:  Returns TRUE if successful, otherwise FALSE
  */
  function favorite($module, $record)
  {
    $request = $this->client->put($module . '/' . $record . '/favorite');
    $result = $request->send()->json();

    if(!$result)
      return false;

    return $result;
  }

  /**
  * Function: unfavorite()
  * Parameters: $module = Record Type
  *   $record = The record to unfavorite
  * Description:  This method unfavorites a record of the specified type
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  function unfavorite($module, $record)
  {
    $request = $this->client->delete($module . '/' . $record . '/favorite');
    $result = $request->send()->json();

    if(!$result)
      return false;

    return $result;
  }

  /**
  * Function: files()
  * Parameters: $module = Record Type
  *   $record = The record  we are working with
  * Description:  Gets a listing of files related to a field for a module record.
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  function files($module, $record)
  {
    $request = $this->client->get($module . '/' . $record . '/file');
    $result = $request->send()->json();

    if(!$result)
      return false;

    return $result;
  }

  /**
  * Function: download()
  * Parameters: $module = Record Type
  *   $record = The record  we are working with
  *   $field = Field associated to the file
  * Description:  Gets the contents of a single file related to a field for a module record.
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  function download($module, $record, $field, $destination)
  {
    $file = $this->getUrl() . $module . '/' . $record . '/file/' . $field;
    $request = $this->client->get($module . '/' . $record . '/file/' . $field );
    $request->setResponseBody($destination);
    $result = $request->send();

    if(!$result)
      return false;

    return $result;
  }

  /**
  * Function: upload()
  * Parameters: $module = Record Type
  *   $record = The record  we are working with
  *   $params = [
  *     format - sugar-html-json (Required),
  *     delete_if_fails - Boolean indicating whether the API is to mark related record deleted if the file upload fails.  Optional (if used oauth_token is also required)
  *     oauth_token - oauth_token_value Optional (Required if delete_if_fails is true)
  *   ]
  * Description:  Saves a file. The file can be a new file or a file override.
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  function upload($module, $record, $field, $path, $params=array())
  {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $contentType = finfo_file($finfo, $path);
    finfo_close($finfo);

    $request = $this->client->post($module . '/' . $record . '/file/' . $field, array(), $params);
    $request->addPostFile(basename($path), dirname($path), $contentType)
                  ->send();

    if(!$result)
      return false;

    return $result;
  }

  /**
  * Function: deleteFile()
  * Parameters: $module = Record Type
  *   $record = The record  we are working with
  *   $field = Field associated to the file
  * Description:  Saves a file. The file can be a new file or a file override.
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  function deleteFile($module, $record, $field)
  {
    $request = $this->client->delete($module . '/' . $record . '/file/' . $field);
    $result = $request->send()->json();

    if(!$result)
      return false;

    return $result;
  }

  /**
  * Function: related()
  * Parameters: $module = Record Type
  *   $record = The record we are working with
  *   $link = The link for the relationship
  * Description:  This method retrieves a list of records from the specified link
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  function related($module, $record, $link)
  {
    $request = $this->client->get($module . '/' . $record . '/link/' . $link);
    $result = $request->send()->json();

    if(!$result)
      return false;

    return $result;
  }

  /**
  * Function: relate()
  * Parameters: $module = Record Type
  *   $record = The record we are working with
  *   $link = The link for the relationship
  *   $related_record = the record to relate to
  *   $fields = Relationship data
  * Description:  This method relates 2 records
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  function relate($module, $record, $link, $related_record, $fields=array())
  {
    $request = $this->client->post($module . '/' . $record . '/link/' . $link . '/' . $related_record, array(), $fields);
    $result = $request->send()->json();

    if(!$result)
      return false;

    return $result;
  }

  /**
  * Function: unrelate()
  * Parameters: $module = Record Type
  *   $record = The record to unfavorite
  * Description:  This method removes the relationship for 2 records
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  function unrelate($module, $record, $link, $related_record)
  {
    $request = $this->client->delete($module . '/' . $record . '/link/' . $link . '/' . $related_record);
    $result = $request->send()->json();

    if(!$result)
      return false;

    return $result;
  }

  /**
  * Function: updateRelationship()
  * Parameters: $module = Record Type
  *   $record = The record we are working with
  *   $link = The link for the relationship
  *   $related_record = the record to relate to
  *   $fields = Relationship data
  * Description:  This method updates relationship data
  * Returns:  Returns an Array if successful, otherwise FALSE
  */
  function updateRelationship($module, $record, $link, $related_record, $fields=array())
  {
    $request = $this->client->put($module . '/' . $record . '/link/' . $link . '/' . $related_record, array(), json_encode($fields));
    $result = $request->send()->json();

    if(!$result)
      return false;

    return $result;
  }
}
