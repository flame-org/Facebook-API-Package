<?php
/**
 * Test: Flame\Tests\Facebook\ProfileTest
 *
 * @package \Flame\Tests\Facebook
 */
 
namespace Flame\Tests\Facebook;

use Flame\Tester\MockTestCase;
use Nette;
use Tester\Assert;
use Flame\Tester\TestCase;
use Flame\Facebook\Profile;

require_once __DIR__ . '/../bootstrap.php';

class ProfileTest extends MockTestCase
{

	/** @var  \Flame\Facebook\Profile */
	private $profile;

	/** @var  \Mockista\MockInterface */
	private $facebookMock;

    public function setUp()
    {
        parent::setUp();

	    $this->facebookMock = $this->mockista->create('\Facebook');
	    $this->profile = new Profile($this->facebookMock);
    }

	public function testGetFacebook()
	{
		Assert::true($this->profile->getFacebook() instanceof \Facebook);
	}
    
    public function testGetId()
    {
	    $this->facebookMock->expects('getUser')
		    ->once()
		    ->andReturn('1');

	    Assert::same('1', $this->profile->getId());
    }

	public function testGetDataException()
	{
		$this->facebookMock->expects('api')
			->with('/me')
			->once()
			->andThrow(new \FacebookApiException(array()));

		Assert::throws(function() {
			$this->profile->getData();
		}, '\Nette\InvalidStateException');
	}

	public function testGetData()
	{
		$this->facebookMock->expects('api')
			->with('/me')
			->once()
			->andReturn(array());

		Assert::same(array(), $this->profile->getData());
	}

	public function testGetDataByNotExistKey()
	{
		$this->facebookMock->expects('api')
			->with('/me')
			->once()
			->andReturn(array());

		Assert::same(1, $this->profile->getDataBy('key', 1));
	}

	public function testGetDataBy()
	{
		$this->facebookMock->expects('api')
			->with('/me')
			->once()
			->andReturn(array('key' => 1));

		Assert::same(1, $this->profile->getDataBy('key', 2));
	}

	public function testGetAvatarUrlNull()
	{
		$this->facebookMock->expects('getUser')
			->once()
			->andReturn(null);

		Assert::null($this->profile->getAvatarUrl());
	}

	public function testGetAvatarUrl()
	{
		$this->facebookMock->expects('getUser')
			->once()
			->andReturn('100002685942845');

		Assert::true(is_string($this->profile->getAvatarUrl()));
	}

	public function testGetFriendsException()
	{
		$this->facebookMock->expects('api')
			->with('/me/friends')
			->once()
			->andThrow(new \FacebookApiException(array()));

		Assert::throws(function() {
			$this->profile->getFriends();
		}, '\Nette\InvalidStateException');
	}

	public function testGetFriendsEmpty()
	{
		$this->facebookMock->expects('api')
			->with('/me/friends')
			->once()
			->andReturn(array());

		Assert::same(array(), $this->profile->getFriends());
	}

	public function testgetLoginUrl()
	{
		$expected = array(
			'redirect_uri' => 'url',
			'next' => 'url'
		);

		$this->facebookMock->expects('getLoginUrl')
			->once()
			->with($expected)
			->andReturn('loginUrl');

		Assert::same($expected, $this->profile->getLoginUrl(array(), 'url'));
	}

	public function testLogout()
	{
		$this->facebookMock->expects('destroySession')
			->once();

		$this->profile->logout();
	}
}
id(new ProfileTest)->run();