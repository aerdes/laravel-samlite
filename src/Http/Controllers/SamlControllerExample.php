<?php

namespace Aerdes\LaravelSamlite\Http\Controllers;

use Aerdes\LaravelSamlite\SamlAuth;

class SamlControllerExample extends SamlController
{

    public function loginUser(SamlAuth $saml_auth)
    {
        // $mail = $saml_auth->getAttribute('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress')[0];
        // $name = $saml_auth->getAttribute('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/displayname')[0];
        // $user = User::where('email', $mail)->first();
        // if (!$user) {
        //    $user = new User;
        //    $user->name = $name;
        //    $user->email = $mail;
        //    $user->password = md5(rand(1,10000));
        //    $user->save();
        // }
        // $this->guard()->loginUsingId($user->id);

        // $saml_auth->getAttributes();
        // $saml_auth->getAttribute($name);
        // $saml_auth->getAttributesWithFriendlyName()
        // $saml_auth->$getNameId();
        // $saml_auth->getSessionIndex();
    }

}
