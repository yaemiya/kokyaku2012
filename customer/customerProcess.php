<?php

require dirname(__FILE__) . '/../db.php';

function customerList($pdo)
{
    try {
        $sql = 'SELECT * FROM customer INNER JOIN employee on employee.employee_id = customer.employee_id ORDER BY customer_id';
        $stmt = $pdo->query($sql);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $list;
    } catch (PDOException $Exception) {
        die('接続エラー：' . $Exception->getMessage());
    }
}

function customerSearch($pdo)
{
    try {
        //検索パラメータの格納
        $searches = $_GET['search'];
        //全角スペースを半角スペースに変換
        $searchesSpaceHankaku = str_replace("　", " ", $searches);
        //空白で区切って配列に格納
        $searchArray = explode(" ", $searchesSpaceHankaku);
        $count = count($searchArray);
        foreach ($searchArray as $search) {
            $likeSearch = '"%'.$search.'%"';
            $whereArray[] = '((customer_id like '.$likeSearch.')
            OR (customer_name like '.$likeSearch.')
            OR (tel like '.$likeSearch.')
            OR (gender like '.$likeSearch.')
            OR (customer.employee_id like '.$likeSearch.')
            OR (employee.employee_name like '.$likeSearch.'))';
            $likeSearchArray[] = '%'.$search.'%';
        }
        $sql = 'SELECT * FROM customer INNER JOIN employee on employee.employee_id = customer.employee_id where' . implode(" AND ", $whereArray) . ' ORDER BY customer_id';
        $stmt = $pdo->query($sql);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $list;
    } catch (PDOException $Exception) {
        die('接続エラー：' . $Exception->getMessage());
    }
}

function insertCustomer($pdo)
{
    //入力チェック
    $check = null;
    //顧客名
    $custName = $_POST['customerName'];
    //先頭全半角スペースを空文字変換
    $custName = preg_replace('/^[ 　]+/u', '', $custName);
    //最後全半角スペースの空文字変換
    $custName = preg_replace('/[ 　]+$/u', '', $custName);
    //存在
    if (empty($custName)) {
        $msgs[] = custNameErr();
        $check = 0;
    }

    //電話番号
    $tel = $_POST['tel'];
    //先頭全半角スペースを空文字変換
    $tel = preg_replace('/^[ 　]+/u', '', $tel);
    //最後全半角スペースの空文字変換
    $tel = preg_replace('/[ 　]+$/u', '', $tel);
    //存在
    if (empty($tel)) {
        $msgs[] = telErr();
        $check = 0;
    //形式
    } elseif (!preg_match('/^(0{1}\d{9,10})$/', $tel)) {
        $msgs[] = telErr();
        $check = 0;
    }

    //性別
    if (!isset($_POST['gender'])) {
        $msgs[] = genderErr();
        $check = 0;
    }

    //担当営業
    if ($_POST['employeeId'] === '0') {
        $msgs[] = tantoErr();
        $check = 0;
    }

    //問題なければ登録処理実行
    if ($check !== 0) {
        try {
            $sql = 'Insert into customer (customer_name, tel, gender, employee_id) Values (:customerName, :tel, :gender, :employeeId)';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':customerName', $_POST['customerName']);
            $stmt->bindParam(':tel', $_POST['tel']);
            $stmt->bindParam(':gender', $_POST['gender']);
            $stmt->bindParam(':employeeId', $_POST['employeeId']);
            $stmt->execute();
            $msgs[] = "登録しました";
        } catch (PDOException $Exception) {
            die('接続エラー：' . $Exception->getMessage());
        }
    }
    return $msgs;
}

function updateCustomer($pdo)
{
    $check = null;
    $updateErr = null;

    //入力チェック
    //顧客名
    $custName = $_POST['customerName'];
    //先頭全半角スペースを空文字変換
    $custName = preg_replace('/^[ 　]+/u', '', $custName);
    //最後全半角スペースの空文字変換
    $custName = preg_replace('/[ 　]+$/u', '', $custName);
    //存在
    if (empty($custName)) {
        $msgs[] = custNameErr();
        $check = 0;
    }

    //電話番号
    $tel = $_POST['tel'];
    //先頭全半角スペースを空文字変換
    $tel = preg_replace('/^[ 　]+/u', '', $tel);
    //最後全半角スペースの空文字変換
    $tel = preg_replace('/[ 　]+$/u', '', $tel);
    //存在
    if (empty($tel)) {
        $msgs[] = telErr();
        $check = 0;
    //形式
    } elseif (!preg_match('/^(0{1}\d{9,10})$/', $tel)) {
        $msgs[] = telErr();
        $check = 0;
    }

    //問題なければ更新処理実行
    if ($check !== 0) {
        try {
            $sql = 'update customer set customer_name = :customerName, tel = :tel, gender = :gender, employee_id = :employeeId where customer_id = :customerId';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':customerName', $_POST['customerName']);
            $stmt->bindParam(':tel', $_POST['tel']);
            $stmt->bindParam(':gender', $_POST['gender']);
            $stmt->bindParam(':employeeId', $_POST['employeeId']);
            $stmt->bindParam(':customerId', $_POST['customerId']);
            $stmt->execute();
            $msgs[] ="更新しました";
        } catch (PDOException $Exception) {
            die('接続エラー：' . $Exception->getMessage());
        }
    } else {
        $updateErr = 0;
    }
    return [$msgs, $updateErr];
}

function deleteCustomer($pdo)
{
    try {
        $sql = 'delete from customer where customer_id = :customerId';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':customerId', $_POST['customerId']);
        $stmt->execute();
        $msgs[] = "削除しました";
        return $msgs;
    } catch (PDOException $Exception) {
        die('接続エラー：' . $Exception->getMessage());
    }
}

function tantoEigyo($pdo)
{
    $sqlList = 'SELECT employee_id, employee_name FROM employee';
    $stmt = $pdo->query($sqlList);
    $empList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $empList;
}
