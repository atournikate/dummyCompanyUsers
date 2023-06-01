<?php
require_once 'Dummy.php';


class DummyUser extends Dummy {


    public function __construct() {

    }

    private function getLastName($lang) {
        $arr = $this->createArrayFromJSON($lang . self::SURNAMES);
        $value = $this->getRandomValueFromArray($arr);
        return ucfirst(strtolower($value));
    }

    private function getFirstName($lang, $gender) {
        if ($gender == self::GENDER_NB) {
            $file = $lang . self::FILE_FIRSTNAME_NB;
        } elseif ($gender == self::GENDER_FEMALE) {
            $file = $lang . self::FILE_FIRSTNAME_FEMALE;
        } else {
            $file = $lang . self::FILE_FIRSTNAME_MALE;
        }
        $arr = $this->createArrayFromJSON($file);
        $value = $this->getRandomValueFromArray($arr);
        return ucfirst(strtolower($value));
    }

    private function createUserName($fullName) {
        $fullName = $this->replaceSpecialCharacters($fullName);
        $firstLetter = substr($fullName, 0, 1);
        $arr = explode(' ', $fullName);
        return strtolower($firstLetter . $arr['1']);
    }

    public function createEmailProvider() {
        $arr = [ "gmail.com", "bluewin.ch", "gmx.ch", "msn.ch", "sunrise.ch" ];
        return $this->getRandomValueFromArray($arr);
    }

    public function createHouseNumber() {
        $arr = $this->createArrayFromJSON('streets');
        $street = $this->getRandomValueFromArray($arr);
        $houseNum = rand(self::HOUSE_NR_MINIMUM, self::HOUSE_NR_MAXIMUM);
        return $street . " " . $houseNum;
    }

    public function createUser() {
        $gender     = $this->assignRandomGender();
        $lang       = $this->assignRandomLanguage();
        $name       = $this->getFirstName($lang, $gender);
        $surname    = $this->getLastName($lang);
        $fullName   = $name . " " . $surname;
        $plzCityArr = $this->getPLZCityArray();
        $plz        = $this->getRandomPLZ($plzCityArr);
        $city       = $this->getCityFromPLZ($plzCityArr, $plz);

        $user = [
            'first_name'        => $name,
            'last_name'         => $surname,
            'full_name'         => $fullName,
            'language'          => $lang,
            'gender'            => $gender,
            'address'           => $this->createHouseNumber(),
            'plz'               => $plz,
            'city'              => $city,
            'personal_email'    => '',
            'user_id'           => '',
            'username'          => '',
            'unit'              => '',
            'unit_code'         => '',
            'job_title'         => '',
            'company_name'      => '',
            'company_street'    => '',
            'company_plz'       => '',
            'acronym'           => '',
            'email'             => '',
            'password'          => '',
            'is_demo_user'      => 1,
            'is_real_user'      => 0,
            'mut_user'          => ''
        ];

        print_r($user);

    }


}




$dummy = new DummyUser();
$dummy->createUser();


