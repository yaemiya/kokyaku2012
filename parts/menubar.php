<div class="menubar">
  <ul class="nav justify-content-end">
  <li class="nav-item">
        <?php if (isset($_SESSION['empName'])) {
    echo "こんにちは ". $_SESSION['empName'] . " さん　　";
} ?>
      </li>
      
        <li class="nav-item">
        <form name="custList">
          <input type="hidden" name="custList">
          <a href="../customer/customerList.php">顧客一覧</a>
        </form>
        </li>

        <li class="nav-item">
        <form name="empList">
          <input type="hidden" name="empList">
          <a href="../employee/employeeList.php">社員一覧</a>
        </form>
        </li>

        <li class="nav-item">
        <form name="logout">
            <input type="hidden" name="logout">
            <a href="javascript:document.logout.submit()">ログアウト</a>
          </form>
        </li>
    </ul>
</div>