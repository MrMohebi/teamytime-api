<?php
require_once "../configs/index.php";

if (isset($client) && isset($_GET["userID"])) {
    $reportsCollection = $client->selectCollection($_ENV['DB_NAME'], 'reports');
    $reports = $reportsCollection->find(["userID"=>$_GET["userID"]], ["sort"=>array('jalaliDate' => -1), "limit"=>5])->toArray();

    for ($i = 0; $i < count($reports); $i++){
        $c = json_decode(json_encode($reports[$i]),true);
        unset($reports[$i]->_id);
        $reports[$i]->id = $c["_id"]['$oid'];
    }

    exit(json_encode($reports));
}
exit("400");
