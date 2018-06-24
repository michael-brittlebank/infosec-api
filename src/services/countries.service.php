<?php

namespace App\Services;

class CountriesService {

    // todo, replace service-level variable with caching mechanism like redis
    private $countryList = null;

    public function getCountryList() {

        if (is_null($this->countryList)) {
            try {
                $pest = new \Pest('https://restcountries.eu');
                $this->countryList = json_decode($pest->get('/rest/v2/all'), true);
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