<?php
/**
 * Sloxy (https://github.com/dlundgren/sloxy)
 *
 * @link      https://github.com/dlundgren/sloxy
 * @copyright Copyright (c) 2018 David Lundgren
 * @license   https://github.com/dlundgren/sloxy/blob/master/LICENSE.md (MIT License)
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$app = new \Slim\App();
$app->any(
	'/[{path:.*}]',
	function (ServerRequestInterface $req, ResponseInterface $res) {
		$uri  = $req->getUri();
		$host = $req->getHeaderLine('X-NEXT-HOST');
		if (empty($host)) {
			$params = $req->getQueryParams();
			if (empty($params['__host'])) {
				$res = $res->withStatus('409');
				$res->getBody()->write("Must supply X-NEXT-HOST header or __host query param");

				return $res;
			}
			$host = $params['__host'];
			unset($params['__host']);
			$uri = $uri->withQuery(http_build_query($params));
		}

		$url = parse_url($host);
		$uri = $uri->withHost($url['host']);
		if ($uri->getScheme() !== $url['scheme']) {
			$uri = $uri->withScheme($url['scheme']);
		}

		$port = $uri->getPort();
		if (empty($url['port'])) {
			if ($url['scheme'] === 'https' && $port != 443) {
				$url['port'] = 443;
			}
			elseif ($port != 80) {
				$url['port'] = 80;
			}
		}

		$req    = $req->withUri($uri->withPort($url['port']));
		$client = new Client();
		try {
			$response = $client->send($req);
		}
		catch (\Exception $e) {

			$response = $res->withStatus(500);
		}

		// we have a problem with chunked at the moment
		if ($response->hasHeader('Transfer-Encoding')) {
			$encoding = explode(',', $response->getHeaderLine('Transfer-Encoding'));
			$encoding = array_filter($encoding, function ($v) {
				return 'chunked' != trim($v);
			});
			if (empty($encoding)) {
				$response = $response->withoutHeader('Transfer-Encoding');
			}
			else {
				$response = $response->withHeader('Transfer-Encoding', join(', ', $encoding));
			}
		}

		return $response;
	}
);

// remain silent on errors, but send the response
$app->respond($app->run(true));