<?php

namespace Aerdes\LaravelSamlite\Tests;

use Aerdes\LaravelSamlite\SamlServiceProvider;
use Orchestra\Testbench\BrowserKit\TestCase;

class SamlControllerTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        putenv('SAML_SP_PRIVATE_KEY=MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDJaNUq2677ihFW6MX5S4tdzsl8ikmHd73TQNYRWw2LtNWndwCzmqh6LvrW09kzL5rcvgBft8GEn7Md+ehdsNbaa+OeWEUTvksQDLM4PBci7gqz6ivW3tu3fBullcXMwE9D+mFeFQgLLrRwMV9VaBmbosP7vkYCJ059pONSzpPJeMKogsvekGnq2Mh0CAgYH1HAPmW7+X/jxOhFaV6lVakxua7PbJ8yp07hZlOUNo0kYJQpvJQ1BWZlvsmJ0M2IiUSldsB5CZUL9i7jddzCxLQbWXqzRR4gaKGSCGLk8vm8PIhIMXmwnRrTbVN0jQxzjt1qYFl5POAIMaAgJUtLwSIVAgMBAAECggEAG3AMmioTTHEiq5RKZAvGkKD7EdxE6A+H7J+IihS9Y6re8FFl5xHKodqEkX+Kt05k6m1335JI6qhW3l6NKTZODrNj7s5XmrjxwN36DE8jV77V9myQTFVP2U8u9P/SUnJgWgJiAU8cKWnTavVO53OnCWzDBiiFoQV5y+QKk91QhMGQ5H+lLpN5aOsF4HyPipbIxzkUMrUiK8B1aW/2eiJHDLyfvJMjH587bWKLgiJEYEv/g2qKIhDsM28tHVSTJls4V+PaQ32ndCdK7Hzuv9/7COHcM8fCYM3nuIvogNQkcW+wyDfExe9c205QdATDbsPY7bobKOC6bwbJvMgV0eK4AQKBgQDk4o+eGI9YOlwX8B8ye+Cc2ZKElbniLZYZdSJQ+g4k2QsNRP4O1nE4C5bxHDkiNmUfZ/bO8hOiK7Cs7qOooqjqQwXdw25VOXtJMk6lyPgW7aJEdg/siwrVibJBgIXF4D/UeaLgCPaEPcEUdBnV2JgpNm9EK7UcgXlcdkIseba6xQKBgQDhRQRVvQoEB4dGqmCTVz8Roc0LBPLcFJ3MsdgH38nG5eGoKKEJ269ZdJtJj3OYu/bfq3Z4vgohsXiIhRs85mHERhsIHnC6Q9XDLMyDOZs2KEyg+THOPbsk2eU9eLWJ80Q1akwpxzAkgD+i8v7tjtOdrW6I1IerN8Y8OIQT+4t/EQKBgQCmA4ql4ix+kv3fQwRtypo1Sj5SuoL0AZqgZmx4jZaatW6ltkgMHRBL1WQrCAyMuyWHrRHAMqd49fWUyRadpzWbjPeBTVIGsMWyZrHS37zKbJxIydVs/cDi95f6mKzuxGOnyn4Pv5CGhIA6RKfEivB63AfCS212+pY7MW938ORP/QKBgQDFFzhat2Fa8ydFCW4jm1Lf31thR7wgF52UaQkkooSMk7ZiBOIYmO+K6b2vl2XA/LtbKE4oB8Ufg4F8mFCjcMGbEuc1rEReg1k0QS7RpQYKVSnuMPiFhHcHH+k0ZjcW8hL8VPs8Fj0lwltq+wVV3P+C1il+Z8wnXk3/hEbyoMOEsQKBgDbC8aqegacdMXU+epjNZGSvrnkKlVhoBijlHxuyBkdG8UYdJ+q+7d0RyYdCX9+mZk4+x4VZC8EL1RIVh4GRKnVNG45wEYCyBnn1K5hx4KWvEbIHv7VeVeb9Rz5uWFtz980PQ7ziYtkA3exNfZzpsREbeeHLiMUUwKVx+fu6FRoG');
        putenv('SAML_SP_CERT=MIIDbDCCAlSgAwIBAgIBADANBgkqhkiG9w0BAQsFADBPMRAwDgYDVQQKDAdMYXJhdmVsMRkwFwYDVQQDDBBodHRwOi8vbG9jYWxob3N0MQswCQYDVQQGEwJBVTETMBEGA1UECAwKU29tZS1TdGF0ZTAeFw0yMDA1MDIyMDA4MTNaFw0zMDA0MzAyMDA4MTNaME8xEDAOBgNVBAoMB0xhcmF2ZWwxGTAXBgNVBAMMEGh0dHA6Ly9sb2NhbGhvc3QxCzAJBgNVBAYTAkFVMRMwEQYDVQQIDApTb21lLVN0YXRlMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyWjVKtuu+4oRVujF+UuLXc7JfIpJh3e900DWEVsNi7TVp3cAs5qoei761tPZMy+a3L4AX7fBhJ+zHfnoXbDW2mvjnlhFE75LEAyzODwXIu4Ks+or1t7bt3wbpZXFzMBPQ/phXhUICy60cDFfVWgZm6LD+75GAidOfaTjUs6TyXjCqILL3pBp6tjIdAgIGB9RwD5lu/l/48ToRWlepVWpMbmuz2yfMqdO4WZTlDaNJGCUKbyUNQVmZb7JidDNiIlEpXbAeQmVC/Yu43XcwsS0G1l6s0UeIGihkghi5PL5vDyISDF5sJ0a021TdI0Mc47damBZeTzgCDGgICVLS8EiFQIDAQABo1MwUTAdBgNVHQ4EFgQUHbMEbwE+UXcCL3PbW46JGiF5dDgwHwYDVR0jBBgwFoAUHbMEbwE+UXcCL3PbW46JGiF5dDgwDwYDVR0TAQH/BAUwAwEB/zANBgkqhkiG9w0BAQsFAAOCAQEAcD8z5c2SqnfQyOqkdROarnxrRm7381CDgddCG7S2MPp5zVb51CW3eUgtxJE3KKZKQ/maP6Emx/tEiZ2nCpTmTWAa4qVbNNSxuneZ2cVKaO9ZJ37z7A8JK3N3SVUgXFH5H21UvoCWoC4BP1o5l64qCGcNl+U7AN33ny92ppE6TG5/vrbyLLboPXPDSRdhcddDea4Fv3hy6YgYeT0NSDBV9F+v4aFmtmTyAlVdm0pwDMiiVKrXm01RPNjDFb+NmVNE4+2fE/h30ean3WlLkEqwEYSTkBH7Me64hxnUR1E7iUndlYNE4TlPM4t7t0dqctkY3FghF43eFSYVGnEk9Dpvkw==');

        return [SamlServiceProvider::class];
    }

    public function testMetadata()
    {
        $this->visit('saml/test/metadata')->assertResponseOk();
    }

    public function testInvalidMetadata()
    {
        $this->call('GET', 'saml/somethingthatwillneverhappen/metadata')->assertNotFound();
    }

    public function testLogin()
    {
        $res = $this->call('GET', 'saml/test/login')->assertStatus(302);
        $parts = explode('?', $res->getTargetUrl());
        $this->assertTrue($parts[0] === 'http://localhost:8000/simplesaml/saml2/idp/SSOService.php');
        $this->assertTrue(substr($parts[1], 0, 11) === 'SAMLRequest');
    }

    public function testAcs()
    {
        // TODO: Write test
        $this->assertTrue(true);
    }

    public function testSls()
    {
        // TODO: Write test
        $this->assertTrue(true);
    }

    public function testLogout()
    {
        $res = $this->call('POST', 'saml/test/logout')->assertStatus(302);
        $parts = explode('?', $res->getTargetUrl());
        $this->assertTrue($parts[0] === 'http://localhost:8000/simplesaml/saml2/idp/SingleLogoutService.php');
        $this->assertTrue(substr($parts[1], 0, 11) === 'SAMLRequest');
    }
}
