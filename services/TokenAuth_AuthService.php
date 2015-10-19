<?php
namespace Craft;

use Firebase\JWT\JWT;

class TokenAuth_AuthService extends BaseApplicationComponent {

	private $settings;

	/*
	 * Helpers
	 */
	/**
	 * Return JSON
	 *
	 * @param array $var
	 */
	private function returnJson($var = array())
	{
		JsonHelper::sendJsonHeaders();

		// Output it into a buffer, in case TasksService wants to close the connection prematurely
		ob_start();
		echo JsonHelper::encode($var);

		craft()->end();
	}

	/**
	 * Gets a header from the request
	 *
	 * @param $headerName String Name of the header to get
	 * @return null
	 */
	private function getHeader($headerName) {
		$headers = array();
		foreach($_SERVER as $key => $value) {
			if (substr($key, 0, 5) <> 'HTTP_') {
				continue;
			}
			$header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
			$headers[$header] = $value;
		}
		return !empty($headers[$headerName]) ? $headers[$headerName] : null;
	}

	/**
	 * Init
	 */
	public function init() {
		parent::init();

		$this->settings = craft()->plugins->getPlugin('tokenAuth')->getSettings();
	}

	/*
	 * Public Actions
	 */
	/**
	 * Login User
	 *
	 * @throws HttpException
	 */
	public function loginUser()
	{
		if (craft()->request->getRequestType() !== 'POST') throw new HttpException(400);

		craft()->users->purgeExpiredPendingUsers();

		$loginName = craft()->request->getPost('loginName');
		$password = craft()->request->getPost('password');

		if (craft()->userSession->login($loginName, $password)) {
			$user = craft()->userSession->getUser();
			$this->generateJwt($user);
		} else {
			$errorCode = craft()->userSession->getLoginErrorCode();
			$errorMessage = craft()->userSession->getLoginErrorMessage($errorCode, $loginName);

			$this->returnJson(array(
				'errorCode' => $errorCode,
				'error' => $errorMessage
			));
		}
	}

	/**
	 * Handle all other post actions
	 */
	public function post()
	{
		// Ensure this is a post request
		if (craft()->request->getRequestType() !== 'POST') throw new HttpException(400);

		$this->returnJson($_SERVER);

		// Ensure we have an action
		if (empty($_POST['act'])) $this->returnJson(['error'=>'action-required']);

		// If we have a JWT, try to authenticate
		if (craft()->request->getParam('jwt')) $this->checkAuth(craft()->request->getParam('jwt'));

		// Fake Ajax to force JSON response
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

		// Get Action
		$action = $_POST['act'];

		// Run action
		try {
			craft()->runController($action);
		} catch (\Exception $e) {
			$this->returnJson(['error'=>$e->getCode(), 'message'=>$e->getMessage()]);
		}
	}

	public function checkAuth($jwt = false)
	{
		if (craft()->request->getRequestType() !== 'POST') $jwt = craft()->request->getParam('jwt');

		$ret = false;

		$secret = $this->settings->secret;
		$expirationTime = (int) $this->settings->expirationTime;

		sscanf($this->getHeader('Authorization'), 'Bearer %s', $authHeaderToken);

		$token = $jwt ?: $authHeaderToken;

		if ($token) {
			$err = '';

			try {
				$decoded = JWT::decode($token, $secret, array('HS256'));

				$username = $decoded->username;
				$iat = $decoded->iat;

				$doesUserExist = craft()->users->getUserByUsernameOrEmail($username);

				if ($doesUserExist) {

					$hasTokenExpired = false;

					if ($expirationTime !== -1) {
						$timeDifference = time() - $iat;
						$hasTokenExpired = $timeDifference > $expirationTime;
					}

					if (!$hasTokenExpired) {

						$userId = $doesUserExist->id;

						if (craft()->userSession->loginByUserId($userId)) {
//							$this->returnJson(craft()->userSession->getUser());
//							return craft()->userSession->getUser();
							return true;
						} else {
							$ret = ['error'=>'login-failed'];
						}

					} else {
						$ret = ['error'=>'jwt-expired'];
					}

				} else {
					$ret = ['error'=>'invalid-username'];
				}

			} catch (\Firebase\JWT\SignatureInvalidException $e) {
				$err = $e->getMessage();
			} catch (\Firebase\JWT\BeforeValidException $e) {
				$err = $e->getMessage();
			} catch (\Firebase\JWT\ExpiredException $e) {
				$err = $e->getMessage();
			} catch (\DomainException $e) {
				$err = $e->getMessage();
			} catch (\UnexpectedValueException $e) {
				$err = $e->getMessage();
			}

			if ($err != '') {
				$ret = ['error'=>'jwt-error'];
				$ret['message'] = $err;
				$ret['token-received'] = $token;
			}

		} else {
			$ret = ['error'=>'jwt-required'];
		}

		if ($ret) {
			$this->returnJson($ret);
		}
	}

	private function generateJwt($user)
	{
		$secret = $this->settings->secret;
		$payload = array(
			"username" => $user['username'],
			"id" => $user['id'],
			"iat" => time()
		);

		$token = JWT::encode($payload, $secret);

		$this->returnJson(['jwt'=>$token]);
	}

}