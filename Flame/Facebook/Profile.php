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
use Nette\Object;

class Profile extends Object
{

	/** @var \Facebook  */
	private $facebook;

	/** @var \Flame\Facebook\Cookies  */
	private $cookies;

	/**
	 * @param \Facebook $facebook
	 * @param Cookies   $cookies
	 */
	public function __construct(\Facebook $facebook, Cookies $cookies)
	{
		$this->facebook = $facebook;
		$this->cookies = $cookies;
	}

	/**
	 * @return \Facebook
	 */
	public function getFacebook()
	{
		return $this->facebook;
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

	public function logout()
	{
		$this->cookies->destroy($this->facebook->getAppId());
		$this->facebook->destroySession();
	}

}