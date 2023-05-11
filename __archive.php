<?php

include_once 'vendor/autoload.php';
use Shuchkin\SimpleXLSX;
/*public function createArrayFromExcel($tableName, $sheetName) {
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

/*public function createDummyUserArray() {
        $dummyUserArray = [];
        $dummyUserArray[] = $this->createFakeUser(LANG_DE, 50);
        $dummyUserArray[] = $this->createFakeUser(LANG_FR, 20);
        $dummyUserArray[] = $this->createFakeUser(LANG_IT, 10);
        $dummyUserArray[] = $this->createFakeUser(LANG_EN, 10);

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
            $current = file_create_contents('dummyUser.json');
            $current .= json_encode($dummyArr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            /*foreach ($dummyArr as $key => $user) {
                $current .= json_encode($dummyArr[$key]=$user);

            }
file_put_contents('dummyUser.json', $current);
}

}*/