<?php

return [

    'controller' => env('SAML_CONTROLLER', 'Aerdes\LaravelSamlite\Http\Controllers\SamlControllerExample'),

    'idps' => [
        'test' => [
            'entityId' => 'http://localhost:8000/simplesaml/saml2/idp/metadata.php',
            'singleSignOnService' => [
                'url' => 'http://localhost:8000/simplesaml/saml2/idp/SSOService.php',
            ],
            'singleLogoutService' => [
                'url' => 'http://localhost:8000/simplesaml/saml2/idp/SingleLogoutService.php',
            ],
            'x509cert' => 'MIID/TCCAuWgAwIBAgIJAI4R3WyjjmB1MA0GCSqGSIb3DQEBCwUAMIGUMQswCQYDVQQGEwJBUjEVMBMGA1UECAwMQnVlbm9zIEFpcmVzMRUwEwYDVQQHDAxCdWVub3MgQWlyZXMxDDAKBgNVBAoMA1NJVTERMA8GA1UECwwIU2lzdGVtYXMxFDASBgNVBAMMC09yZy5TaXUuQ29tMSAwHgYJKoZIhvcNAQkBFhFhZG1pbmlAc2l1LmVkdS5hcjAeFw0xNDEyMDExNDM2MjVaFw0yNDExMzAxNDM2MjVaMIGUMQswCQYDVQQGEwJBUjEVMBMGA1UECAwMQnVlbm9zIEFpcmVzMRUwEwYDVQQHDAxCdWVub3MgQWlyZXMxDDAKBgNVBAoMA1NJVTERMA8GA1UECwwIU2lzdGVtYXMxFDASBgNVBAMMC09yZy5TaXUuQ29tMSAwHgYJKoZIhvcNAQkBFhFhZG1pbmlAc2l1LmVkdS5hcjCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAMbzW/EpEv+qqZzfT1Buwjg9nnNNVrxkCfuR9fQiQw2tSouS5X37W5h7RmchRt54wsm046PDKtbSz1NpZT2GkmHN37yALW2lY7MyVUC7itv9vDAUsFr0EfKIdCKgxCKjrzkZ5ImbNvjxf7eA77PPGJnQ/UwXY7W+cvLkirp0K5uWpDk+nac5W0JXOCFR1BpPUJRbz2jFIEHyChRt7nsJZH6ejzNqK9lABEC76htNy1Ll/D3tUoPaqo8VlKW3N3MZE0DB9O7g65DmZIIlFqkaMH3ALd8adodJtOvqfDU/A6SxuwMfwDYPjoucykGDu1etRZ7dF2gd+W+1Pn7yizPT1q8CAwEAAaNQME4wHQYDVR0OBBYEFPsn8tUHN8XXf23ig5Qro3beP8BuMB8GA1UdIwQYMBaAFPsn8tUHN8XXf23ig5Qro3beP8BuMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQELBQADggEBAGu60odWFiK+DkQekozGnlpNBQz5lQ/bwmOWdktnQj6HYXu43e7sh9oZWArLYHEOyMUekKQAxOK51vbTHzzw66BZU91/nqvaOBfkJyZKGfluHbD0/hfOl/D5kONqI9kyTu4wkLQcYGyuIi75CJs15uA03FSuULQdY/Liv+czS/XYDyvtSLnu43VuAQWN321PQNhuGueIaLJANb2C5qq5ilTBUw6PxY9Z+vtMjAjTJGKEkE/tQs7CvzLPKXX3KTD9lIILmX5yUC3dLgjVKi1KGDqNApYGOMtjr5eoxPQrqDBmyx3flcy0dQTdLXud3UjWVW3N0PYgJtw5yBsS74QTGD4=',
        ],
        'azure' => [
            'entityId' => env('SAML_IDP_AZURE_AD_IDENTIFIER'),
            'singleSignOnService' => [
                'url' => env('SAML_IDP_AZURE_LOGIN_URL'),
            ],
            'singleLogoutService' => [
                'url' => env('SAML_IDP_AZURE_LOGOUT_URL'),
            ],
            'x509cert' => env('SAML_IDP_AZURE_CERT'),
        ],
    ],

    'contactPerson' => [
        'technical' => [
            'givenName' => env('SAML_ADMIN_NAME', 'Admin'),
            'emailAddress' => env('SAML_ADMIN_MAIL', 'admin@example.org'),
        ],
        'support' => [
            'givenName' => env('SAML_SUPPORT_NAME', 'Support'),
            'emailAddress' => env('SAML_SUPPORT_MAIL', 'support@example.org'),
        ],
    ],

    'sp' => [
        'privateKey' => env('SAML_SP_PRIVATE_KEY'),
        'x509cert' => env('SAML_SP_CERT'),
    ],

    'routes_prefix' => env('SAML_ROUTES_PREFIX', 'saml'),

    // If 'proxy_vars' is True, then the Saml lib will trust proxy headers
    // e.g X-Forwarded-Proto / HTTP_X_FORWARDED_PROTO. This is useful if
    // your application is running behind a load balancer which terminates
    // SSL.
    'proxy_vars' => env('SAML_PROXY_VARS', false),

];
