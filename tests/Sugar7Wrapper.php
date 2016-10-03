<?php
namespace Spinegar\Sugar7Wrapper\Tests;

use Spinegar\Sugar7Wrapper\Rest;

class Sugar7WrapperTest extends \PHPUnit_Framework_TestCase
{
    function __construct()
    {
        $this->client = new Rest();
        $this->client->setUrl('https://sugar/rest/v10/')
            ->setUsername('username')
            ->setPassword('password')
            ->connect();
    }

    public function testGetCases()
    {
        $res = $this->client->search('Cases');
        
        $this->assertTrue(is_array($res));
        $this->assertArrayHasKey('records', $res);
        $this->assertTrue(is_array($res['records']));
        
        $record = $res['records'][0];
        
        $this->assertArrayHasKey('my_favorite', $record);
        $this->assertArrayHasKey('following', $record);
        $this->assertArrayHasKey('id', $record);
        $this->assertArrayHasKey('name', $record);
        $this->assertArrayHasKey('date_entered', $record);
        $this->assertArrayHasKey('date_modified', $record);
        $this->assertArrayHasKey('modified_user_id', $record);
        $this->assertArrayHasKey('modified_by_name', $record);
        $this->assertArrayHasKey('created_by', $record);
        $this->assertArrayHasKey('created_by_name', $record);
        $this->assertArrayHasKey('doc_owner', $record);
        $this->assertArrayHasKey('user_favorites', $record);
        $this->assertArrayHasKey('description', $record);
        $this->assertArrayHasKey('deleted', $record);
        $this->assertArrayHasKey('assigned_user_id', $record);
        $this->assertArrayHasKey('assigned_user_name', $record);
        $this->assertArrayHasKey('team_count', $record);
        $this->assertArrayHasKey('team_name', $record);
    }

    public function testGetCasesNameFieldOnly()
    {
        $res = $this->client->search('Cases', array(
            'fields' => 'name'
        ));
        
        $this->assertTrue(is_array($res));
        $this->assertArrayHasKey('records', $res);
        $this->assertTrue(is_array($res['records']));

        $record = $res['records'][0];

        $this->assertTrue(array_key_exists('id', $record));
        $this->assertTrue(array_key_exists('name', $record));
        $this->assertTrue(array_key_exists('date_modified', $record));
        $this->assertTrue(array_key_exists('date_modified', $record));
        
        $this->assertFalse(array_key_exists('my_favorite', $record));
        $this->assertFalse(array_key_exists('following', $record));
        $this->assertFalse(array_key_exists('date_entered', $record));
        $this->assertFalse(array_key_exists('modified_user_id', $record));
        $this->assertFalse(array_key_exists('modified_by_name', $record));
        $this->assertFalse(array_key_exists('created_by', $record));
        $this->assertFalse(array_key_exists('created_by_name', $record));
        $this->assertFalse(array_key_exists('doc_owner', $record));
        $this->assertFalse(array_key_exists('user_favorites', $record));
        $this->assertFalse(array_key_exists('description', $record));
        $this->assertFalse(array_key_exists('deleted', $record));
        $this->assertFalse(array_key_exists('assigned_user_id', $record));
        $this->assertFalse(array_key_exists('assigned_user_name', $record));
        $this->assertFalse(array_key_exists('team_count', $record));
        $this->assertFalse(array_key_exists('team_name', $record));
    }
}