<?php
session_start();
require_once "../parts/header.php";
require dirname(__FILE__) . '/../db.php';
require '../validation.php';

$msgs = array();
$check = null;

//認証
if (!isset($_SESSION['empId'])) {
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

//更新処理
//入力チェック
if (isset($_POST['update'])) {
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
    //パスワード
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
    
    //問題なければ更新処理
    if ($check !== 0) {
        try {
            $sql = 'update employee set employee_name = :employeeName, role = :role, mail = :mail, pwd = :pwd where employee_id = :employeeId';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':employeeName', $_POST['employeeName']);
            $stmt->bindParam(':role', $_POST['role']);
            $stmt->bindParam(':mail', $_POST['mail']);
            $stmt->bindParam(':pwd', password_hash($_POST['pwd'], PASSWORD_DEFAULT));//パスワードのハッシュ化
            $stmt->bindParam(':employeeId', $_POST['employeeId']);
            $stmt->execute();
            $msgs[] = "更新しました";
        } catch (PDOException $Exception) {
            die('接続エラー：' . $Exception->getMessage());
        }
    }
}

//削除画面へ遷移
if (isset($_POST['delete'])) {
    header("Location: employeeList.php");
}

//詳細表示処理
try {
    $sql = 'SELECT customer_id, customer_name FROM customer INNER JOIN employee on employee.employee_id = customer.employee_id where employee.employee_id = :employeeId';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':employeeId', $_POST['employeeId']);
    $stmt->execute();
    $tantoArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $employeeId = $_POST['employeeId'];
} catch (PDOException $Exception) {
    die('接続エラー：' . $Exception->getMessage());
}

require_once "../parts/menubar.php";
?>
</div>
<div class="container">
  <!-- メッセージ -->
 <p class="msg">
    <?php if ($msgs != null) {
    foreach ($msgs as $msg) {
        echo $msg."<br>";
    }
} ?></p>
<br>
    <h4 class="col-sm-12">社員詳細（管理用）</h4>
    <br>
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <table class="table detailTable">
                <tbody>
                    <tr>
                        <td>社員ID</td>
                        <td><?php if (!empty($_POST['employeeId'])) {
    echo $_POST['employeeId'];
} ?></td>
                    </tr>
                    <tr>
                        <td>社員名</td>
                        <td><?php if (!empty($_POST['employeeName'])) {
    echo h($_POST['employeeName']);
} ?></td>
                    </tr>
                    <tr>
                        <td>メールアドレス</td>
                        <td><?php if (!empty($_POST['mail'])) {
    echo h($_POST['mail']);
} ?></td>
                    </tr>
                    <tr>
                        <td>役職</td>
                        <td><?php if (!empty($_POST['role'])) {
    echo $_POST['role'];
} ?></td>
                    </tr>
                    <tr>
                        <td>担当顧客</td>
                        <td>
                        <?php
                        foreach ($tantoArray as $tanto) {
                            ?>
                            <p>
                            <?php  echo $tanto['customer_id']; ?>：<?php echo h($tanto['customer_name']) ?></p>
                        <?php
                        } ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <br>

    <div class="form-group row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
        <form method="post" action="editEmployee.php">
            <button type="submit" class="btn btn-info btn-block" name="edit">編集</button>
            <input type="hidden" name="employeeId" value="<?php echo $employeeId ?>">
            <input type="hidden" name="employeeName" value="<?php if (isset($_POST['employeeName'])) {
                            echo $_POST['employeeName'];
                        } ?>">
            <input type="hidden" name="role" value="<?php if (isset($_POST['role'])) {
                            echo $_POST['role'];
                        } ?>">
            <input type="hidden" name="mail" value="<?php if (isset($_POST['mail'])) {
                            echo $_POST['mail'];
                        } ?>">
        </form>
        </div>
    </div>

  <div class="form-group row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
            <form method="post" action="employeeList.php">
                <button type="submit" name="delete" value="削除しました" class="btn btn-danger btn-block" onclick="return confirm('削除します。よろしいですか？');">削除</button>
                <input type="hidden" name="employeeId" value="<?php echo $employeeId ?>">
             </form>
            </div>
        </div>

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