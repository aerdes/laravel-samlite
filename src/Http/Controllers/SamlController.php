<?php

namespace Aerdes\LaravelSamlite\Http\Controllers;

use Aerdes\LaravelSamlite\SamlAuth;
use Exception;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use OneLogin\Saml2\Error as OneLogin_Saml2_Error;
use OneLogin\Saml2\ValidationError;

abstract class SamlController extends Controller
{
    /**
     * Create a new controller instance with the appropriate middleware.
     */
    public function __construct()
    {
        $this->middleware([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
        ]);
    }

    /**
     * Generate local SAML metadata.
     *
     * @param SamlAuth $saml_auth
     * @return Response
     * @throws OneLogin_Saml2_Error
     * @throws Exception
     */
    public function metadata(SamlAuth $saml_auth)
    {
        $settings = $saml_auth->getSettings();
        $metadata = $settings->getSPMetadata();
        $errors = $settings->validateMetadata($metadata);

        if (! empty($errors)) {
            throw new InvalidArgumentException(
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
     * @return RedirectResponse
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
     * @return RedirectResponse
     * @throws OneLogin_Saml2_Error
     * @throws ValidationError
     */
    public function acs(SamlAuth $saml_auth)
    {
        $this->validateLogin($saml_auth);

        $this->loginUser($saml_auth);

        return redirect($this->redirectPath());
    }

    /**
     * Process an incoming SAML logout request (the user logged out of the SSO infrastructure).
     *
     * @param SamlAuth $saml_auth
     * @return Response
     * @throws Exception
     */
    public function sls(SamlAuth $saml_auth, Request $request)
    {
        $this->validateLogout($saml_auth);

        $this->logoutUser($request);

        return response('Successfully logged out the user.', 200);
    }

    /**
     * Initiate a SAML logout request (log out the user across all the SSO infrastructure).
     *
     * @param SamlAuth $saml_auth
     * @param Request $request
     * @return RedirectResponse
     * @throws OneLogin_Saml2_Error
     */
    public function logout(SamlAuth $saml_auth, Request $request)
    {
        $this->logoutUser($request);

        // Initiate the request
        $returnTo = $request->query('returnTo');
        $sessionIndex = $request->query('sessionIndex');
        $nameId = $request->query('nameId');
        $url = $saml_auth->logout($returnTo, [], $nameId, $sessionIndex, true);

        // Redirect
        return redirect($url);
    }

    /**
     * Validate the login request.
     *
     * @param SamlAuth $saml_auth
     * @throws OneLogin_Saml2_Error
     * @throws ValidationError
     */
    protected function validateLogin(SamlAuth $saml_auth)
    {
        $saml_auth->processResponse();

        if (! empty($saml_auth->getErrors()) or ! $saml_auth->isAuthenticated()) {
            abort(403, sprintf('Something went wrong. Go to %s to try again.',
                route('saml.login', $saml_auth->idp)
            ));
        }
    }

    /**
     * Validate the logout request.
     *
     * @param SamlAuth $saml_auth
     * @throws OneLogin_Saml2_Error
     * @throws Exception
     */
    protected function validateLogout(SamlAuth $saml_auth)
    {
        $saml_auth->processSLO();

        $errors = $saml_auth->getErrors();
        if (! empty($errors)) {
            throw new Exception('Could not process SLO: '.implode(', ', $errors));
        }
    }

    /**
     * Get the post login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        // First check if redirectTo method exists in this controller
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }
        // Then check if redirectTo attributes exists in this controller
        if (property_exists($this, 'redirectTo')) {
            return $this->redirectTo;
        }
        // Then check if RelayState exists in the request
        $relays_state = app('request')->input('RelayState');
        $url = app('Illuminate\Contracts\Routing\UrlGenerator');
        if ($relays_state && $url->full() != $relays_state) {
            return $relays_state;
        }
        // Send to intended url (this is the default)
        return redirect()->intended()->getTargetUrl();
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * Logout the user and destroy the session.
     *
     * @param Request $request
     * @return void
     */
    public function logoutUser(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    /**
     * Login the user and setup the session.
     * See SamlControllerExample for examples.
     *
     * @param SamlAuth $saml_auth
     * @return void
     */
    abstract public function loginUser(SamlAuth $saml_auth);
}
