<?php
include_once 'vendor/autoload.php';
use Shuchkin\SimpleXLSX;

const LANG_DE = 'de';
const LANG_EN = 'en';
const LANG_FR = 'fr';
const LANG_IT = 'it';

class DummyUser {
    const COMPANY_XLSX  = "company.xlsx";
    const NAME_XLSX     = "nameTable";

    const FILE_TO_JSON  = "json/";

    const GENDER_FEMALE = "f";
    const GENDER_MALE   = "m";
    const GENDER_NB     = "nb";

    const FIRST_NAMES       = "names";
    const SURNAMES          = "_surnames";

    const FIRSTNAMES_FEMALE = "_" . self::GENDER_FEMALE . "_" . self::FIRST_NAMES;
    const FIRSTNAMES_MALE   = "_" . self::GENDER_MALE . "_" . self::FIRST_NAMES;
    const FIRSTNAMES_NB   = "_" . self::GENDER_NB . "_" . self::FIRST_NAMES;

    public function __construct() {
    }

    public function getArrayFromJSON($fileName) {
        $file = file_get_contents(self::FILE_TO_JSON . $fileName . ".json");
        return json_decode($file, true);
    }

    /*public function getArrayFromExcel($tableName, $sheetName) {
        $xlsx = SimpleXLSX::parse($tableName);
        $metaData = $xlsx->sheetMeta();
        $num = count($metaData);
        $arr = [];
        for ($i = 0; $i < $num; $i++) {
            if (in_array($sheetName, $metaData[$i])) {
                $rowData = $xlsx->rowsEx($i);

                foreach ($rowData as $row) {
                    foreach ($row as $data) {
                        $arr[] = $data['value'];
                    }
                }
            }
        }
        return $arr;
    }*/

    public function getSurnameArray($lang) {
        return $this->getArrayFromJSON($lang . self::SURNAMES);
        //return $this->getArrayFromExcel(self::NAME_XLSX, $lang . self::SURNAMES);
    }
    public function getMaleNameArray($lang) {
        return $this->getArrayFromJSON( $lang . self::FIRSTNAMES_MALE);
        //return $this->getArrayFromExcel(self::NAME_XLSX, $lang . self::FIRSTNAMES_MALE);
    }
    public function getFemaleNameArray($lang) {
        return $this->getArrayFromJSON( $lang . self::FIRSTNAMES_FEMALE);
        //return $this->getArrayFromExcel(self::NAME_XLSX, $lang . self::FIRSTNAMES_FEMALE);
    }
    public function getNonBinaryNameArray() {
        return $this->getArrayFromJSON( self::FIRSTNAMES_NB);
        //return $this->getArrayFromExcel(self::NAME_XLSX, $lang . self::FIRSTNAMES_NB);
    }

    /*public function getCompanyArray() {
         //$data = $this->getArrayFromExcel(self::COMPANY_XLSX, 'branches');
        //$data = $this->getCompanyStructureFromJSON();
        $structure = 'hierarchy';
        $userId = rand(100000, 99999999999);
        $company = [
            "id" => rand(100000, 9999999999),
            "name" => "",
            "org_structure" => $structure,
            "leadership" => [
                "position" => [
                    "title" => '',
                    "person" => $userId,

                ],

            ]
        ];

    }*/

    private function getLastName($lang) {
        $arr = $this->getSurnameArray($lang);
        $key = rand(0, count($arr));
        return $arr['last_names'][$key];
    }

    private function getFirstName($lang, $gender) {
        if ($gender == 'f') {
            $arr = $this->getFemaleNameArray($lang);
        } elseif ($gender == 'm') {
            $arr = $this->getMaleNameArray($lang);
        } else {
            $arr = $this->getNonBinaryNameArray();
        }
        $num = count($arr);
        $key = rand(0, $num);
        return $arr['first_names'][$key];
    }

    private function getGender() {
        $rand = rand(1,3);
        if ($rand == 1) {
            $gender = self::GENDER_FEMALE;
        } elseif ($rand == 2) {
            $gender = self::GENDER_MALE;
        } else {
            $gender = self::GENDER_NB;
        }
        return $gender;
    }

