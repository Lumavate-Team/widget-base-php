<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Dflydev\FigCookies\FigRequestCookies;

// Routes

$app->get('/{integration_cloud}/{url_ref}/{widget_instance_id}', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/default' route");

		$uri = 'https://' . $request->getUri()->getHost() . '/';
		$uri .= $args['integration_cloud'] . '/';
		$uri .= $args['url_ref'] . '/';
		$uri .= $args['widget_instance_id'] . '/';
		$uri .= 'index';
		return $response->withStatus(302)->withHeader('Location', $uri);
});

$app->get('/{integration_cloud}/{url_ref}/{widget_instance_id}/index', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/default' route");

		$cookie = FigRequestCookies::get($request, 'pwa_jwt')->getValue();

		$headers = [
			'Authorization: Bearer ' . $cookie
		];

		$path = '/pwa/v1/widget-instances/' . $args['widget_instance_id'];
		$data = '';
		$cmd = 'python3 signer_cli.py "get" "' . $path . '" "' . $data . '"';
    $path = trim((string)shell_exec($cmd));
		$signed = getenv('BASE_URL') . $path;
    $this->logger->info($signed);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $signed,
				CURLOPT_HTTPHEADER => $headers
    ));
    $result = curl_exec($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
		if ($httpcode == 401) {
			$uri = 'https://' . $request->getUri()->getHost();
			$uri .= '?u=/';
			$uri .= $args['integration_cloud'] . '/';
			$uri .= $args['url_ref'] . '/';
			$uri .= $args['widget_instance_id'] . '/';
			$uri .= 'index';
			return $response->withStatus(302)->withHeader('Location', $uri);
		}

    // Render index view
    return $this->renderer->render($response, 'index.phtml', array('helloText' =>json_decode($result)->payload->data->helloText));
});

$app->get('/{integration_cloud}/{url_ref}/discover/properties', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/discover/properties' route");

    $data = array('payload'
            => array('data'
              => array(
                   array(
                     'classification' => 'General',
                     'section' => 'General Settings',
                     'default' => 'Hello, World!',
                     'helpText' => 'Our example property',
                     'label' => 'Hello PHP Text Property',
                     'name' => 'helloText',
										 'options' => array(),
                     'type' => 'text'
                   )
                 )
               )
            );

    // Render index view
    return $response->withJson($data);
});

$app->get('/{integration_cloud}/{url_ref}/discover/health', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/discover/health' route");

    // Render index view
    return 'Ok';
});

