<?php

class CountriesService {

    // todo, replace service-level variable with caching mechanism like redis
    private $countryList = null;

    public function getCountryList() {

        if (is_null($this->countryList)) {
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://restcountries.eu/rest/v2/all');
                $this->countryList = json_decode(curl_exec($ch));
                return $this->countryList;
            } catch (Error $e) {
                $this->logger->error($e);
                return null;
            }
        } else {
            return $this->countryList;
        }
    }
}