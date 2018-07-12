<?php

namespace Visacheck\Visacheck\Tests;


use PHPUnit\Framework\TestCase;
use Visacheck\Visacheck\Manifest;

class ManifestTest extends TestCase
{
    /** @var Manifest */
    protected $manifest;

    public function setUp()
    {
        $this->manifest = new Manifest();
    }

    public function testData()
    {
        $this->assertNotEmpty($this->manifest->data());
    }

    public function testGetResource()
    {
        $this->assertNotEmpty($this->manifest->getResource('User'));
        $this->assertEmpty($this->manifest->getResource('NonExistentResource'));
    }

    public function testGetService()
    {
        $this->assertNotEmpty($this->manifest->getService('Registration'));
        $this->assertEmpty($this->manifest->getService('NonExistentService'));
    }

    public function testReload()
    {
        $oldData = $this->manifest->data();
        $this->manifest->reload();
        $this->assertEquals($oldData, $this->manifest->data());
    }
}