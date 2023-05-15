<?php

const LANG_DE = 'de';
const LANG_EN = 'en';
const LANG_FR = 'fr';
const LANG_IT = 'it';

class DummyUser {
    const FILE_TO_JSON  = "json/";
    const GENDER_FEMALE = "f";
    const GENDER_MALE   = "m";
    const GENDER_NB     = "nb";
    const FIRST_NAMES       = "names";
    const SURNAMES          = "_surnames";
    const FIRSTNAMES_FEMALE = "_" . self::GENDER_FEMALE . "_" . self::FIRST_NAMES;
    const FIRSTNAMES_MALE   = "_" . self::GENDER_MALE . "_" . self::FIRST_NAMES;
    const FIRSTNAMES_NB   = self::GENDER_NB . "_" . self::FIRST_NAMES;

    const LANG_CODE_DE  = 100;
    const LANG_CODE_FR  = 200;
    const LANG_CODE_IT  = 300;
    const LANG_CODE_EN  = 400;

    public function __construct() {
    }

    public function createArrayFromJSON($fileName) {
        $file = file_get_contents(self::FILE_TO_JSON . $fileName . ".json");
        return json_decode($file, true);
    }

    public function getRandomValueFromArray($array) {
        $key = array_rand($array, 1);
        return $array[$key];
    }

    public function updateArray($array, $fieldName, $value) {
        $array[$fieldName] = $value;
        return $array;
    }

    private function createSurnameArray($lang) {
        $arr = $this->createArrayFromJSON($lang . self::SURNAMES);
        return $arr['last_names'];
    }
    private function createMaleNameArray($lang) {
        $arr = $this->createArrayFromJSON( $lang . self::FIRSTNAMES_MALE);
        return $arr['first_names'];
    }
    private function createFemaleNameArray($lang) {
        $arr = $this->createArrayFromJSON( $lang . self::FIRSTNAMES_FEMALE);
        return $arr['first_names'];
    }
    private function createNonBinaryNameArray() {
        $arr = $this->createArrayFromJSON( self::FIRSTNAMES_NB);
        return $arr['first_names'];
    }
    private function createGender() {
        $rand = rand(0,10);
        if ($rand == 0 || $rand == 1) {
            $gender = self::GENDER_NB;
        } elseif ($rand <= 6) {
            $gender = self::GENDER_FEMALE;

        } else {
            $gender = self::GENDER_MALE;

        }
        return $gender;
    }
    private function createLastName($lang) {
        $arr = $this->createSurnameArray($lang);
        $value = $this->getRandomValueFromArray($arr);
        return ucfirst(strtolower($value));
    }
    private function createFirstName($lang, $gender) {
        if ($gender == 'f') {
            $arr = $this->createFemaleNameArray($lang);
        } elseif ($gender == 'm') {
            $arr = $this->createMaleNameArray($lang);
        } else {
            $arr = $this->createNonBinaryNameArray();
        }
        $value = $this->getRandomValueFromArray($arr);

        return ucfirst(strtolower($value));
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
            $pre = self::LANG_CODE_EN;
        } elseif ($lang == LANG_IT) {
            $pre = self::LANG_CODE_IT;
        } elseif ($lang == LANG_FR) {
            $pre = self::LANG_CODE_FR;
        } else {
            $pre = self::LANG_CODE_DE;
        }
        $rand = rand(1000, 9999);
        return $pre . $rand;
    }

    private function replaceSpecialCharacters($fullName) {
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
        $arr = [ "gmail.com", "bluewin.ch", "gmx.ch", "msn.ch", "sunrise.ch" ];
        $rand = array_rand($arr);
        return $arr[$rand];
    }

    public function createAddress() {
        $arr = $this->createArrayFromJSON('streets');
        foreach ($arr as $streets) {
            $street = $this->getRandomValueFromArray($streets);
        }
        $houseNum = rand(1, 99);
        return $street . " " . $houseNum;
    }

    public function createUser() {
        $gender = $this->createGender();
        $lang = $this->createLanguageLocation();
        $lastName = $this->createLastName($lang);
        $firstName = $this->createFirstName($lang, $gender);
        $fullName = $firstName . " " . $lastName;
        $userName = $this->createUserName($fullName);

        $user = [
            'user_id'           => $this->createUserId($lang),
            'username'          => $userName,
            'job_title'         => null,
            'acronym'           => null,
            'first_name'        => $firstName,
            'last_name'         => $lastName,
            'full_name'         => $fullName,
            'language'          => $lang,
            'gender'            => $gender,
            'address'           => $this->createAddress(),
            'personal_email'    => $userName . '@' . $this->createEmailProvider(),
            'email'             => null,
            'password'          => $this->createUserPassword($userName),
            'is_demo_user'      => 1,
            'is_real_user'      => 0,
            'mut_user'          => 999999
        ];

        return $user;
    }
}

class DummyCorp extends DummyUser {
    public function __construct()
    {
        parent::__construct();
    }

    private function createCompanyID() {
        return rand(101,999);
    }