    private function getLanguageLocation() {
        $rand = rand(1,10);
        if ($rand == 1) {
            $lang = LANG_EN;
        } elseif ( $rand == 2 ) {
            $lang = LANG_IT;
        } elseif ($rand < 5) {
            $lang = LANG_IT;
        } else {
            $lang = LANG_DE;
        }
        return $lang;
    }

    private function getUserId($lang) {
        if ($lang == LANG_EN) {
            $pre = 400;
        } elseif ($lang == LANG_IT) {
            $pre = 300;
        } elseif ($lang == LANG_FR) {
            $pre = 200;
        } else {
            $pre = 100;
        }
        $rand = rand(10000, 99999);
        return $pre . $rand;
    }

    private function replaceSpecialCharacters($fullName) {
        $conversionArray = [
            "ä" => "ae",
            "ö" => "oe",
            "ü" => "ue",
            "é" => "e",
            "ë" => "e",
            "É" => "E"
        ];

        foreach ($conversionArray as $original => $replacement) {
            $translationArray = explode(',', $original);
            foreach ($translationArray as $search) {
                $fullName = str_replace($search, $replacement, $fullName);
            }
        }
        return $fullName;
    }

    private function getUserName($fullName) {
        $fullName = $this->replaceSpecialCharacters($fullName);
        $firstLetter = substr($fullName, 0, 1);
        $arr = explode(' ', $fullName);
        return strtolower($firstLetter . $arr['1']) . rand(1, 99);
    }

    private function getUserPassword($userName) {
        return password_hash($userName, '2y');
    }

    private function getEmailProvider() {
        $arr = [
            "gmail.com",
            "bluewin.ch",
            "gmx.ch",
            "msn.ch",
            "sunrise.ch"
        ];

        return array_rand($arr);
    }

    public function createUser() {
        $gender = $this->getGender();
        $lang = $this->getLanguageLocation();
        $lastName = $this->getLastName($lang);
        $firstName = $this->getFirstName($lang, $gender);
        $fullName = $firstName . " " . $lastName;
        $userName = $this->getUserName($fullName);

        $user = [
            'userID'        => $this->getUserId($lang),
            'username'      => $userName,
            'password'      => $this->getUserPassword($userName),
            'language'      => $lang,
            'first_name'    => $firstName,
            'last_name'     => $lastName . " TEST",
            'full_name'     => $fullName . " TEST",
            'company_id'    => 123456789,
            'org_id'        => 1,
            'is_demo_user'  => 1,
            'is_real_user'  => 0,
            'email'         => $userName . '@' . $this->getEmailProvider(),
            'mut_user'      => 999999
        ];
        print_r($user);exit;
        return $user;
    }




    /*public function createUsers($setNum) {
        $users = [];
        for ($i = 0; $i < $setNum; $i++) {
            $users[] = $this->createUser();
        }
        //print_r($users);
        return $users;
    }*/

    /*private function getFakeUser($lang, $setNum) {
        $fakeUser = new fakeUser($lang);
        return $fakeUser->createUsers($setNum);
    }*/

    public function createDummyUserArray() {
        $dummyUserArray = [];
/*        $dummyUserArray[] = $this->getFakeUser(LANG_DE, 50);
        $dummyUserArray[] = $this->getFakeUser(LANG_FR, 20);
        $dummyUserArray[] = $this->getFakeUser(LANG_IT, 10);
        $dummyUserArray[] = $this->getFakeUser(LANG_EN, 10);*/

        $cleanArr = [];
        foreach ($dummyUserArray as $userByLang) {
            foreach ($userByLang as $user) {
                $cleanArr[] = $user;
            }
        }
        return $cleanArr;
    }

    public function writeDummyUserArrayToJSON() {
        $dummyArr = $this->createDummyUserArray();

        $jsonFile = realpath('/Users/kebensteiner/Documents/code/user-gen/dummyUser.json');
        if(!file_exists($jsonFile)) {
            fopen('dummyUser.json','w+');
        } else {
            $current = file_get_contents('dummyUser.json');
            $current .= json_encode($dummyArr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            /*foreach ($dummyArr as $key => $user) {
                $current .= json_encode($dummyArr[$key]=$user);

            }*/
            file_put_contents('dummyUser.json', $current);
        }

    }

}


$dummy = new DummyUser();
$dummy->createUser();