<?php

namespace Aerdes\LaravelSamlite;

use InvalidArgumentException;
use OneLogin\Saml2\Auth as OneLogin_Saml2_Auth;

class SamlAuth extends OneLogin_Saml2_Auth
{

    public $idp, $config;

    function __construct(string $idp)
    {
        // Check if IDP is defined
        if (empty($idp)) {
            throw new InvalidArgumentException('The IDP name is required.');
        }
        // Check if IDP is setup in the config
        if(!array_key_exists($idp, config('saml.idps'))) {
            throw new InvalidArgumentException(
                sprintf("The IDP %s is not defined in the configuration.", $idp)
            );
        }
        // Check that IDP test is not used in production
        if($idp=='test' && config('app.environment')=='production') {
            throw new InvalidArgumentException('The IDP test should not be used in a production environment.');
        }

        // Load example config
        $config = $this->loadConfig();

        // Replace entries in example config (with auto-generated data and config data)
        $config = $this->seedConfig($config, $idp);

        // Initialize
        $this->idp = $idp;
        parent::__construct($config);
    }

    public static function loadConfig()
    {
        $config_example = include(__DIR__ . '/../config/onelogin_example.php');
        $config_advanced_example = include(__DIR__ . '/../config/onelogin_advanced_example.php');
        return array_merge($config_example, $config_advanced_example);
    }

    public static function seedConfig($config, $idp) {
        $config['debug'] = config('app.debug');
        $config['sp']['entityId'] = route('saml.metadata', $idp);
        $config['sp']['assertionConsumerService']['url'] = route('saml.acs', $idp);
        $config['sp']['singleLogoutService']['url'] = route('saml.sls', $idp);
        $config['sp'] = config('saml.sp') + $config['sp'];
        $config['idp'] = config(sprintf("saml.idps.%s", $idp)) + $config['idp'];
        $config['contactPerson'] = config('saml.contactPerson') + $config['contactPerson'];
        $config['organization'] = [
            'en-US' => [
                'name' => config('app.name'),
                'displayname' => config('app.name'),
                'url' => config('app.url')
            ]
        ];
        return $config;
    }

    public function processResponse($requestId = null)
    {
        // Make sure that processResponse does not output anything
        ob_start();
        parent::processResponse($requestId);
        ob_clean();
    }

}
