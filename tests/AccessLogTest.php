<?php

use \Leo\Middlewares\AccessLog;
use \Leo\Fixtures\DummyLogger;
use \Leo\Fixtures\DummyRequestHandler;
use \PHPUnit\Framework\TestCase;
use \GuzzleHttp\Psr7;

/**
 * @testdox \Leo\Middlewares\AccessLog
 */
class AccessLogTest extends TestCase
{
	public function testLoggingWithoutRequestId():void
	{
		$this->expectOutputRegex('/200.*?METHOD \/path\/ HTTP\/1\.1/');

		$middleware = new AccessLog(new DummyLogger());
		$handler = new DummyRequestHandler();
		$request = (new Psr7\ServerRequest('METHOD', 'http://domain.tld/path/'))
			->withAttribute('REMOTE_IP', '10.0.0.1');

		$middleware->process($request, $handler);
	}

	public function testLoggingWithRequestId():void
	{
		$this->expectOutputRegex('/req_id.*?200.*?METHOD \/path\/ HTTP\/1\.1/');

		$middleware = new AccessLog(new DummyLogger());
		$handler = new DummyRequestHandler();
		$request = (new Psr7\ServerRequest('METHOD', 'http://domain.tld/path/'))
			->withAttribute('REMOTE_IP', '10.0.0.1')
			->withAttribute('REQUEST_ID', 'req_id');

		$middleware->process($request, $handler);
	}
}

?>
