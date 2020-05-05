<?php

namespace Aerdes\LaravelSamlite\Tests;

use Aerdes\LaravelSamlite\SamlServiceProvider;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Orchestra\Testbench\TestCase;
use phpseclib\File\X509;
use ReflectionClass;

class SamlSetupCommandTest extends TestCase
{
    protected $class = 'Aerdes\\LaravelSamlite\\Console\\Commands\\SamlSetupCommand';
    protected $env = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'.env';

    protected function getPackageProviders($app)
    {
        putenv('SAML_SP_PRIVATE_KEY=ABC');
        putenv('SAML_SP_CERT=123');

        return [SamlServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        fopen($this->env, 'w');
        $app->useEnvironmentPath(substr($this->env, 0, -4));
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        parent::getEnvironmentSetUp($app);
    }

    protected function executeMethodWithoutArgs($name)
    {
        $class = new ReflectionClass($this->class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        $obj = new $this->class();

        return $method->invokeArgs($obj, []);
    }

    public function testGenerateKey()
    {
        $this->withoutExceptionHandling();
        [$key, $cert] = $this->executeMethodWithoutArgs('generateKeyAndCert');
        $x509 = new X509();
        $cert = $x509->loadX509($cert);
        $this->assertArrayHasKey('tbsCertificate', $cert);
        $this->assertTrue($cert['tbsCertificate']['issuer']['rdnSequence'][0][0]['value']['utf8String'] === 'Laravel');
    }

    public function testReplacementPatterns()
    {
        $key_pattern = $this->executeMethodWithoutArgs('keyReplacementPattern');
        $this->assertEquals("/^SAML_SP_PRIVATE_KEY\=ABC/m", $key_pattern);
        $cert_pattern = $this->executeMethodWithoutArgs('certReplacementPattern');
        $this->assertEquals("/^SAML_SP_CERT\=123/m", $cert_pattern);
    }

    public function testEntireCommand()
    {
        // Test initial write
        $this->artisan('saml:setup');
        $content_first = file_get_contents($this->env);
        $this->assertStringContainsString('SAML_SP_PRIVATE_KEY=', $content_first);
        $this->assertStringContainsString('SAML_SP_CERT=', $content_first);
        // Test update
        $this->artisan('saml:setup');
        $content_seconds = file_get_contents($this->env);
        $this->assertStringContainsString('SAML_SP_PRIVATE_KEY=', $content_seconds);
        $this->assertStringContainsString('SAML_SP_CERT=', $content_seconds);
        $this->assertTrue($content_first !== $content_seconds);
    }
}
