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

class UserService extends \Nette\Object
{

	/**
	 * @var string
	 */
	private $user;

	/**
	 * @var \Facebook $facebook
	 */
	private $facebook;

	/**
	 * @var \Nette\Http\IRequest
	 */
	private $httpRequest;

	/**
	 * @param \Facebook $facebook
	 * @param \Nette\Http\IRequest $request
	 */
	public function __construct(\Facebook $facebook, \Nette\Http\IRequest $request)
	{
		$this->facebook = $facebook;
		$this->httpRequest = $request;
	}

	/**
	 * @return string
	 */
	public function getUser()
	{
		if($this->user === null)
			$this->user = $this->facebook->getUser();

		return $this->user;
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		if(!$this->getUser()) return;

		try {
			return $this->facebook->api($this->user);
		}catch (\FacebookApiException $ex){
			Debugger::log($ex);
			$this->user = null;
		}
	}

	/**
	 * @return mixed
	 */
	public function getAvatarUrl()
	{
		if(!$this->getUser()) return;

		try {
			$fields = $this->facebook->api($this->user, array(
				'fields' => 'picture',
				'type'   => 'large'
			));

			if(isset($fields['picture']['data']['url']))
				return $fields['picture']['data']['url'];

		}catch (\FacebookApiException $ex){
			Debugger::log($ex);
			$this->user = null;
		}
	}

	/**
	 * @return mixed
	 */
	public function getFriends()
	{
		if(!$this->getUser()) return;

		try {
			$friends = $this->facebook->api('/me/friends');

			if(isset($friends['data']))
				return $friends['data'];

		}catch (\FacebookApiException $ex){
			Debugger::log($ex);
			$this->user = null;
		}
	}

	/**
	 * @param $key
	 * @param null $default
	 * @return null
	 */
	public function getDataBy($key, $default = null)
	{
		$api = $this->getUserData();
		return (isset($api[$key])) ? $api[$key] : $default;
	}

	/**
	 * @param null $redirect
	 * @return string
	 */
	public function getLoginurl($redirect = null)
	{
		$params = array('scope' => 'email');
		if($redirect) $params += array('redirect_uri' => $redirect);
		return $this->facebook->getLoginUrl($params);
	}

	/**
	 * @return bool
	 */
	public function logout()
	{
		$fbCookieName = 'fbsr_' . $this->facebook->getAppId();
		if ($this->httpRequest->getCookie($fbCookieName) !== null) {
			$this->httpRequest->deleteCookie($fbCookieName);
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