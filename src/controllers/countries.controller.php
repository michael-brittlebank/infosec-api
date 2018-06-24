<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->group('/countries', function () use ($app) {

    $app->post('/search', function (Request $request, Response $response, array $args) {
        $this->logger->info("Slim-Skeleton '/countries/search' route");
        $countriesService = new CountriesService();
        $countryList = $countriesService->getCountryList();

        $newResponse = $response->withJson($countryList);
        return $newResponse;
    });

})->add(function ($request, $response, $next) {
    if ($request->isOptions()) {
        $response = $next($request, $response);
        return $response;
    } else {
        // todo, use real authorisation service
        $headers = $request->getHeaders();
        if (array_key_exists('HTTP_X_AUTH_TOKEN', $headers) && $headers['HTTP_X_AUTH_TOKEN'][0] == "abcd1234") {
            $response = $next($request, $response);
            return $response;
        } else {
            return $response->withJson(array('message' => 'Unauthorised'))->withStatus(403);
        }
    }
});