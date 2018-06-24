<?php

use Slim\Http\Request;
use Slim\Http\Response;
use App\Services\CountriesService;

$app->group('/countries', function () use ($app) {

    $app->post('/search', function (Request $request, Response $response, array $args) {
        $this->logger->info("Slim-Skeleton '/countries/search' route");
        $parsedBody = $request->getParsedBody();
        $searchTerm = $parsedBody['searchTerm'];
        $countriesService = new CountriesService();
        $countryList = $countriesService->getCountryList();
        $filteredCountryList = array();
        // filter
        foreach ($countryList as $country) {
            if (stripos($country['name'], $searchTerm) !== false ||
                stripos($country['alpha2Code'], $searchTerm) !== false ||
                stripos($country['alpha3Code'], $searchTerm) !== false) {
                $filteredLanguages = array();
                foreach ($country['languages'] as $language) {
                    array_push($filteredLanguages, $language['name']);
                }
                array_push($filteredCountryList, array(
                    "name" => $country['name'],
                    "alphaCode2" => $country['alpha2Code'],
                    "alphaCode3" => $country['alpha3Code'],
                    "flag" => $country['flag'],
                    "region" => $country['region'],
                    "subregion" => $country['subregion'],
                    "population" => $country['population'],
                    "languages" => $filteredLanguages
                ));
            }
        }

        // sort by name and population
        // todo, this doesn't make any sense since names are not shared amongst countries. clarify requirement from product owner
        array_multisort(
            array_column($filteredCountryList, 'name'), SORT_ASC,
            array_column($filteredCountryList, 'population'), SORT_DESC,
            $filteredCountryList);

        // paginate
        // todo, implement real pagination
        $sizeOfResults = sizeof($filteredCountryList);
        if (sizeof($filteredCountryList) > 50) {
            // limit to 50 results
            $filteredCountryList = array_slice($filteredCountryList, 0, 50);
        }

        // build response
        $results = array(
            "metadata" => array(
                "totalResults" => $sizeOfResults,
                "totalCountries" => sizeof($countryList)
            ),
            "countries" => $filteredCountryList
        );
        $newResponse = $response->withJson($results);
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