<?php

namespace App\Services;

class CountriesService {

    // todo, replace service-level variable with caching mechanism like redis
    private $countryList = null;

    public function getCountryList() {

        if (is_null($this->countryList)) {
            try {
                // since countries change very infrequently and therefore this call has a non-variable response size it is easier to store it in a local cache and perform filtering, sorting, and pagination operations on it ourselves
                $pest = new \Pest('https://restcountries.eu');
                $this->countryList = json_decode($pest->get('/rest/v2/all?fields=name;alpha2Code;alpha3Code;flag;region;subregion;population;languages'), true);
                return $this->countryList;
            } catch (\Pest_BadRequest $e) {
                $this->logger->error($e);
                return null;
            }
        } else {
            return $this->countryList;
        }
    }
}