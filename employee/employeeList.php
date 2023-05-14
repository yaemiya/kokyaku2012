<?php
session_start();

require_once "../parts/header.php";
require dirname(__FILE__) . '/../db.php';
require '../validation.php';

$msgs = array();

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

//削除処理
if (isset($_POST['delete'])) {
    try {
        $sql = 'delete from employee where employee_id = :employeeId';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':employeeId', $_POST['employeeId']);
        $stmt->execute();
        $msgs[] = "削除しました";
        // header("Location: employeeList.php") ;
        // exit ;
    } catch (PDOException $Exception) {
        die('接続エラー：' . $Exception->getMessage());
    }
}
?>

<link rel="stylesheet" href="../css/employee.css">

<?php
require_once "../parts/subheader.php";
require_once "../parts/menubar.php";

try {
    $sql = 'SELECT * FROM employee ORDER BY employee_id';
    $stmt = $pdo->query($sql);
    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $Exception) {
    die('接続エラー：' . $Exception->getMessage());
}
?>

<div class="container">
<br>
    <!-- メッセージ -->
 <p class="msg">
    <?php if ($msgs != null) {
    foreach ($msgs as $msg) {
        echo $msg."<br>";
    }
} ?></p>
<br>
    <h4 class="col-sm-12">社員一覧（管理用）</h4>
    <br>
    <div class="row">
        <div class="col-sm-12">
            <table class="table detailTable">
                <thead>
                    <tr>
                        <td>社員ID</td>
                        <td>社員名</td>
                        <td>役職</td>
                        <td>メールアドレス</td>
                        <td></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($list as $row) {
                        ?>
                        <form action="employeeDetail.php" method="post">
                        <tr>
                        <td><?php echo $row['employee_id']; ?></td>
                        <td><?php echo h($row['employee_name']); ?></td>
                        <td><?php echo h($row['role']); ?></td>
                        <td><?php echo h($row['mail']); ?></td>
                        <td>
                            <button type="submit" class="btn btn-info btn-sm">詳細</button>
                            <input type="hidden" name="employeeId" value="<?php echo $row['employee_id'] ?>">
                            <input type="hidden" name="employeeName" value="<?php echo $row['employee_name'] ?>">
                            <input type="hidden" name="role" value="<?php echo $row['role'] ?>">
                            <input type="hidden" name="mail" value="<?php echo $row['mail'] ?>">
                            </td>
                        </tr>
                        </form>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            <br>
            <div class="form-group row">
            <div class="col-sm-1"></div>
            <div class="col-sm-10">
            <form action="addEmployee.php">
                <button type="submit" class="btn btn-success btn-block" name="create">新規登録</button>
            </form>
            </div>
        </div>

        </div>
    </div>
</div>

<?php
require_once "../parts/footer.php";
?>