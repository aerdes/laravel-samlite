<?php

namespace Aerdes\LaravelSamlite\Tests;

use Aerdes\LaravelSamlite\SamlAuth;
use Aerdes\LaravelSamlite\SamlServiceProvider;
use Orchestra\Testbench\TestCase;

class SamlAuthTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [SamlServiceProvider::class];
    }

    public function testLoadConfig()
    {
        $config = SamlAuth::loadConfig();
        $this->assertIsArray($config);
        $this->assertArrayHasKey('sp', $config);
        $this->assertArrayHasKey('idp', $config);
        $this->assertArrayHasKey('contactPerson', $config);
    }

    public function testSeedConfig()
    {
        $config = [
            'sp' => [],
            'idp' => [],
            'contactPerson' => [],
        ];
        $config = SamlAuth::seedConfig($config, 'test');
        $this->assertIsArray($config);
        $this->assertEquals(config('saml.idps.test'), $config['idp']);
    }

    public function testConstruct()
    {
        $auth = new SamlAuth('test');
        $this->assertInstanceOf(SamlAuth::class, $auth);
    }

    public function testConstructError()
    {
        $this->expectException(\InvalidArgumentException::class);
        new SamlAuth('IsThisTestAnOverkill?');
    }
}
