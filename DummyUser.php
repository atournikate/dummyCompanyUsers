<?php

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
    const FIRSTNAMES_NB   = self::GENDER_NB . "_" . self::FIRST_NAMES;

    public function __construct() {
    }

    public function createArrayFromJSON($fileName) {
        $file = file_get_contents(self::FILE_TO_JSON . $fileName . ".json");
        return json_decode($file, true);
    }

    private function createSurnameArray($lang) {
        return $this->createArrayFromJSON($lang . self::SURNAMES);
        //return $this->createArrayFromExcel(self::NAME_XLSX, $lang . self::SURNAMES);
    }
    private function createMaleNameArray($lang) {
        return $this->createArrayFromJSON( $lang . self::FIRSTNAMES_MALE);
        //return $this->createArrayFromExcel(self::NAME_XLSX, $lang . self::FIRSTNAMES_MALE);
    }
    private function createFemaleNameArray($lang) {
        return $this->createArrayFromJSON( $lang . self::FIRSTNAMES_FEMALE);
        //return $this->createArrayFromExcel(self::NAME_XLSX, $lang . self::FIRSTNAMES_FEMALE);
    }
    private function createNonBinaryNameArray() {
        return $this->createArrayFromJSON( self::FIRSTNAMES_NB);
        //return $this->createArrayFromExcel(self::NAME_XLSX, $lang . self::FIRSTNAMES_NB);
    }

    private function createLastName($lang) {
        $arr = $this->createSurnameArray($lang);
        $num = count($arr['last_names']);
        $key = rand(0, $num);
        return ucfirst(strtolower($arr['last_names'][$key]));
    }

    private function createFirstName($lang, $gender) {
        if ($gender == 'f') {
            $arr = $this->createFemaleNameArray($lang);
        } elseif ($gender == 'm') {
            $arr = $this->createMaleNameArray($lang);
        } else {
            $arr = $this->createNonBinaryNameArray();
        }
        $num = count($arr['first_names']);
        $key = rand(0, $num);

        return ucfirst(strtolower($arr['first_names'][$key]));
    }

    private function createGender() {
        $rand = rand(1,10);
        if ($rand == 1 || $rand == 2) {
            $gender = self::GENDER_NB;
        } elseif ($rand <= 6) {
            $gender = self::GENDER_FEMALE;

        } else {
            $gender = self::GENDER_MALE;

        }
        return $gender;
    }

    private function createLanguageLocation() {
        $rand = rand(1,10);
        if ($rand == 1) {
            $lang = LANG_EN;
        } elseif ( $rand == 2 ) {
            $lang = LANG_IT;
        } elseif ($rand < 5) {
            $lang = LANG_FR;
        } else {
            $lang = LANG_DE;
        }
        return $lang;
    }

    private function createUserId($lang) {
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

    private function createUserName($fullName) {
        $fullName = $this->replaceSpecialCharacters($fullName);
        $firstLetter = substr($fullName, 0, 1);
        $arr = explode(' ', $fullName);
        return strtolower($firstLetter . $arr['1']) . rand(1, 99);
    }

    private function createUserPassword($userName) {
        return password_hash($userName, '2y');
    }

    private function createEmailProvider() {
        $arr = [
            "gmail.com",
            "bluewin.ch",
            "gmx.ch",
            "msn.ch",
            "sunrise.ch"
        ];

        $rand = array_rand($arr);

        return $arr[$rand];
    }

    public function createUser() {
        $gender = $this->createGender();
        $lang = $this->createLanguageLocation();
        $lastName = $this->createLastName($lang);
        $firstName = $this->createFirstName($lang, $gender);
        $fullName = $firstName . " " . $lastName;
        $userName = $this->createUserName($fullName);

        $user = [
            'userID'        => $this->createUserId($lang),
            'username'      => $userName,
            'password'      => $this->createUserPassword($userName),
            'language'      => $lang,
            'first_name'    => $firstName,
            'last_name'     => $lastName . " TEST",
            'full_name'     => $fullName . " TEST",
            'gender'        => $gender,
            'company_id'    => 123456789,
            'org_id'        => 1,
            'is_demo_user'  => 1,
            'is_real_user'  => 0,
            'email'         => $userName . '@' . $this->createEmailProvider(),
            'mut_user'      => 999999
        ];
        return $user;
    }
}

class DummyCorp extends DummyUser {
    public function __construct()
    {

    }

    private function createCompanyID() {
        return rand(101,999);
    }

    private function createCompanyName($arr) {
        $nameArr = $arr['names'];
        $num = count($nameArr);
        return $nameArr[rand(0, $num)];
    }

    private function createOrgStructureType() {
        return 'hierarchy';
    }

    private function createJobTitles($arr) {
        $branchArr = $arr['branches'];
        $hierarchyArr = $arr['hierarchy'];
        $numBranches = count($branchArr);
        $numHierarchy = count($hierarchyArr);
        $titleArr = [];
        foreach ($branchArr as $branch) {
            if ($branch == 'Executive') {
                for ($i = 0; $i < $numBranches; $i++) {
                    $titleArr[] = [
                        'title' => $hierarchyArr[0] . " " . $branchArr[$i] . " Officer"
                    ] ;
                }
            }
            if ($branch !== 'Executive') {
                for ($j = 1; $j < $numHierarchy; $j++) {
                    switch ($hierarchyArr[$j]) {
                        case "President":
                        case "Vice President":
                        case "Director":
                        $titleArr[] = [
                                'title' => $hierarchyArr[$j] . " of " . $branch
                            ];
                            break;
                        default:
                            $titleArr[] = [
                                'title' => $branch . " " . $hierarchyArr[$j]
                            ];
                    }
                }
            }
        }
        return $titleArr;
    }

    private function getEmployeePositionLimit($arr) {
        $jobs = $this->createJobTitles($arr);
        foreach ($jobs as $key => $job) {
            foreach ($job as $title) {
                $jobs[$key]['acronym'] = $this->createTitleAcronym($job['title']);
                if (strpos($job['title'], 'Associate')) {
                    $jobs[$key]['empLimit'] = 7;
                } else {
                    $jobs[$key]['empLimit'] = 1;
                }
            }
        }
        return $jobs;
    }

    private function createTitleAcronym($title) {
        $words = explode(" ", $title);
        $acronym = "";
        foreach ($words as $word) {
            if (!(strlen($word) < 3)) {
                $acronym .= mb_substr($word, 0, 1);
            }
        }
        return $acronym;
    }


    public function createCompany() {
        $arr = $this->createArrayFromJSON('company');
        $companyID = $this->createCompanyID();
        $name = $this->createCompanyName($arr);
        $structure = $this->createOrgStructureType();
        $limits = $this->getEmployeePositionLimit($arr);
        print_r($limits);
    }


}


/*$dummy = new DummyUser();
$dummy->createCompany();*/
$dummyCo = new DummyCorp();
$dummyCo->createCompany();