    private function createCompanyName($arr) {
        $nameArr = $arr['names'];
        return $this->getRandomValueFromArray($nameArr);
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

    private function createAcronym($title) {
        $words = explode(" ", $title);
        $acronym = "";
        foreach ($words as $word) {
            if (!(strlen($word) < 3)) {
                $acronym .= mb_substr($word, 0, 1);
            }
        }
        return $acronym;
    }

    private function createCompanyEmail($employee, $company) {
        $username = $employee['username'];
        $companyName = $company['company_name'];
        $explode = explode(" ", $companyName);
        $num = count($explode);
        if ($num < 2) {
            $companyEmailSuffix = $companyName;
        } elseif ($num == 2) {
            $companyEmailSuffix = $explode[0];
            if ($explode[1] == 'Corporation') {
                $companyEmailSuffix .= substr($explode[1], 0, 4);
            }
        } else {
            $companyEmailSuffix = '';
            foreach ($explode as $word) {
                if ($word == '&') {
                    $word = "-";
                }
                $companyEmailSuffix .= substr($word, 0, 1);
            }
        }
        $companyEmailSuffix .= ".ch";
        return $username . "@" . strtolower($companyEmailSuffix);
    }

    private function createJobPositions($arr) {
        $jobs = $this->createJobTitles($arr);
        foreach ($jobs as $key => $job) {
            foreach ($job as $title) {
                $jobs[$key]['acronym'] = $this->createAcronym($job['title']);
                if (strpos($job['title'], 'Associate')) {
                    $jobs[$key]['empLimit'] = 7;
                    $jobs[$key]['employees'] = [];
                } else {
                    $jobs[$key]['empLimit'] = 1;
                    $jobs[$key]['employee'] = '';
                }
            }
        }
        return $jobs;
    }

    private function createCompany() {
        $arr = $this->createArrayFromJSON('company');
        $company = [
            'company_id'        => $this->createCompanyID(),
            'company_name'      => $this->createCompanyName($arr),
            'company_structure' => $this->createOrgStructureType(),
            'company_address'   => $this->createAddress(),
            'employee_list'     => []
        ];
        return $company;
    }

    private function addCompanyInfoToEmployee($employee, $acronym, $title, $company) {
        $employee = $this->updateArray($employee, 'job_title', $title);
        $employee = $this->updateArray($employee, 'acronym', $acronym);
        $newEmail = $this->createCompanyEmail($employee, $company);
        $employee = $this->updateArray($employee, 'email', $newEmail);
        return $employee;
    }

    private function createEmployees($company) {
        $arr = $this->createArrayFromJSON('company');
        $jobs = $this->createJobPositions($arr);
        $empList = [];
        foreach ($jobs as $job) {
            $limit = $job['empLimit'];
            $acronym = $job['acronym'];
            $title = $job['title'];
            if ($limit == 1) {
                $employee = $this->createUser();
                $employee = $this->addCompanyInfoToEmployee($employee, $acronym, $title, $company);
                $empList[] = $employee;
            } else {
                for ($i = 0; $i < $limit; $i++) {
                    $employee = $this->createUser();
                    if (!in_array($employee['full_name'], $empList)) {
                        $employee = $this->addCompanyInfoToEmployee($employee, $acronym, $title, $company);
                        $empList[] = $employee;
                    } else {
                        $i--;
                    }
                }
            }
        }
        return $empList;
    }

    public function buildOrganization() {
        $company = $this->createCompany();
        $employees = $this->createEmployees($company);
        $company = $this->updateArray($company, 'employee_list', $employees);
        return $company;
    }

    public function writeDummyDataToJSON()
    {
        $company = $this->buildOrganization();
        $jsonFile = 'dummyCompany.json';
        if (!file_exists($jsonFile)) {
            fopen('dummyCompany.json', 'w+');
        }
        $current = '';
        $current .= json_encode($company, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        file_put_contents('dummyCompany.json', $current);
    }

    public function writeJSONtoSQLInsert() {
        $json = file_get_contents('dummyCompany.json');
        $data = json_decode($json, true);

        $sql = "INSERT INTO sys_emp (user_id, username, job_title, acronym, first_name, last_name, full_name, `language`, gender, address, personal_email, company_email, `password`, is_demo_user, is_real_user, mut_user) VALUES ";

        $empList = $data['employee_list'];
        foreach ($empList as $emp) {
            $sql .= "('" . $emp['user_id'] . "', '" . $emp['username'] . "', '" . $emp['job_title'] . "', '" . $emp['acronym'] . "', '" . $emp['first_name'] . "', '" . $emp['last_name'] . "', '" . $emp['full_name'] . "', '" . $emp['language'] . "', '" . $emp['gender'] . "', '" . $emp['address'] . "', '" . $emp['personal_email'] . "', '" . $emp['email'] . "', '" . $emp['password'] . "', '" . $emp['is_demo_user'] . "', '" . $emp['is_real_user'] . "', '" . $emp['mut_user'] . "'), \n";


        }
        $newSQLFile = 'dummyEmp.sql';
        if (!file_exists($newSQLFile)) {
            fopen('dummyEmp.sql', 'w+');
        }

        file_put_contents('dummyEmp.sql', $sql);
    }
}


/*$dummy = new DummyUser();
$user = $dummy->createUser();*/

$dummyCo = new DummyCorp();
$dummyCo->writeDummyDataToJSON();
$dummyCo->writeJSONtoSQLInsert();
