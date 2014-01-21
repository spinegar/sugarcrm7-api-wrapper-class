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
  * Variable: $username
  * Description:  A SugarCRM User. 
  */
  private $username;

  /**
  * Variable: $password
  * Description:  The password for the $username SugarCRM account
  */
  private $password;

  /**
  * Variable: $token
  * Description:  OAuth 2.0 token
  */
  private $token;

  /**
  * Variable: $client
  * Description:  Guzzle Client
  */
  private $client;

  /**
  * Function: __construct()
  * Parameters:   none    
  * Description:  Construct Class
  * Returns:  VOID
  */
  function __construct()
  {
    $this->client = new Client();
  }

  /**
  * Function: __destruct()
  * Parameters:   none    
  * Description:  OAuth2 Logout
  * Returns:  TRUE on success, otherwise FALSE
  */
  function __destruct()
  {
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
  public function connect()
  {
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
    $token = $this->token;
    
    $this->client->getEventDispatcher()->addListener('request.before_send', function(Event $event) use ($token) {
      $event['request']->setHeader('OAuth-Token', $token);
    });
    
    return true;
  }

  /**
  * Function: check()
  * Parameters:   none    
  * Description:  Check if authenticated
  * Returns:  TRUE if authenticated, otherwise FALSE
  */
  public function check()
  {
    if(!$this->token)
      return false;

    return true;
  }

  /**
  * Function: setClientOptions()
  * Parameters:   $key = Guzzle option, $value = Value  
  * Description:  Set Default options for the Guzzle client.
  * Returns:  returns $this
  */
  public function setClientOption($key, $value)
  {
    $this->client->setDefaultOption($key, $value);

    return $this;
  }

  /**
  * Function: setUrl()
  * Parameters:   $value = URL for the REST API    
  * Description:  Set $url
  * Returns:  returns $url
  */
  public function setUrl($value)
  {
    $this->client->setBaseUrl($value) ;

    return $this;
  }

  /**
  * Function: setUsername()
  * Parameters:   $value = Username for the REST API User    
  * Description:  Set $username
  * Returns:  returns $username
  */
  public function setUsername($value)
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
  public function setPassword($value)
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
  public function create($module, $fields)
  {
    if(!self::check())
      self::connect();

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
  public function search($module, $params = array())
  {
    if(!self::check())
      self::connect();

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
  public function delete($module, $record)
  {
    if(!self::check())
      self::connect();

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
  public function retrieve($module, $record)
  {
    if(!self::check())
      self::connect();

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
  public function update($module, $record, $fields)
  {
    if(!self::check())
      self::connect();

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
  public function favorite($module, $record)
  {
    if(!self::check())
      self::connect();

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
  public function unfavorite($module, $record)
  {
    if(!self::check())
      self::connect();

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
  public function files($module, $record)
  {
    if(!self::check())
      self::connect();

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
  public function download($module, $record, $field, $destination)
  {
    if(!self::check())
      self::connect();

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
  public function upload($module, $record, $field, $path, $params=array())
  {
    if(!self::check())
      self::connect();

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
  public function deleteFile($module, $record, $field)
  {
    if(!self::check())
      self::connect();

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
  public function related($module, $record, $link)
  {
    if(!self::check())
      self::connect();

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
  public function relate($module, $record, $link, $related_record, $fields=array())
  {
    if(!self::check())
      self::connect();

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
  public function unrelate($module, $record, $link, $related_record)
  {
    if(!self::check())
      self::connect();

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
  public function updateRelationship($module, $record, $link, $related_record, $fields=array())
  {
    if(!self::check())
      self::connect();

    $request = $this->client->put($module . '/' . $record . '/link/' . $link . '/' . $related_record, array(), json_encode($fields));
    $result = $request->send()->json();

    if(!$result)
      return false;

    return $result;
  }
}
