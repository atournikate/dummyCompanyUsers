<?php

abstract class Dummy {
    const LANG_DE                           = 'de';
    const LANG_EN                           = 'en';
    const LANG_FR                           = 'fr';
    const LANG_IT                           = 'it';

    const GENDER_FEMALE                     = "f";
    const GENDER_MALE                       = "m";
    const GENDER_NB                         = "nb";
    const FIRST_NAMES                       = "names";
    const SURNAMES                          = "_surnames";

    const PATH_TO_JSON                      = "json/";
    const FILE_FIRSTNAME_FEMALE             = "_" . self::GENDER_FEMALE . "_" . self::FIRST_NAMES;
    const FILE_FIRSTNAME_MALE               = "_" . self::GENDER_MALE . "_" . self::FIRST_NAMES;
    const FILE_FIRSTNAME_NB                 = self::GENDER_NB . "_" . self::FIRST_NAMES;

    const GENDER_MALE_CHANCE_PERCENT        = 45;
    const GENDER_FEMALE_CHANCE_PERCENT      = 49;
    const GENDER_NON_BINARY_CHANCE_PERCENT  = 100 - self::GENDER_MALE_CHANCE_PERCENT - self::GENDER_FEMALE_CHANCE_PERCENT;

    const LANG_FR_PERCENT_CHANCE            = 20;
    const LANG_IT_PERCENT_CHANCE            = 10;
    const LANG_EN_PERCENT_CHANCE            = 7;
    const LANG_DE_PERCENT_CHANCE            = 100 - self::LANG_FR_PERCENT_CHANCE - self::LANG_IT_PERCENT_CHANCE - self::LANG_EN_PERCENT_CHANCE;

    const NUMBER_OF_USERS                   = 140;

    const USER_ID_MINIMUM                   = 2000001;
    const ORGANISATION_ID_MINIMUM           = 5000001;

    const HOUSE_NR_MINIMUM                  = 1;
    const HOUSE_NR_MAXIMUM                  = 100;

    const EMAIL_DOMAIN                      = 'dummy.ch';
    const COMPANY_NAME                      = 'Dummy Company';

    const ORG_DEPTH                         = 10;

    const ASSISTANT_LEVEL_UNTIL             = 2;

    /**
     * "hierarchy": ["Chief", "President", "Vice President", "Director", "Manager", "Assistant Manager", "Supervisor", "Associate"],
    "branches": [
    "Executive", "Operations", "Financial", "Information", "Technology", "Sales", "Human Resources"
    ]
     */

    public function createArrayFromJSON($fileName) {
        $file = file_get_contents(self::PATH_TO_JSON . $fileName . ".json");
        $json = json_decode($file, true);
        return $json['data'];
    }

    public function getRandomValueFromArray($array) {
        $key = array_rand($array, 1);
        return $array[$key];
    }

    public function getRandomKeyFromArray($array) {
        $key = array_rand($array, 1);
        return $key;
    }

    public function updateArray($array, $fieldName, $value) {
        $array[$fieldName] = $value;
        return $array;
    }

    public function assignRandomGender() {
        $genders = [
            self::GENDER_FEMALE    => [
                'min'              => 0,
                'max'              => self::GENDER_FEMALE_CHANCE_PERCENT
            ],
            self::GENDER_MALE      => [
                'min'              => self::GENDER_FEMALE_CHANCE_PERCENT,
                'max'              => self::GENDER_FEMALE_CHANCE_PERCENT + self::GENDER_MALE_CHANCE_PERCENT
            ],
            self::GENDER_NB        => [
                'min'              => 100 - self::GENDER_NON_BINARY_CHANCE_PERCENT,
                'max'              => 100
            ]
        ];

        $random = rand(1,100);
        foreach ($genders as $key => $value) {
            if ($random > $value['min'] && $random <= $value['max']) {
                $gender = $key;
            }
        }
        return $gender;
    }

    public function assignRandomLanguage() {
        $languages = [
            self::LANG_EN => [
                'min'       => 0,
                'max'       => self::LANG_EN_PERCENT_CHANCE
            ],
            self::LANG_IT => [
                'min'       => self::LANG_EN_PERCENT_CHANCE,
                'max'       => self::LANG_EN_PERCENT_CHANCE + self::LANG_IT_PERCENT_CHANCE
            ],
            self::LANG_FR => [
                'min'       => self::LANG_EN_PERCENT_CHANCE + self::LANG_IT_PERCENT_CHANCE,
                'max'       => self::LANG_EN_PERCENT_CHANCE + self::LANG_IT_PERCENT_CHANCE + self::LANG_FR_PERCENT_CHANCE
            ],
            self::LANG_DE => [
                'min'       => 100 - self::LANG_DE_PERCENT_CHANCE,
                'max'       => 100
            ]
        ];

        $random = rand(1,100);
        foreach ($languages as $key => $value) {
            if ($random > $value['min'] && $random <= $value['max']) {
                $lang = $key;
            }
        }

        return $lang;
    }

    public function replaceSpecialCharacters($fullName) {
        $conversionArray = [
            "ä" => "ae",
            "ö" => "oe",
            "ü" => "ue",
            "é" => "e",
            "ë" => "e",
            "É" => "E",
            "ï" => "i",
            "&nbsp;" => "",
            "&apos;" => ""
        ];

        foreach ($conversionArray as $original => $replacement) {
            $translationArray = explode(',', $original);
            foreach ($translationArray as $search) {
                $fullName = str_replace($search, $replacement, $fullName);
            }
        }
        return $fullName;
    }

    public function getPLZCityArray() {
        $plzArr = $this->createArrayFromJSON('plz');
        $cityArr = $this->createArrayFromJSON('city');
        $plzCityArr = array_combine($plzArr, $cityArr);
        return $plzCityArr;
    }

    public function getRandomPLZ($arr) {
        return $this->getRandomKeyFromArray($arr);
    }

    public function getCityFromPLZ($arr, $plz) {
        return $arr[$plz];
    }

    public function getPLZCity() {
        $arr = $this->getPLZCityArray();
        $plz = $this->getRandomPLZ($arr);
        $city = $this->getCityFromPLZ($arr, $plz);

        return $plz . " " . $city;
    }

    private function createUserId() {
        return rand(self::USER_ID_MINIMUM, 499999);
    }

    public function createPassword($value) {
        return password_hash($value, '2y');
    }


    
}