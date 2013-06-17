<?php
/**
 * Class Cookies
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 17.06.13
 */
namespace Flame\Facebook;

use Nette\Object;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

class Cookies extends Object
{

	/** @var \Nette\Http\IRequest  */
	private $httpRequest;

	/** @var \Nette\Http\IResponse  */
	private $httpResponse;

	/**
	 * @param IRequest  $request
	 * @param IResponse $response
	 */
	public function __construct(IRequest $request, IResponse $response)
	{
		$this->httpRequest = $request;
		$this->httpResponse = $response;
	}

	/**
	 * @param $appId
	 */
	public function destroy($appId)
	{
		$fbCookieName = 'fbsr_' . $appId;
		if ($this->httpRequest->getCookie($fbCookieName) !== null) {
			$this->httpResponse->deleteCookie($fbCookieName);
		}
	}

}