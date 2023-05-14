<?php
session_start();
?>
<?php
require dirname(__FILE__) . '/../parts/header.php';
require 'customerProcess.php';
require '../validation.php';

$updateErr = null;
$msgs = null;

//認証
if (!isset($_SESSION['empId'])) {
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


//新規登録
if (isset($_POST['create'])) {
    $msgs =insertCustomer($pdo);
}
//更新
if (isset($_POST['update'])) {
    $msgs = updateCustomer($pdo)[0];
    $updateErr = updateCustomer($pdo)[1];
}
//削除
if (isset($_POST['delete'])) {
    $msgs = deleteCustomer($pdo);
}
//検索
if (isset($_GET['search'])) {
    $list = customerSearch($pdo);
//一覧
} else {
    $list = customerList($pdo);
}

?>

<link rel="stylesheet" href="../css/customer.css">

<?php
require "../parts/subheader.php";
?>

<div class="menubar">
    <ul class="nav justify-content-end">
  <li class="nav-item">
        <?php echo "こんにちは ". $_SESSION['empName'] . " さん　　"; ?>
      </li>

        <?php if ($_SESSION['role']==='営業部長') { ?>
            <li class="nav-item">
            <form name="custList">
            <input type="hidden" name="custList">
            </form>
            <a href="../customer/customerList.php">顧客一覧</a>
            </li>

            <li class="nav-item">
            <form name="empList">
            <input type="hidden" name="empList">
            </form>
            <a href="../employee/employeeList.php">社員一覧</a>
            </li>
        <?php } ?>

        <li class="nav-item">
        <form name="logout">
            <input type="hidden" name="logout">
        </form>
        <a href="javascript:document.logout.submit()">ログアウト</a>
        </li>
    </ul>
</div>

<div class="container">
<?php
if (!isset($_POST['edit']) && $updateErr !== 0) {
    ?>
    <!-- 新規登録フォーム -->
    <h4 class="col-sm-12">顧客登録</h4>

    <form method="post">
        <div class="form-row">
            <div class="col-sm-1"></div>
            <div class="form-group col-sm-2">
                <label for="customerId">顧客ID</label>
                <input type="text" name="customerId" id="customerId" class="form-control" placeholder="自動登録" readonly>
            </div>
            <div class="form-group col-sm-4">
                <label for="customerName">顧客名</label>
                <input type="text" name="customerName" id="customerName" class="form-control" placeholder="例：顧客11">
            </div>
            <div class="form-group col-sm-4">
                <label for="tel">電話番号</label>
                <input type="text" name="tel" id="tel" class="form-control" placeholder="ハイフンなしの半角数字10〜11桁">
            </div>
        </div>

        <div class="form-row">
            <div class="col-sm-2"></div>
            <div class="form-group col-sm-4">
                <label for="gender">性別</label>
                <br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="gender" value="男性">
                    <label class="form-check-label" for="gender">男性　</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="gender" value="女性">
                    <label class="form-check-label" for="gender">女性</label>
                </div>
            </div>
            <div class="form-group col-sm-4 select">
                <label for="role">担当営業</label>
                <select class="custom-select custom-select-sm" id="role" name="employeeId">
                    <?php
                    $empList = tantoEigyo($pdo);
    if ($_SESSION['role'] === '営業') {
        foreach ($empList as $row) {
            if ($_SESSION['empId'] === $row['employee_id']) {
                ?>
                                <option value=<?php echo $row['employee_id'] ?> selected><?php echo $row['employee_id'] . ":" . h($row['employee_name']); ?></option>
                                <?php
            }
            if ($_SESSION['empId'] !== $row['employee_id']) {
                ?>
                                    <option value=<?php echo $row['employee_id'] ?> disabled><?php echo $row['employee_id'] . ":" . h($row['employee_name']); ?></option>
                            <?php
            }
        }
    } ?>

              <?php if ($_SESSION['role'] === '営業部長') { ?>
                        <option value="0">選択してください</option>
                        <?php foreach ($empList as $row) { ?>
                        <option value=<?php echo $row['employee_id']; ?>><?php echo $row['employee_id'] . ":" . $row['employee_name']; ?></option>
                        <?php
                        }
    } ?>
                </select>
             </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <button type="submit" class="btn btn-success btn-block" name = "create" onclick="return confirm('新規登録します。よろしいですか？')">新規登録</button>
            </div>
        </div>
    </form>

<?php
}
if (isset($_POST['edit']) || $updateErr === 0) {
    ?>
    <!-- 編集・更新フォーム -->
        <h4 class="col-sm-12" id="customerUpdate">顧客編集</h4>
        <br>

        <form method="post">
            <div class="form-row">
                <div class="col-sm-1"></div>
                <div class="form-group col-sm-2">
                    <label for="customerId">顧客ID</label>
                    <input type="text" name="customerId" id="customerId" class="form-control" disabled value="<?php if (!empty($_POST['customerId'])) {
        echo $_POST['customerId'];
    } ?>">
                </div>
                <div class="form-group col-sm-4">
                    <label for="customerName">顧客名</label>
                    <input type="text" name="customerName" id="customerName" class="form-control" placeholder="例：顧客11" value="<?php if (!empty($_POST['customerName'])) {
        echo h($_POST['customerName']);
    } ?>">
                </div>
                <div class="form-group col-sm-4">
                    <label for="tel">電話番号</label>
                    <input type="text" name="tel" id="tel" class="form-control" placeholder="ハイフンなしの半角数字10〜11桁" value="<?php if (!empty($_POST['tel'])) {
        echo h($_POST['tel']);
    } ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="col-sm-2"></div>
                <div class="form-group col-sm-4">
                    <label for="gender">性別</label>
                    <br>
                    <?php
                    if (!empty($_POST['gender'])) {
                        if ($_POST['gender'] === "男性") {
                            ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="gender" value="男性" checked>
                                <label class="form-check-label" for="gender">男性　</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="gender" value="女性">
                                <label class="form-check-label" for="gender">女性</label>
                            </div>
                        <?php
                        }
                        if ($_POST['gender'] === "女性") {
                            ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="gender" value="男性">
                                <label class="form-check-label" for="gender">男性　</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="gender" value="女性" checked>
                                <label class="form-check-label" for="gender">女性</label>
                            </div>
                    <?php
                        }
                    } ?>
                </div>
                <div class="form-group col-sm-4 select">
                    <label for="role">担当営業</label>
                    <select class="custom-select custom-select-sm" id="role" name="employeeId">
                        <?php
                        if (!empty($_POST['employeeId'])) {
                            $empList = tantoEigyo($pdo);
                            if ($_SESSION['role'] === '営業') {
                                foreach ($empList as $row) {
                                    if ($_SESSION['empId'] === $row['employee_id']) {
                                        ?>
                                                        <option value=<?php echo $row['employee_id'] ?> selected><?php echo $row['employee_id'] . ":" . h($row['employee_name']); ?></option>
                                                        <?php
                                    }
                                    if ($_SESSION['empId'] !== $row['employee_id']) {
                                        ?>
                                                            <option value=<?php echo $row['employee_id'] ?> disabled><?php echo $row['employee_id'] . ":" . h($row['employee_name']); ?></option>
                                                    <?php
                                    }
                                }
                            }
                            if ($_SESSION['role'] === '営業部長') {
                                foreach ($empList as $row) {
                                    if ($_POST['employeeId'] === $row['employee_id']) {
                                        ?>
                                    <option value=<?php echo $row['employee_id'] ?> selected><?php echo $row['employee_id'] . ":" . h($row['employee_name']); ?></option>
                                 <?php
                                    }
                                    if ($_POST['employeeId'] !== $row['employee_id']) {
                                        ?>
                                        <option value=<?php echo $row['employee_id'] ?>><?php echo $row['employee_id'] . ":" . h($row['employee_name']); ?></option>
                        <?php
                                    }
                                }
                            }
                        } ?>

                    </select>
                </div>
            </div>
            <br>

            <div class="form-group row">
                <div class="col-sm-1"></div>
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-primary btn-block" name="update" onclick="return confirm('更新します。よろしいですか？')">更新</button>
                    <input type="hidden" name="customerId" value="<?php if (isset($_POST['customerId'])) {
                            echo $_POST['customerId'];
                        } ?>">
                    <input type="hidden" name="employeeId" value="<?php if (isset($row['employee_id'])) {
                            echo $row['employee_id'];
                        } ?>">

                </div>
                <div class="col-sm-5">
                    <button type="button" class="btn btn-secondary btn-block" onclick="history.back()">戻る</button>
                </div>
            </div>
        </form>
