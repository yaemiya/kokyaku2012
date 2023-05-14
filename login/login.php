<?php
require_once "../parts/header.php";
require dirname(__FILE__) . '/../db.php';
require '../validation.php';
?>

<link rel="stylesheet" href="../css/login.css">

<?php
require_once "../parts/subheader.php";

session_start();
$check = null;
if (isset($_POST['login'])) {
    //アドレス有無
    if (empty($_POST["mail"])) {
        $msg = loginErr();
        $check = 0;
    }
    //パスワード有無
    if (empty($_POST["pwd"])) {
        $msg = loginErr();
        $check = 0;
    }
    //メールアドレス形式
    if (!preg_match('/^([\w])+([\w\._-])*\@([\w])+([\w\._-])*\.([a-zA-Z])+$/', $_POST['mail'])) {
        $msg = loginErr();
        $check = 0;
    }
    //パスワード桁数＆形式
    $pwd = $_POST['pwd'];
    //半角英数字かどうか
    if (!preg_match("/^[a-zA-Z0-9]+$/", $pwd)) {
        $msg = loginErr();
        $check = 0;
    }
    //桁数
    if (mb_strlen($pwd) < 8 || mb_strlen($pwd) > 16) {
        $msg = loginErr();
        $check = 0;
    }

    //問題なければログイン処理
    if ($check !== 0) {
        try {
            //メールアドレスから登録確認
            $sql = 'SELECT * FROM employee where mail = :mail';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':mail', $_POST['mail']);
            $stmt->execute();
            $emp = $stmt->fetch(PDO::FETCH_ASSOC);
            //パスワードの登録確認
            if (password_verify($_POST['pwd'], $emp['pwd'])) {
                //セッションに社員ID・社員名・役職を格納
                $_SESSION['empId'] = $emp['employee_id'];
                $_SESSION['empName'] = $emp['employee_name'];
                $_SESSION['role'] = $emp['role'];
                header("Location: ../customer/customerList.php");
            }
        } catch (PDOException $Exception) {
            die('接続エラー：' . $Exception->getMessage());
        }
    }
}
?>

<div class="container">
  <h4 class="col-sm-12">社員ログイン画面</h4>
  <hr>

  <form method="post">
    <div class="form-group row">
      <div class="col-sm-2"></div>
      <div class="col-sm-8">
        <label for="mail">メールアドレス</label>
        <input type="text" class="form-control" placeholder="aaa@gliss.com" name="mail" id="mail">
      </div>
    </div>
    <br>
    <div class="form-group row">
      <div class="col-sm-2"></div>
      <div class="col-sm-8">
        <label for="pwd">パスワード</label>
        <input type="password" class="form-control" placeholder="8文字以上16文字以下の半角英数字" name="pwd" id="pwd">
      </div>
    </div>
    <br>
    <div class="form-group row">
      <div class="col-sm-2"></div>
      <div class="col-sm-8">
        <button type="submit" class="btn btn-info btn-block" name="login">ログインする</button>
      </div>
    </div>
  </form>

  <div class="shinki col-sm-10">
    <a href="addRegEmployee.php">新規登録はこちら</a>
  </div>
<br>
  <p class="msg"><?php if (isset($msg)) {
    echo $msg;
}  ?> </p>
</div>
<div class="form-group row">
  <div class="col-sm-4"></div>
  <a href="#loginData">ログイン用データはこちらです</a>
</div>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<div class="row">
  <div class="col-sm-3"></div>
  <br>
  <div class="col-sm-6">
    <h5 class="loginData"id="loginData">ログイン用データ</h5>
    <table class="table">
      <thead>
        <tr>
          <th scope="col">社員名</th>
          <th scope="col">役職</th>
          <th scope="col">メールアドレス</th>
          <th scope="col">パスワード</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>営業部長A</td>
          <td>営業部長</td>
          <td>aaa@gliss.com</td>
          <td>password</td>
        </tr>
        <tr>
          <td>営業部長B</td>
          <td>営業部長</td>
          <td>bbb@gliss.com</td>
          <td>password</td>
        </tr>
        <tr>
          <td>営業C</td>
          <td>営業</td>
          <td>ccc@gliss.com</td>
          <td>password</td>
        </tr>
        <tr>
          <td>営業D</td>
          <td>営業</td>
          <td>ddd@gliss.com</td>
          <td>password</td>
        </tr>
        <tr>
          <td>営業E</td>
          <td>営業</td>
          <td>eee@gliss.com</td>
          <td>password</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<?php
require_once "../parts/footer.php";
?>