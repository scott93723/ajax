<?php
header("Content-Type:text/html; charset=utf-8");

// 建立連線字串，另外順便校正 PHP 時差
date_default_timezone_set("Asia/Taipei");

// $host = "localhost:3306";
$host = "api.com";
$user = "root";
$password = "1234";
$db = "class";
$port = "3306";

$conn = mysqli_connect($host, $user, $password) or die("連線錯誤");

if ($conn) {
    mysqli_select_db($conn, $db) or die("資料庫開啟失敗");
    //設定資料庫查詢使用的字元集編碼方式
    mysqli_query($conn, "set names utf8");
    //設定資料庫讀取時
    mysqli_query($conn, "set character_set_client=utf8");
    //設定資料庫寫入時
    mysqli_query($conn, "set charcter_set_results=utf8");
} else {
    die("資料庫連線失敗");
}

// 這裡用 switch 是因為還有其他 Ajax 提交，因此利用 GET 來做區分判斷處理。
// 功能測試, 搭配 PostMan
switch ($_GET['do']) {
    case 'select':
        $sql = "SELECT * FROM `students` limit {$_POST['start']} , 4";
        $result = mysqli_query($conn, $sql);
        //提取所有資料，索引為欄名
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        //print_r($rows);
        // SQL 內取得所有學生資料，由 foreach 規劃完整 tr>td，使前端單純 HTML 替換即可。
        if ($rows) {
            foreach ($rows as $row) {
                echo "
                 <tr align=center>
                   <td>{$row["cID"]}</td>
                   <td>{$row["cName"]}</td>
                   <td>{$row["cSex"]}</td>
                   <td>{$row["cBirthday"]}</td>
                   <td>{$row["cEmail"]}</td>
                   <td>{$row["cPhone"]}</td>
                   <td>{$row["cAddr"]}</td>
                   <td>{$row["cHeight"]}</td>
                   <td>{$row["cWeight"]}</td>
                   <td>
                      <button class='mdy'>修改</button>
                      <button onclick='del(this)'>刪除</button>
                   </td>
                 </tr></br>";
            }
        } else {
            echo 'fail';
        }
        break;
    case 'update':
        $sql = "UPDATE `students` SET `cName`='{$_POST['cName']}',";
        $sql .= "                      `cSex`='{$_POST['cSex']}',";
        $sql .= "                      `cBirthday`='{$_POST['cBirthday']}',";
        $sql .= "                      `cEmail`='{$_POST['cEmail']}',";
        $sql .= "                      `cPhone`='{$_POST['cPhone']}',";
        $sql .= "                      `cAddr`='{$_POST['cAddr']}',";
        $sql .= "                      `cHeight`='{$_POST['cHeight']}',";
        $sql .= "                      `cWeight`='{$_POST['cWeight']}' WHERE `cID`={$_POST['cID']}";

        $result = mysqli_query($conn, $sql);
        // 成功時，我們 HTML 生成所需要的更新日期之文字，透過 Ajax 回傳給前端
        if ($result) {
            echo "updated";
        }

        break;
    case 'delete':
        $sql = "DELETE FROM `students` WHERE `cID`={$_POST['cID']}";
        $result = mysqli_query($conn, $sql);
        mysqli_close($conn);
        if ($result) {
            echo "Record deleted successfully";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
        break;
    case 'insert':
        $sql = "INSERT INTO `students`(`cName`,`cSex`,`cBirthday`,`cEmail`,`cPhone`,`cAddr`,`cHeight`,`cWeight`) values(";
        $sql .= "'{$_POST['cName']}','{$_POST['cSex']}','{$_POST['cBirthday']}','{$_POST['cEmail']}','{$_POST['cPhone']}','{$_POST['cAddr']}','{$_POST['cHeight']}','{$_POST['cWeight']}')";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            echo "inserted";
        }

        break;

    default:
        # code...
        break;
}