<?php
}
?>
    <br>
    <p class="msg">
    <?php if ($msgs != null) {
    foreach ($msgs as $msg) {
        echo $msg."<br>";
    }
} ?></p>
    <hr id="customerList">
    <br>
    <h4 class="col-sm-12">顧客一覧</h4>
    <br>

    <form class="form-inline" action="customerList.php#customerList">
        <div class="col-sm-6"></div>
        <div class="form-group">
            <input type="search" class="form-control" name="search" id="search" placeholder="入力してください">
            <button type="submit" class="btn btn-dark">検索</button>
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="location.href='customerList.php#customerList'">リセット</button>
        </div>
    </form>

    <table class="table detailTable table-hover">
        <thead>
            <tr>
            <td style="width:10%">顧客ID</td>
            <td style="width:20%">　顧客名</td>
            <td style="width:15%">　　電話番号</td>
            <td style="width:10%">　性別</td>
            <td style="width:25%">　　営業担当</td>
            <td style="width:10%"></td>
            <td style="width:10%"></td>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($list as $row) {
                ?>
                <tr>
                    <form method="post">
                        <td style="width:10%"><?php echo $row['customer_id']; ?></td>
                        <td style="width:20%"><?php echo h($row['customer_name']); ?></td>
                        <td style="width:15%"><?php echo h($row['tel']); ?></td>
                        <td style="width:10%">
                            <?php echo h($row['gender']) ?>
                        </td>
                        <td style="width:25%">
                            <?php echo $row['employee_id'] ?>：<?php echo h($row['employee_name']); ?>
                        </td>
                        <td style="width:10%">
                        <?php if ($_SESSION['role'] ==='営業' && $_SESSION['empId'] !== $row['employee_id']) { ?>
                            <button type="submit" class="btn btn-info btn-sm" name="edit" value="edit" disabled>編集</button>
                        <?php } else { ?>
                            <button type="submit" class="btn btn-info btn-sm" name="edit" value="edit">編集</button>
                        <?php } ?>
                        </td>
                        <input type="hidden" name="customerId" value="<?php echo $row['customer_id'] ?>">
                        <input type="hidden" name="customerName" value="<?php echo $row['customer_name'] ?>">
                        <input type="hidden" name="tel" value="<?php echo $row['tel'] ?>">
                        <input type="hidden" name="gender" value="<?php echo $row['gender'] ?>">
                        <input type="hidden" name="employeeId" value="<?php echo $row['employee_id'] ?>">
                        <input type="hidden" name="employeeName" value="<?php echo $row['employee_name'] ?>">
                    </form>
                        <td style="width:10%">
                    <form method="post">
                    <?php if ($_SESSION['role'] ==='営業' && $_SESSION['empId'] !== $row['employee_id']) { ?>
                        <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('削除します。よろしいですか？');" disabled>削除</button>
                        <?php } else { ?>
                            <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('削除します。よろしいですか？');">削除</button>
                        <?php } ?>
                        <input type="hidden" name="customerId" value="<?php echo $row['customer_id'] ?>">
                    </form>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</>
<?php
require_once '../parts/footer.php';
?>