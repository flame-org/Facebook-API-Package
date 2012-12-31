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
	 * @param \Facebook $facebook
	 */
	public function __construct(\Facebook $facebook)
	{
		$this->facebook = $facebook;
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
			$this->user = null;
		}
	}

	/**
	 * @return null
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
			\Nette\Diagnostics\Debugger::log($ex);
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
		$params = array('scope' => 'email', 'display' => 'popup');
		if($redirect) $params += array('redirect_uri' => $redirect);
		return $this->facebook->getLoginUrl($params);
	}

	/**
	 * @return \Facebook
	 */
	public function getFacebookService()
	{
		return $this->facebook;
	}

}