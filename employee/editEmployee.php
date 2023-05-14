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

if (isset($_POST['edit'])) {
    //DBより役職配列の作成
    $sql = 'SELECT distinct role FROM employee';
    $stmt = $pdo->query($sql);
    $roleArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //DBより顧客配列の作成
    $sql = 'SELECT customer_id, customer_name FROM customer';
    $stmt = $pdo->query($sql);
    $custArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //DBより担当営業配列の作成
    $sql = 'SELECT customer_id, customer_name FROM customer inner join employee on employee.employee_id = customer.employee_id where employee.employee_id = :employeeId;';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':employeeId', $_POST['employeeId']);
    $stmt->execute();
    $tantoArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php
require_once "../parts/menubar.php";
?>

<div class="container">

    <h4 class="col-sm-12">社員編集（管理用）</h4>
    <br>
    <!-- メッセージ -->
 <p class="msg">
    <?php if ($msgs != null) {
    foreach ($msgs as $msg) {
        echo $msg."<br>";
    }
} ?></p>
    <hr>

    <form method="post" action="employeeDetail.php">
            <div class="form-group row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                    <div class="form-group">
                        <label for="employeeId">社員ID</label>
                        <input type="text" class="form-control" id="employeeId" name="employeeId" value="<?php if (!empty($_POST['employeeId'])) {
    echo $_POST['employeeId'];
} ?>" readonly>
</div>
                </div>
            </div>

        <div class="form-group row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <label for="employeeName">社員名</label>
                <input type="text" class="form-control" id="employeeName" name="employeeName" value="<?php if (!empty($_POST['employeeName'])) {
    echo h($_POST['employeeName']);
} ?>">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <label for="employeeMail">メールアドレス</label>
                <input type="text" class="form-control" id="mail" name="mail" value="<?php if (!empty($_POST['mail'])) {
    echo h($_POST['mail']);
} ?>">
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
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <label for="role">役職</label>
                <select class="custom-select custom-select-sm" id="role" name="role">
                <div class="col-sm-2"></div>
            <div class="col-sm-8">
                        <?php
                        if (!empty($_POST['role'])) {
                            foreach ($roleArray as $role) {
                                if ($_POST['role'] === $role['role']) {
                                    ?>
                                    <option value=<?php echo $role['role'] ?> selected><?php echo $role['role']; ?></option>
                                 <?php
                                }
                                if ($_POST['role'] !== $role['role']) {
                                    ?>
                                        <option value=<?php echo $role['role'] ?>><?php echo $role['role']; ?></option>
                        <?php
                                }
                            }
                        } ?>

                    </select>
            </div>
        </div>

        <br>

        <div class="form-group row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <button type="submit" class="btn btn-primary btn-block" name="update" onclick="return confirm('更新します。よろしいですか？')">更新</button>
            </div>
        </div>
    </form>
    <br>
    <br>
    <div class="form-group row">
        <div class="col-sm-7"></div>
        <div class="shinki col-sm-5">
            <form method="post" name="empDetailBack" action="employeeDetail.php">
                <input type="hidden" name="employeeId" value="<?php echo $_POST['employeeId'] ?>">
                <input type="hidden" name="employeeName" value="<?php echo $_POST['employeeName'] ?>">
                <input type="hidden" name="role" value="<?php echo $_POST['role'] ?>">
                <input type="hidden" name="mail" value="<?php echo $_POST['mail'] ?>">
                <a href="javascript:empDetailBack.submit()">社員詳細画面へ戻る</a>
            </form>
         </div>
    </div>
</div>

<?php
require_once "../parts/footer.php";
?>