<?php

namespace Aerdes\LaravelSamlite\Http\Controllers;

use Aerdes\LaravelSamlite\SamlAuth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OneLogin\Saml2\Error as OneLogin_Saml2_Error;

class SamlController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Generate local SAML metadata.
     *
     * @param SamlAuth $saml_auth
     * @return \Illuminate\Http\Response
     * @throws OneLogin_Saml2_Error
     */
    public function metadata(SamlAuth $saml_auth)
    {
        $settings = $saml_auth->getSettings();
        $metadata = $settings->getSPMetadata();
        $errors = $settings->validateMetadata($metadata);

        if (! empty($errors)) {
            throw new \InvalidArgumentException(
                'Invalid SP metadata: '.implode(', ', $errors),
                OneLogin_Saml2_Error::METADATA_SP_INVALID
            );
        }

        return response($metadata, 200, ['Content-Type' => 'text/xml']);
    }

    /**
     * Initiate a SAML login request.
     *
     * @param SamlAuth $saml_auth
     * @return \Illuminate\Http\RedirectResponse
     * @throws OneLogin_Saml2_Error
     */
    public function login(SamlAuth $saml_auth)
    {
        $url = $saml_auth->login(null, [], false, false, true);

        return redirect($url);
    }

    /**
     * Process an incoming SAML assertion request.
     *
     * @param SamlAuth $saml_auth
     * @return \Illuminate\Http\RedirectResponse
     * @throws OneLogin_Saml2_Error
     * @throws \OneLogin\Saml2\ValidationError
     */
    public function acs(SamlAuth $saml_auth)
    {
        // Process response
        $saml_auth->processResponse();

        // Check if authenticated
        if (! empty($saml_auth->getErrors()) or ! $saml_auth->isAuthenticated()) {
            abort(403, sprintf('Something went wrong. Go to %s to try again.',
                    route('saml.login', $saml_auth->idp)
            ));
        }

        // Example login flow
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
        // Auth::loginUsingId($user->id);

        // Other examples
        // $saml_auth->getAttributes();
        // $saml_auth->getAttribute($name);
        // $saml_auth->getAttributesWithFriendlyName()
        // $saml_auth->$getNameId();
        // $saml_auth->getSessionIndex();

        // Redirect
        $relays_state = app('request')->input('RelayState');
        $url = app('Illuminate\Contracts\Routing\UrlGenerator');
        if ($relays_state && $url->full() != $relays_state) {
            return redirect($relays_state);
        }

        return redirect()->intended();
    }

    /**
     * Process an incoming SAML logout request (the user logged out of the SSO infrastructure).
     *
     * @param SamlAuth $saml_auth
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sls(SamlAuth $saml_auth, Request $request)
    {
        // Process the request
        $saml_auth->processSLO();

        // Check for errors
        if (! empty($saml_auth->getErrors())) {
            abort(403, sprintf('Something went wrong.Go to %s to try again.',
                route('saml.sls', $saml_auth->idp)
            ));
        }

        // Clear the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect
        return redirect('/');
    }

    /**
     * Initiate a SAML logout request (log out the user across all the SSO infrastructure).
     *
     * @param SamlAuth $saml_auth
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws OneLogin_Saml2_Error
     */
    public function logout(SamlAuth $saml_auth, Request $request)
    {
        $returnTo = $request->query('returnTo');
        $sessionIndex = $request->query('sessionIndex');
        $nameId = $request->query('nameId');
        $url = $saml_auth->logout($returnTo, [], $nameId, $sessionIndex, true);

        return redirect($url);
    }
}
