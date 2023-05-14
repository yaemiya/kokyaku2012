<?php
session_start();
require_once "../parts/header.php";
require dirname(__FILE__) . '/../db.php';
require '../validation.php';

$msgs = array();
$check = null;
$pwdConfirm = null;

//認証
if (!isset($_SESSION['empId']) && $_SESSION['role']==='営業部長') {
    header("Location: ../login/login.php");
}
if (!$_SESSION['role']==='営業部長') {
    header("Location: ../login/login.php");
}

//ログアウト
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['empId']);
    unset($_SESSION['empName']);
    unset($_SESSION['role']);
    header("Location: ../login/login.php");
}
?>

<link rel="stylesheet" href="../css/employee.css">

<?php
require_once "../parts/subheader.php";
require_once "../parts/menubar.php";

// 入力チェック
if (isset($_POST['insert'])) {
    //社員名有無
    $empName = $_POST['employeeName'];
    //先頭全半角スペースを空文字変換
    $empName = preg_replace('/^[ 　]+/u', '', $empName);
    //最後全半角スペースの空文字変換
    $empName = preg_replace('/[ 　]+$/u', '', $empName);
    //存在
    if (empty($empName)) {
        $msgs[] = empNameErr();
        $check = 0;
    }

    //メールアドレス有無
    $mail = $_POST['mail'];
    //先頭全半角スペースを空文字変換
    $mail = preg_replace('/^[ 　]+/u', '', $mail);
    //最後全半角スペースの空文字変換
    $mail = preg_replace('/[ 　]+$/u', '', $mail);
    //存在
    if (empty($mail)) {
        $msgs[] = mailErr();
        $check = 0;
    //メールアドレス形式
    } elseif (!preg_match('/^([\w])+([\w\._-])*\@([\w])+([\w\._-])*\.([a-zA-Z])+$/', $mail)) {
        $msgs[] = mailErr();
        $check = 0;
    }
    //パスワード桁数＆形式
    $pwd = $_POST['pwd'];
    $pwdConfirm = $_POST['pwdConfirm'];
    //タイプミス
    if ($pwd !== $pwdConfirm) {
        $msgs[] = pwdErr();
        $check = 0;
    //桁数
    } elseif (mb_strlen($pwd) < 8 || mb_strlen($pwd) > 16) {
        $msgs[] = pwdErr();
        $check = 0;
    //半角英数字かどうか
    } elseif (!preg_match("/^[a-zA-Z0-9]+$/", $pwd)) {
        $msgs[] = pwdErr();
        $check = 0;
    }

    //役職
    if ($_POST['role'] === '0') {
        $msgs[] = roleErr();
    }

    //問題なければ登録処理
    if ($check !== 0) {
        try {
            $sql = 'Insert into employee (employee_name, role, mail, pwd) Values (:employeeName, :role, :mail, :pwd)';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':employeeName', $_POST['employeeName']);
            $stmt->bindParam(':role', $_POST['role']);
            $stmt->bindParam(':mail', $_POST['mail']);
            $pwdHash = password_hash($_POST['pwd'], PASSWORD_DEFAULT);//パスワードのハッシュ化
            $stmt->bindParam(':pwd', $pwdHash);
            $stmt->execute();
            $msgs[] = '登録しました';
        } catch (PDOException $Exception) {
            die('接続エラー：' . $Exception->getMessage());
        }
    }
}
?>

<div class="container">

    <h4 class="col-sm-12">社員登録（管理用）</h4>
    <br>
<!-- メッセージ -->
 <p class="msg">
    <?php if ($msgs != null) {
    foreach ($msgs as $msg) {
        echo $msg."<br>";
    }
} ?></p>
  <hr>

    <form method="post">
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <label for="empName">社員名</label>
                <input type="text" class="form-control"  name="employeeName" placeholder="営業部長A" id="empName">
            </div>
        </div>
        
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
            <label for="mail">メールアドレス</label>
            <input type="text" class="form-control" placeholder="aaa@gliss.com" name="mail" id="mail">
            </div>
        </div>
        
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
            <label for="pwd">パスワード</label>
            <input type="password" class="form-control" placeholder="8文字以上16文字以下の半角英数字" name="pwd" id="pwd">
            </div>
        </div>

<div class="form-group row">
    <div class="col-sm-2"></div>
    <div class="col-sm-8">
    <label for="pwd">パスワード（確認用）</label>
    <input type="password" class="form-control" placeholder="8文字以上16文字以下の半角英数字" name="pwdConfirm" id="pwd">
    </div>
</div>
        
        <div class="form-group select row">
            <div class="col-sm-3"></div>
            <select class="custom-select custom-select-sm col-sm-6" name="role">
                <option selected value="0">役職を選択してください</option>
                <option value="営業部長">営業部長</option>
                <option value="営業">営業</option>
            </select>
        </div>
        <br>
        
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                    <button type="submit" class="btn btn-success btn-block" name="insert" onclick="return confirm('新規登録します。よろしいですか？')">新規登録</button>
            </div>
        </div>
    </form>
    
    <br>
    <div class="form-group row">
        <div class="col-sm-7"></div>
        <div class="shinki col-sm-5">
            <a href="employeeList.php">社員一覧画面へ戻る</a>
        </div>
    </div>
</div>

<?php
require_once "../parts/footer.php";
?>