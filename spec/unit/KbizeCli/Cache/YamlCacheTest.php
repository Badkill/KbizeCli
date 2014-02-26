<?php
namespace KbizeCli\Cache;
use org\bovigo\vfs\vfsStream;

class YamlCacheTest extends \PHPUnit_Framework_TestCase
{
    private $root;

    public function setUp()
    {
        $this->basePath = 'root/';
        $this->fileName = 'file.yml';
        $this->configFilePath = 'config/env/' . $this->fileName;

        $this->root = vfsStream::setup($this->basePath);
        $this->file = vfsStream::url($this->basePath . $this->configFilePath);
        $this->parser = $this->getMock('Symfony\Component\Yaml\Parser');
        $this->dumper = $this->getMock('Symfony\Component\Yaml\Dumper');
        $this->cache = new YamlCache($this->parser, $this->dumper);
    }

    public function testWrite()
    {
        $data = [
            'foo' => 'bar',
        ];

        $this->dumper->expects($this->once())
            ->method('dump')
            ->with($data);

        $this->assertFalse($this->root->hasChild($this->configFilePath));
        $this->cache->write($this->file, $data, 2);
        $this->assertTrue($this->root->hasChild($this->configFilePath));
    }

    public function testReadNotExistingFileReturnsEmptyArray()
    {
        $this->assertFalse($this->root->hasChild($this->configFilePath));
        $this->assertEquals([], $this->cache->read($this->file));
    }

    public function testReadWithExistingFile()
    {
        $data = [
            'foo' => 'bar',
        ];

        vfsStream::newDirectory('config/env', 0700)->at($this->root);
        vfsStream::newFile($this->configFilePath, 0600)
            ->at($this->root)
            ->withContent('foo: bar');

        $this->parser->expects($this->once())
            ->method('parse')
            ->with('foo: bar')
            ->will($this->returnValue($data));

        $this->assertEquals($data, $this->cache->read($this->file));
    }
}
