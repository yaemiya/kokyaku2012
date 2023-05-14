<?php
require_once "../parts/header.php";
require dirname(__FILE__) . '/../db.php';
require '../validation.php';
?>

<link rel="stylesheet" href="../css/login.css">

<?php
require_once "../parts/subheader.php";

$msgs = array();
$check = null;
$pwdConfirm = null;

if (isset($_POST['insert'])) {
    //社員名有無
    $empName = $_POST['employeeName'];
    $empName = preg_replace('/^[ 　]+/u', '', $empName);
    $empName = preg_replace('/[ 　]+$/u', '', $empName);
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

    //問題なければ登録処理
    if ($check !== 0) {
        try {
            $sql = 'Insert into employee (employee_name, mail, pwd, role) Values (:employeeName, :mail, :pwd, "営業")';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':employeeName', $_POST['employeeName']);
            $stmt->bindParam(':mail', $_POST['mail']);
            $pwdHash = password_hash($_POST['pwd'], PASSWORD_DEFAULT);//パスワードのハッシュ化
            $stmt->bindParam(':pwd', $pwdHash);
            $stmt->execute();
            $stmt->bindParam(':pwd', $pwdHash);
            $msgs[] = '登録しました';
        } catch (PDOException $Exception) {
            die('接続エラー：' . $Exception->getMessage());
        }
    }
}
?>

<div class="container">
  <h4 class="col-sm-12">営業社員登録画面</h4>
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
    <br>

    <div class="form-group row">
    <div class="col-sm-2"></div>
      <div class="col-sm-8">
        <button type="submit" class="btn btn-success btn-block" name="insert" onclick="return confirm('新規登録します。よろしいですか？')">新規登録</button>
      </div>
    </div>
  </form>

  <div class="shinki col-sm-10">
    <a href="login.php">ログイン画面へ戻る</a>
  </div>
</div>

<?php
require_once "../parts/footer.php";
?>