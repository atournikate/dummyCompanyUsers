<?php
require_once 'Dummy.php';

class dummyCompany extends Dummy
{
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

    /*public function writeJSONtoSQLInsert($table) {
        $json = file_get_contents('dummyCompany.json');
        $data = json_decode($json, true);

        $sql = "INSERT INTO $table (user_id, username, job_title, acronym, first_name, last_name, full_name, `language`, gender, address, personal_email, company_email, `password`, is_demo_user, is_real_user, mut_user) VALUES ";

        $empList = $data['employee_list'];
        foreach ($empList as $emp) {
            $sql .= "('" . $emp['user_id'] . "', '" . $emp['username'] . "', '" . $emp['job_title'] . "', '" . $emp['acronym'] . "', '" . $emp['first_name'] . "', '" . $emp['last_name'] . "', '" . $emp['full_name'] . "', '" . $emp['language'] . "', '" . $emp['gender'] . "', '" . $emp['address'] . "', '" . $emp['personal_email'] . "', '" . $emp['email'] . "', '" . $emp['password'] . "', '" . $emp['is_demo_user'] . "', '" . $emp['is_real_user'] . "', '" . $emp['mut_user'] . "'), \n";


        }
        $newSQLFile = 'dummyEmp.sql';
        if (!file_exists($newSQLFile)) {
            fopen('dummyEmp.sql', 'w+');
        }

        file_put_contents('dummyEmp.sql', $sql);
    }*/
}