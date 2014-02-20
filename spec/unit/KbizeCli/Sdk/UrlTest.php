<?php
namespace KbizeCli\Sdk;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleCreation()
    {
        $url = Url::fromPath('login');
        $this->assertEquals('login', $url->__toString());
    }

    public function testAddParameters()
    {
        $url = Url::fromPath('login')->withParams([
            'email' => 'from@email.com',
            'password' => 'secret',
        ]);

        $this->assertEquals('login/email/from%40email.com/password/secret', $url->__toString());
    }
}
