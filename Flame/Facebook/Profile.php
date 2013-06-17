<?php
/**
 * UserService.php
 *
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 * @package Flame
 *
 * @date    05.11.12
 */

namespace Flame\Facebook;

use Nette\Diagnostics\Debugger;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Object;

class Profile extends Object
{

	/** @var \Facebook  */
	private $facebook;

	/** @var \Nette\Http\IRequest  */
	private $httpRequest;

	/** @var \Nette\Http\IResponse  */
	private $httpResponse;

	/**
	 * @param \Facebook $facebook
	 * @param \Nette\Http\IRequest $request
	 * @param \Nette\Http\IResponse $response
	 */
	public function __construct(\Facebook $facebook, IRequest $request, IResponse $response)
	{
		$this->facebook = $facebook;
		$this->httpRequest = $request;
		$this->httpResponse = $response;
	}

	/**
	 * @return string
	 */
	public function getUser()
	{
		return $this->facebook->getUser();
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		try {
			return $this->facebook->api('/me');
		}catch (\FacebookApiException $ex){
			Debugger::log($ex);
		}
	}

	/**
	 * @param int $width
	 * @param int $height
	 * @return mixed
	 */
	public function getAvatarUrl($width = 200, $height = 200)
	{
		if($this->getUser()) {
			$headers = @get_headers('https://graph.facebook.com/' . $this->getUser() . '/picture?width=' . $width . '&height=' . $height, 1);

			if(isset($headers['Location']))
				return $headers['Location'];
		}
	}

	/**
	 * @return mixed
	 */
	public function getFriends()
	{
		try {
			$friends = $this->facebook->api('/me/friends');

			if(isset($friends['data']))
				return $friends['data'];

		}catch (\FacebookApiException $ex){
			Debugger::log($ex);
		}
	}

	/**
	 * @param $key
	 * @param null $default
	 * @return null
	 */
	public function getDataBy($key, $default = null)
	{
		$api = $this->getData();
		return (isset($api[$key])) ? $api[$key] : $default;
	}

	/**
	 * @param array $params
	 * @param null $redirect
	 * @return string
	 */
	public function getLoginUrl(array $params, $redirect = null)
	{
		if($redirect)
			$params += array(
				'redirect_uri' => $redirect,
				'next' => $redirect
			);
		return $this->facebook->getLoginUrl($params);
	}

	/**
	 * @return bool
	 */
	public function logout()
	{
		$fbCookieName = 'fbsr_' . $this->facebook->getAppId();
		if ($this->httpRequest->getCookie($fbCookieName) !== null) {
			$this->httpResponse->deleteCookie($fbCookieName);
		}
		$this->facebook->destroySession();

		return true;
	}

	/**
	 * @return \Facebook
	 */
	public function getFacebookService()
	{
		return $this->facebook;
	}

}