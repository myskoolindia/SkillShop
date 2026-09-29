<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\EmailModel;
use PHPMailer\PHPMailer\Exception;

class AuthController extends BaseController
{

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    /**
     * Login Post
     */
    public function loginPost()
    {
        $response = ['result' => 0];

        //user already authenticated
        if (authCheck()) {
            $response['result'] = 1;
            $user = user();
            if ($user && isVendor($user)) {
                $response['redirect'] = dashboardUrl();
            }
            return jsonResponse($response);
        }

        $val = \Config\Services::validation();
        $val->setRule('email', trans("email_address"), 'required|valid_email|max_length[255]');
        $val->setRule('password', trans("password"), 'required|max_length[255]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return jsonResponse([
                'result' => 0,
                'response' => view('partials/_messages')
            ]);
        }

        if ($this->authModel->login()) {
            $response['result'] = 1;
            $user = user();
            if ($user && isVendor($user)) {
                $response['redirect'] = dashboardUrl();
            }
        } else {
            $response['response'] = view('partials/_messages');
        }

        resetFlashData();
        return jsonResponse($response);
    }

    /**
     * Admin Login
     */
    public function adminLogin()
    {
        if (authCheck()) {
            $user = user();
            if ($user && hasPermission('admin_panel', $user)) {
                return redirect()->to(adminUrl());
            } elseif ($user && isVendor($user)) {
                return redirect()->to(dashboardUrl());
            }
            return redirect()->to(langBaseUrl());
        }
        $data = setPageMeta(trans("login"));

        $data['generalSettings'] = $this->generalSettings;
        $data['baseSettings'] = $this->settings;

        echo view('admin/login', $data);
    }

    /**
     * Admin Login Post
     */
    public function adminLoginPost()
    {
        $val = \Config\Services::validation();
        $val->setRule('email', trans("form_email"), 'required|valid_email|max_length[255]');
        $val->setRule('password', trans("form_password"), 'required|max_length[255]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $authModel = new AuthModel();
            $user = $authModel->getUserByEmail(inputPost('email'));
            if (!empty($user) && !hasPermission('admin_panel', $user) && $this->generalSettings->maintenance_mode_status == 1) {
                $this->session->setFlashdata('error', "Site under construction! Please try again later.");
                return redirect()->to(adminUrl('login'));
            }
            if ($authModel->login()) {
                $loggedUser = user();
                if ($loggedUser && hasPermission('admin_panel', $loggedUser)) {
                    return redirect()->to(adminUrl());
                } elseif ($loggedUser && isVendor($loggedUser)) {
                    return redirect()->to(dashboardUrl());
                }
                return redirect()->to(langBaseUrl());
            } else {
                return redirect()->to(adminUrl('login'));
            }
        }
    }

    /**
     * Connect with Facebook
     */
    public function connectWithFacebook()
    {
        $state = generateToken();
        $fbUrl = "https://www.facebook.com/v2.10/dialog/oauth?client_id=" . $this->generalSettings->facebook_app_id . "&redirect_uri=" . langBaseUrl() . "facebook-callback&scope=email&state=" . $state;
        $this->session->set('oauth2state', $state);
        $this->session->set('fbLoginReferrer', previous_url());
        return redirect()->to($fbUrl);
    }

    /**
     * Facebook Callback
     */
    public function facebookCallback()
    {
        require_once APPPATH . "ThirdParty/facebook/vendor/autoload.php";
        $provider = new \League\OAuth2\Client\Provider\Facebook([
            'clientId' => $this->generalSettings->facebook_app_id,
            'clientSecret' => $this->generalSettings->facebook_app_secret,
            'redirectUri' => langBaseUrl() . 'facebook-callback',
            'graphApiVersion' => 'v2.10',
        ]);
        if (!isset($_GET['code'])) {
            echo 'Error: Invalid Login';
            exit();
            // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $this->session->get('oauth2state'))) {
            $this->session->remove('oauth2state');
            echo 'Error: Invalid State';
            exit();
        }
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
        try {
            $user = $provider->getResourceOwner($token);
            $fbUser = new \stdClass();
            $fbUser->id = $user->getId();
            $fbUser->email = $user->getEmail();
            $fbUser->name = $user->getName();
            $fbUser->firstName = $user->getFirstName();
            $fbUser->lastName = $user->getLastName();
            $fbUser->pictureURL = $user->getPictureUrl();
            $model = new AuthModel();
            $model->loginWithSocialProvider('facebook', $fbUser);
            if (!empty($this->session->get('fbLoginReferrer'))) {
                return redirect()->to($this->session->get('fbLoginReferrer'));
            } else {
                return redirect()->to(langBaseUrl());
            }
        } catch (\Exception $e) {
            echo 'Error: Invalid User';
            exit();
        }
    }

    /**
     * Connect with Google
     */
    public function connectWithGoogle()
    {
        require_once APPPATH . 'ThirdParty/google/vendor/autoload.php';
        $provider = new \League\OAuth2\Client\Provider\Google([
            'clientId' => $this->generalSettings->google_client_id,
            'clientSecret' => $this->generalSettings->google_client_secret,
            'redirectUri' => base_url('connect-with-google'),
        ]);

        if (!empty($_GET['error'])) {
            return redirect()->to(langBaseUrl());
        } elseif (empty($_GET['code'])) {
            $authUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            $this->session->set('gLoginReferrer', previous_url());
            return redirect()->to($authUrl);
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        } else {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);
            try {
                $user = $provider->getResourceOwner($token);
                $gUser = new \stdClass();
                $gUser->id = $user->getId();
                $gUser->email = $user->getEmail();
                $gUser->name = $user->getName();
                $gUser->firstName = $user->getFirstName();
                $gUser->lastName = $user->getLastName();
                $gUser->avatar = $user->getAvatar();

                $model = new AuthModel();
                $model->loginWithSocialProvider('google', $gUser);
                if (!empty($this->session->get('gLoginReferrer'))) {
                    return redirect()->to($this->session->get('gLoginReferrer'));
                } else {
                    return redirect()->to(langBaseUrl());
                }
            } catch (Exception $e) {
                exit('Something went wrong: ' . $e->getMessage());
            }
        }
    }

    /**
     * Connect with VK
     */
    public function connectWithVK()
    {
        require_once APPPATH . "ThirdParty/vkontakte/vendor/autoload.php";
        $provider = new \J4k\OAuth2\Client\Provider\Vkontakte([
            'clientId' => $this->generalSettings->vk_app_id,
            'clientSecret' => $this->generalSettings->vk_secure_key,
            'redirectUri' => base_url('connect-with-vk'),
            'scopes' => ['email'],
        ]);
        // Authorize if needed
        if (PHP_SESSION_NONE === session_status()) session_start();
        $isSessionActive = PHP_SESSION_ACTIVE === session_status();
        $code = !empty($_GET['code']) ? $_GET['code'] : null;
        $state = !empty($_GET['state']) ? $_GET['state'] : null;
        $sessionState = 'oauth2state';
        // No code – get some
        if (!$code) {
            $authUrl = $provider->getAuthorizationUrl();
            if ($isSessionActive) $_SESSION[$sessionState] = $provider->getState();
            $this->session->set('vkLoginReferrer', previous_url());
            return redirect()->to($authUrl);
        } // Anti-CSRF
        elseif ($isSessionActive && (empty($state) || ($state !== $_SESSION[$sessionState]))) {
            unset($_SESSION[$sessionState]);
            throw new \RuntimeException('Invalid state');
        } else {
            try {
                $providerAccessToken = $provider->getAccessToken('authorization_code', ['code' => $code]);
                $user = $providerAccessToken->getValues();
                //get user details with cURL
                $url = 'http://api.vk.com/method/users.get?uids=' . $providerAccessToken->getValues()['user_id'] . '&access_token=' . $providerAccessToken->getToken() . '&v=5.95&fields=photo_200,status';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                $response = curl_exec($ch);
                curl_close($ch);

                $userDetails = json_decode($response);
                $vkUser = new \stdClass();
                $vkUser->id = $providerAccessToken->getValues()['user_id'];
                $vkUser->email = $providerAccessToken->getValues()['email'];
                $vkUser->name = @$userDetails->response['0']->first_name . " " . @$userDetails->response['0']->last_name;
                $vkUser->firstName = @$userDetails->response['0']->first_name;
                $vkUser->lastName = @$userDetails->response['0']->last_name;
                $vkUser->avatar = @$userDetails->response['0']->photo_200;

                $model = new AuthModel();
                $model->loginWithSocialProvider('vkontakte', $vkUser);
                if (!empty($this->session->get('vkLoginReferrer'))) {
                    return redirect()->to($this->session->get('vkLoginReferrer'));
                } else {
                    return redirect()->to(langBaseUrl());
                }
            } catch (IdentityProviderException $e) {
                error_log($e->getMessage());
            }
        }
    }

    /**
     * Register
     */
    public function register()
    {
        if (authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $data = setPageMeta(trans("register"));
        $data['userSession'] = getUserSession();
        $data['isTranslatable'] = true;

        echo view('partials/_header', $data);
        echo view('auth/register');
        echo view('partials/_footer');
    }

    /**
     * Register Post
     */
    public function registerPost()
    {
        //bot verification
        verifyTurnstile();

        if (authCheck()) {
            return redirect()->to(langBaseUrl());
        }

        $val = \Config\Services::validation();
        $val->setRule('email', trans("email_address"), 'required|valid_email|max_length[255]');
        $val->setRule('password', trans("password"), 'required|min_length[4]|max_length[255]');
        $val->setRule('confirm_password', trans("password_confirm"), 'required|matches[password]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to(generateUrl('register'))->withInput();
        } else {
            $email = inputPost('email');
            if (!$this->authModel->isEmailUnique($email)) {
                setErrorMessage(trans("msg_email_unique_error"));
                return redirect()->to(generateUrl('register'))->withInput();
            }
            if ($this->authModel->register()) {
                setSuccessMessage(trans("msg_register_success"));
                return redirect()->to(generateUrl('settings', 'edit_profile'));
            }
        }
        setErrorMessage(trans("msg_error"));
        return redirect()->to(generateUrl('register'));
    }

    /**
     * Register Success
     */
    public function registerSuccess()
    {
        if (authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $data['title'] = trans("register");
        $data['description'] = trans("register") . ' - ' . $this->baseVars->appName;
        $data['keywords'] = trans("register") . ',' . $this->baseVars->appName;
        $token = inputGet('u');
        $data['user'] = $this->authModel->getUserByToken($token);
        if (empty($data['user']) || $data['user']->email_status == 1) {
            return redirect()->to(langBaseUrl());
        }

        echo view('partials/_header', $data);
        echo view('auth/register_success', $data);
        echo view('partials/_footer');
    }

    /**
     * Confirm Account
     */
    public function confirmAccount()
    {
        $data['title'] = trans("confirm_your_account");
        $data['description'] = trans("confirm_your_account") . " - " . $this->baseVars->appName;
        $data['keywords'] = trans("confirm_your_account") . "," . $this->baseVars->appName;

        $token = trim(inputGet('token') ?? '');
        $data['user'] = $this->authModel->getUserByToken($token);
        if (empty($data['user'])) {
            return redirect()->to(langBaseUrl());
        }
        if ($data['user']->email_status == 1) {
            return redirect()->to(langBaseUrl());
        }
        if ($this->authModel->verifyEmail($data['user'])) {
            $data['success'] = trans("msg_confirmed");
        } else {
            $data['error'] = trans("msg_error");
        }

        echo view('partials/_header', $data);
        echo view('auth/confirm_email', $data);
        echo view('partials/_footer');
    }

    /**
     * Forgot Password
     */
    public function forgotPassword()
    {
        if (authCheck()) {
            return redirect()->to(langBaseUrl());
        }

        $data = setPageMeta(trans("forgot_password"));
        $data['isTranslatable'] = true;

        echo view('partials/_header', $data);
        echo view('auth/forgot_password');
        echo view('partials/_footer');
    }

    /**
     * Forgot Password Post
     */
    public function forgotPasswordPost()
    {
        //bot verification
        verifyTurnstile();

        if (authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $email = inputPost('email');
        $user = $this->authModel->getUserByEmail($email);
        if (empty($user)) {
            setErrorMessage(trans("msg_reset_password_error"));
            return redirect()->to(generateUrl('forgot_password'));
        } else {
            $token = $user->token;
            if (empty($token)) {
                $token = generateToken();
                $this->authModel->updateUserToken($user->id, $token);
            }
            $emailData = [
                'email_type' => 'reset_password',
                'email_address' => $user->email,
                'email_data' => serialize([
                    'content' => trans("email_reset_password"),
                    'url' => generateUrl("reset_password") . '?token=' . $token,
                    'buttonText' => trans("reset_password")
                ]),
                'email_priority' => 1,
                'email_subject' => trans("reset_password"),
                'template_path' => 'email/main'
            ];
            addToEmailQueue($emailData);
            setSuccessMessage(trans("msg_reset_password_success"));
            return redirect()->to(generateUrl('forgot_password'));
        }
    }

    /**
     * Reset Password
     */
    public function resetPassword()
    {
        if (authCheck()) {
            return redirect()->to(langBaseUrl());
        }

        $data = setPageMeta(trans("reset_password"));

        $token = inputGet('token');
        $data['user'] = $this->authModel->getUserByToken($token);
        $data['success'] = $this->session->getFlashdata('success');
        if (empty($data['user']) && empty($data['success'])) {
            return redirect()->to(langBaseUrl());
        }

        echo view('partials/_header', $data);
        echo view('auth/reset_password');
        echo view('partials/_footer');
    }

    /**
     * Reset Password Post
     */
    public function resetPasswordPost()
    {
        $success = inputPost('success');
        if ($success == 1) {
            return redirect()->to(langBaseUrl());
        }
        $val = \Config\Services::validation();
        $val->setRule('password', trans("new_password"), 'required|min_length[4]|max_length[255]');
        $val->setRule('password_confirm', trans("password_confirm"), 'required|matches[password]');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back()->withInput();
        } else {
            $token = inputPost('token');
            $user = $this->authModel->getUserByToken($token);
            if (!empty($user)) {
                if ($this->authModel->resetPassword($user)) {
                    setSuccessMessage(trans("msg_change_password_success"));
                    return redirect()->back();
                }
                setErrorMessage(trans("msg_change_password_error"));
                return redirect()->back()->withInput();
            }
        }
    }

    /**
     * Send Activation Email
     */
    public function sendActivationEmailPost()
    {
        $token = inputPost('token');
        $user = $this->authModel->getUserByToken($token);
        if (!empty($user)) {
            $this->authModel->addActivationEmail($user);
        }
        $emailModel = new EmailModel();
        $emailModel->runEmailQueue();
        $data = [
            'result' => 1,
            'successMessage' => '<div class="text-success text-center m-b-15">' . trans("activation_email_sent") . '</div>'
        ];
        return jsonResponse($data);
    }

    /**
     * Join Affiliate Program Post
     */
    public function joinAffiliateProgramPost()
    {
        if (!authCheck()) {
            return redirect()->to(langBaseUrl());
        }
        $this->authModel->joinAffiliateProgram();
        return redirect()->to(generateUrl('affiliate_program'));
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->authModel->logout();
        redirectToBackUrl();
    }

    /**
     * Single Sign-On (SSO) Login from Skillvation Platform
     */
    public function ssoLogin()
    {
        $token = $this->request->getGet('token');
        if (empty($token) || !is_string($token)) {
            $this->session->setFlashdata('error', 'Invalid SSO token.');
            return redirect()->to(langBaseUrl());
        }

        $parts = explode('.', $token, 2);
        if (count($parts) !== 2) {
            $this->session->setFlashdata('error', 'Malformed SSO token format.');
            return redirect()->to(langBaseUrl());
        }

        list($payloadEncoded, $signature) = $parts;

        $secretKey = env('SSO_SECRET_KEY', 'sk_sso_a9f83e2b17c64d85a109ecf3821094ba723e80d91fca475b83017a4c9b2f6e18');
        $expectedSignature = hash_hmac('sha256', $payloadEncoded, $secretKey);

        if (!hash_equals($expectedSignature, $signature)) {
            $this->session->setFlashdata('error', 'Invalid SSO signature.');
            return redirect()->to(langBaseUrl());
        }

        $payloadJson = base64_decode(strtr($payloadEncoded, '-_', '+/'));
        $payload = json_decode($payloadJson, true);

        if (empty($payload) || !is_array($payload)) {
            $this->session->setFlashdata('error', 'Invalid SSO payload data.');
            return redirect()->to(langBaseUrl());
        }

        // Token expiry verification (default 120 seconds TTL)
        $iat = (int)($payload['iat'] ?? 0);
        $ttl = (int)env('SSO_TOKEN_TTL', 120);
        if (time() - $iat > $ttl || $iat > (time() + 60)) {
            $this->session->setFlashdata('error', 'SSO token has expired. Please try again.');
            return redirect()->to(langBaseUrl());
        }

        $user = $this->authModel->loginWithSso($payload);

        if (!$user) {
            return redirect()->to(langBaseUrl());
        }

        // Determine target redirect
        $target = trim($payload['target'] ?? '');
        if (!empty($target)) {
            if (str_starts_with($target, '/')) {
                return redirect()->to(base_url(ltrim($target, '/')));
            }
            return redirect()->to($target);
        }

        // If user is admin in shop and role is admin, redirect to admin panel
        if ((int)$user->role_id === 1 && (!empty($payload['role']) && $payload['role'] === 'admin')) {
            return redirect()->to(adminUrl());
        }

        return redirect()->to(langBaseUrl());
    }

    /**
     * Single Sign-On (SSO) Bridge to Skillvation Main Platform (LMS)
     */
    public function toMain()
    {
        $target = trim($this->request->getGet('target') ?? '');

        if (!authCheck()) {
            $redirectTarget = !empty($target) ? $target : 'dashboard';
            return redirect()->to(mainAppUrl('login?redirect=' . urlencode($redirectTarget)));
        }

        $user = user();
        $isAdmin = ((int)$user->role_id === 1 || hasPermission('admin_panel', $user));
        $role = $isAdmin ? 'admin' : 'student';

        // Check if explicit admin param or admin target
        if ($this->request->getGet('admin') && $isAdmin) {
            $target = !empty($target) ? $target : 'admin/dashboard';
        }

        $nameParts = explode(' ', trim(getUsername($user)), 2);
        $firstName = $user->first_name ?: ($nameParts[0] ?? '');
        $lastName = $user->last_name ?: ($nameParts[1] ?? '');

        $payload = [
            'uid'        => (string) $user->id,
            'email'      => (string) $user->email,
            'first_name' => (string) $firstName,
            'last_name'  => (string) $lastName,
            'phone'      => (string) ($user->phone_number ?? ''),
            'role'       => (string) $role,
            'target'     => $target ?: '',
            'iat'        => time(),
            'nonce'      => bin2hex(random_bytes(8)),
        ];

        $secretKey = env('SSO_SECRET_KEY', 'sk_sso_a9f83e2b17c64d85a109ecf3821094ba723e80d91fca475b83017a4c9b2f6e18');
        $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $payloadEncoded = rtrim(strtr(base64_encode($payloadJson), '+/', '-_'), '=');
        $signature = hash_hmac('sha256', $payloadEncoded, $secretKey);
        $token = $payloadEncoded . '.' . $signature;

        $ssoLoginUrl = mainAppUrl('sso/login') . '?token=' . urlencode($token);

        return redirect()->to($ssoLoginUrl);
    }
}

