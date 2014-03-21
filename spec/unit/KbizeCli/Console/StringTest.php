<?php

use KbizeCli\Console\String;

class StringTest extends \PHPUnit_Framework_TestCase
{
    public function testCutStringIfItIsWiderThanDesiredSize()
    {
        $string = new String('test');
        $this->assertEquals('te', $string->fixed(2));
    }

    public function testAddPadCharsIfStringIsLessWideThanDesiredSize()
    {
        $string = new String('test');
        $this->assertEquals('test      ', $string->fixed(10));
    }

    public function testColorTheStringIfColorIsPassed()
    {
        $string = new String('test');
        $this->assertEquals('<fg=red>test</fg=red>', $string->color('red'));
    }

    public function testReturnOriginalStringIfColorIsNull()
    {
        $string = new String('test');
        $this->assertEquals('test', $string->color(null));
    }
}
