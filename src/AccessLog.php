<?php

namespace Leo\Middlewares;

use \Psr\Log\LoggerInterface;
use \Psr\Http\Server\MiddlewareInterface;
use \Psr\Http\Server\RequestHandlerInterface;
use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;

class AccessLog implements MiddlewareInterface
{
	/**
	 * @var \Psr\Log\LoggerInterface Logger
	 */
	private ?LoggerInterface $logger;

	public function __construct(LoggerInterface $logger = null)
	{
		$this->logger = $logger;
	}

	public function process(
		ServerRequestInterface $request,
		RequestHandlerInterface $handler
	):ResponseInterface
	{
		$response = $handler->handle($request);

		if (!is_null($this->logger)) {
			// Retrieve IP address from remote IP middleware,
			// server param for fallback.
			$ip =
				$request->getAttribute('REMOTE_IP') ??
				$request->getServerParams()['REMOTE_ADDR'];

			$log =
				"{$ip} {$response->getStatusCode()} ".
				"\"\x1B[33m".
				"{$request->getMethod()} ".
				"{$request->getRequestTarget()} ".
				"HTTP/{$request->getProtocolVersion()}".
				"\x1B[0m\"";

			// Prepend request ID if exist
			if (!is_null($request_id = $request->getAttribute('REQUEST_ID')))
				$log = "{$request_id} " . $log;

			$this->logger->info($log);
		}

		return $response;
	}
}

?>
