<?php
namespace KbizeCli;
use KbizeCli\Cache\Cache;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->cache = $this->getMock('KbizeCli\Cache\Cache');
    }

    public function testUserIsInitializedWithDataFromCache()
    {
        $data = [
            'email' => 'name@company.com',
            'username' => 'name.surname',
            'realname' => 'Name Surname',
            'companyname' => 'Company',
            'timezone' => '0:0',
        ];

        $this->cacheReadReturns($data);

        $user = User::fromCache($this->cache);

        $this->assertEquals($data, $user->toArray());
    }

    public function testUserIsInitializedWithEmptyDataFromEmptyCache()
    {
        $this->cacheReadReturns([]);

        $user = User::fromCache($this->cache);

        $this->assertEquals([], $user->toArray());
    }

    public function testUserWithoutApikeyIsNotAuthenticated()
    {
        $data = [
            'email' => 'name@company.com',
            'username' => 'name.surname',
            'realname' => 'Name Surname',
            'companyname' => 'Company',
            'timezone' => '0:0',
        ];

        $this->cacheReadReturns($data);

        $user = User::fromCache($this->cache);

        $this->assertFalse($user->isAuthenticated());
    }

    public function testUserWithApikeyIsAuthenticated()
    {
        $this->cacheReadReturns([]);
        $user = User::fromCache($this->cache);
        $user = $user->update([
            'email' => 'name@company.com',
            'username' => 'name.surname',
            'realname' => 'Name Surname',
            'companyname' => 'Company',
            'timezone' => '0:0',
            'apikey' => 'UIcXtWGF1ldjKmSdYi6lP3WN8o1K4hMJQnOLkfTv',
        ]);

        $this->assertTrue($user->isAuthenticated());
    }

    public function testStoreMethodWriteRightOnCacheObject()
    {
        $data = [
            'email' => 'name@company.com',
            'username' => 'name.surname',
            'realname' => 'Name Surname',
            'companyname' => 'Company',
            'timezone' => '0:0',
            'apikey' => 'UIcXtWGF1ldjKmSdYi6lP3WN8o1K4hMJQnOLkfTv',
        ];

        $this->cacheReadReturns([]);
        $user = User::fromCache($this->cache);
        $user = $user->update($data);

        $this->cache->expects($this->once())
            ->method('write')
            ->with($data)
            ->will($this->returnValue($this));

        $user->store();
    }

    private function cacheReadReturns(array $data = [])
    {
        $this->cache->expects($this->any())
            ->method('read')
            ->will($this->returnValue($data));
    }
}
