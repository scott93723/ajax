# Ajax 與 API

`Ajax` 是一種非同步的運行資料處理方式，我們都知道網頁瀏覽透過伺服器請求將網頁載到用戶端進行網頁文件讀取。如果還需要做資料處理或請求，則需要重新重整網頁，使得請求後端伺服器再新生成文件並傳送到用戶端進行文件讀取。

`Ajax` 簡單來說就是將這些動作包在一起，當你閱讀目前頁面時，透過 JavaScript 在背後呼叫執行 B 頁面但你未察覺到，而將 B 頁面的資訊取得之後，重新 DOM 控制改寫 A 頁面的內容。通常會使用 Ajax 的場合都是處於需要從某處 (Database or api) 進行資料請求 (get data) 或是資料處理 (pusb data)。

最經典的案例就是 Gmail 介面，網頁不用重新加載就能立即性的看見任何結果，該 HTML 會根據你任何操作直接抽換 DOM 給你，使得網頁就像活現的線上應用程式那樣使用。

## 語法

`JavaScript` 的 `Ajax` 相對來說比較複雜且完整參數指令（可自行查 MDN 網站了解），jQuery 的 Ajax 則大幅簡化程式且提供多種方法，只要輕鬆的指令 ava 就 cript 能控制。主分以下方法與用途：

語法|說明
---|-------
$(selector).load(url)|對 selector 進行載入 url 之 HTML 並替換內容
$.get(url,data,callback) |以 get 概念傳送 data 到 url 提供取得，並透過 callback 取得執行指令與 url 結果
$.post(url,data,callback)|同上，但是以 postt 概念傳送 data 到 url
$.getJSON(url,data,success(data,status,xhr)) |同 get 概念，可從 url 取得 JSON 格式 (API) 之資料，並提供相關 success 請求參數
$.getScript(url,data,success(data,status,xhr)) | 同上，主要是取得JavaScript腳本來執行
$.ajax(setting) |完整的 Ajax 控制語法，setting 為多資料之物件結構陣列。前幾項都是簡化過的 Ajax()

> `$.get()` vs `$.post()`

你如果能理解一般表單進行資料提交或存取時，你可以選擇 get 或 post 方式，透過該超全域變數進行資料傳遞，在 Ajax 也是同樣原理提交變數到另一個網頁去，。基本語法結過如下（以 post 為例，get 不再重複解釋）

```javascript
$.post(url,data,callback,type);
//url 目標網址，將資料送給何者網頁
//data 為傳遞的變數透過 post/get 轉為超全域變數，能以 JSON 陣列塞入多筆資料
//callback 指定當回傳資料時執行函式，通常會在這裡進行 DOM 變化
//能指定回傳的型態 (HTML,XML,JSON...)，預設會自動判別
```

這是前者屬於簡化後的 `$.post` 用法，同等於後者完整的 `$.Ajax` 用法。

```javascript
$.ajax({
  type: 'POST',
  url: url,
  data: data,
  success: callback,//可以指定函式或匿名函式
  dataType: dataType //可省略不寫
});
```

## 範例

由於需要一個後端程式 (PHP) 與資料庫 (MySQL) 並搭配 PDO 方式連接，如果你本身已會此兩項技能可自行參考本事前準備之相關環境，否則請於上課時採用老師的後端環境（由老師提供後端環境）。DOM 操作與 Ajax 部分我們依賴 jQuery 來快速套用。

### 後端準備

 1. `MySQL` 匯入：登入權限的帳號/密碼 `root/1qaz@wsx`, 資料庫/表為 `class/student`。

    `class.sql`

    ```sql
    -- phpMyAdmin SQL Dump
    -- version 4.7.7
    -- https://www.phpmyadmin.net/
    --
    -- 主機: 127.0.0.1
    -- 產生時間： 2018 年 01 月 31 日 13:19
    -- 伺服器版本: 5.7.20
    -- PHP 版本： 7.1.10
    
    SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
    SET AUTOCOMMIT = 0;
    START TRANSACTION;
    SET time_zone = "+00:00";
    
    
    /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
    /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
    /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
    /*!40101 SET NAMES utf8mb4 */;
    
    --
    -- 資料庫： `class`
    --
    
    -- --------------------------------------------------------
    
    --
    -- 資料表結構 `students`
    --
    
    CREATE TABLE `students` (
      `cID` tinyint(2) UNSIGNED ZEROFILL NOT NULL,
      `cName` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
      `cSex` enum('F','M') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'F',
      `cBirthday` date NOT NULL,
      `cEmail` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
      `cPhone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
      `cAddr` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
      `cHeight` tinyint(3) UNSIGNED DEFAULT NULL,
      `cWeight` tinyint(3) UNSIGNED DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
    
    --
    -- 資料表的匯出資料 `students`
    --
    
    INSERT INTO `students` (`cID`, `cName`, `cSex`, `cBirthday`, `cEmail`, `cPhone`, `cAddr`, `cHeight`, `cWeight`) VALUES
    (01, '簡奉君', 'F', '1987-04-04', 'elven@superstar.com', '0922988876', '台北市濟洲北路12號', 160, 49),
    (02, '黃靖輪', 'M', '1987-07-01', 'jinglun@superstar.com', '0918181111', '台北市敦化南路93號5樓', 175, 72),
    (03, '潘四敬', 'M', '1987-08-11', 'sugie@superstar.com', '0914530768', '台北市中央路201號7樓', 162, 65),
    (04, '賴勝恩', 'M', '1984-06-20', 'shane@superstar.com', '0946820035', '台北市建國路177號6樓', 178, 72),
    (05, '黎楚寧', 'F', '1988-02-15', 'ivy@superstar.com', '0920981230', '台北市忠孝東路520號6樓', 164, 45),
    (06, '蔡中穎', 'M', '1987-05-05', 'zhong@superstar.com', '0951983366', '台北市三民路1巷10號', 172, 75),
    (07, '徐佳螢', 'F', '1985-08-30', 'lala@superstar.com', '0918123456', '台北市仁愛路100號', 158, 56),
    (08, '林雨媗', 'F', '1986-12-10', 'crystal@superstar.com', '0907408965', '台北市民族路204號', 166, 48),
    (09, '林心儀', 'F', '1988-12-01', 'peggy@superstar.com', '0916456723', '台北市建國北路10號', 168, 50),
    (10, '王燕博', 'M', '1993-08-10', 'albert@superstar.com', '0918976588', '台北市北環路2巷80號', 169, 68);
    
    --
    -- 已匯出資料表的索引
    --
    
    --
    -- 資料表索引 `students`
    --
    ALTER TABLE `students`
      ADD PRIMARY KEY (`cID`);
    
    --
    -- 在匯出的資料表使用 AUTO_INCREMENT
    --
    
    --
    -- 使用資料表 AUTO_INCREMENT `students`
    --
    ALTER TABLE `students`
      MODIFY `cID` tinyint(2) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
    COMMIT;
    
    /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
    /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
    /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
    ```

 2. 建立 `api.php`：做為後端的簡易連結處理。

    ```php
    <?php
    header("Content-Type:text/html; charset=utf-8");
    
    // 建立連線字串，另外順便校正 PHP 時差
    date_default_timezone_set("Asia/Taipei");
    
    // $host = "localhost:3306";
    $host = "localhost";
    $user = "root";
    $password = "1qaz@wsx";
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
            $sql .= "                      `cWeight`='{$_POST['cWeight']}' WHERE `cID`={$_GET['cID']}";
    
            $result = mysqli_query($conn, $sql);
            // 成功時，我們 HTML 生成所需要的更新日期之文字，透過 Ajax 回傳給前端
            if ($result) {
                echo "updated";
            }
    
            break;
        case 'delete':
            $sql = "DELETE FROM `students` WHERE `cID`={$_POST['cID']}";
            $result = mysqli_query($conn, $sql);
    
            if ($result) {
                echo "deleted";
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
    ```

    最後，由此代碼可知：

    1. 取凡任何資料請求皆使用 POST 進行變數傳遞，
    2. 同時變數之索引名稱分別為 id, name, weight, info，時間為自動抓取。
    3. 後端採用網址 `GET` 參數判斷 `select`, `insert`, `update`, `delete`。
    4. 使用 `select` 需傳送 `start` 值，方便撈取固定十筆數量。

### 前端準備

先設計簡單的 table 畫面，之後隨著 CRUD 的循序設計慢慢豐富此頁面設計。

`php_front_read.html`

```html
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.js'></script>
    <title>Document</title>
    <style>
        .insertzone {
            position: fixed;
            background: #333333AA;
            width: 100%;
            height: 100%;
            top: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-basis: 50%;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        .insertzone>form {
            width: 100%;
        }
    </style>
</head>

<body>
    <table width="100%">
        <thead>
            <tr align="center">
                <td>學號</td>
                <td>姓名</td>
                <td>性別</td>
                <td>生日</td>
                <td>電子郵件</td>
                <td>電話</td>
                <td>住址</td>
                <td>身高</td>
                <td>體重</td>
                <td colspan="2">功能</td>
            </tr>
            <tr>
                <td colspan="11">
                    <hr>
                </td>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <td colspan="11" style="text-align: center;padding:10px">
                    <button onclick="activeForm()">新增</button>
                    <button onclick="loading()">加載</button>
                </td>
            </tr>
        </tfoot>
    </table>
<.body>
```

> SELECT 篇：Ajax 資料請求

1. 先確認後端這裡會提供完整的 `HTML` 代碼給我們，我們不需要自行規劃 `HTML`。因此前端大致語法如下：

   ```javascript
   /* $.post 寫法 */
   $.post("api.php?do=select", {start}, function (result) {
     $("tbody").html(result);
     $(".mdy").click(chginput);//因為是後來生成的 HTML，你必須重新使 DOM 路徑被認識（或者走 HTML 的 onclick 比較快）
   });

   /* $.Ajax 完整寫法 */
   $.ajax({
     type: "POST",
     data: {start},
     url: "api.php?do=select",
     success: function (result){
       $("tbody").html(result);
       $(".mdy").click(chginput);
     }
   });

2. 大致原理為，我們能將資料請求出來並將回傳的所有資訊直接替換 tbody 的 HTML 內容
3. 此外，因配合新生成的 DOM 還沒有事件偵測，所以重新指定點擊事件，將預告指定給 chginput 函式
4. 為了完整性練習，這裡先把第 1 步驟的 Ajax 使用 loading 包住，透過函式運行來達到載入效果。
5. 我們可以控制每次載入幾筆，所以另外可以設計一個按鈕去觸發 loading 就能十筆的載入更多。

   ```javascript
   /*select*/
   let start = 0;

   function loading() {
      $.post("http://api.com/api.php?do=select", {
          start
      }, function(result) {
           if (result != "fail") {
             $("tbody").append(result);
             $(".mdy").click(chginput);
             start += 4;
             console.log(start)
           }
      });
   }
   loading();
   ```

6. 再來是規劃 table footer 區域，這裡放入 loading() 觸發紐，另可預先做好新增按鈕。

   `php_front_read.html`

   ```html
        <tfoot>
            <tr>
                <td colspan="11" style="text-align: center;padding:10px">
                    <button onclick="activeForm()">新增</button>
                    <button onclick="loading()">加載</button>
                </td>
            </tr>
        </tfoot>

   ```

7. 目前為止你已經能完成 `Ajax` 的資料請求 (select)，其整個流程為：
   - 使用者請求一個 `HTML` 文件到前端瀏覽器，該 `HTML` 只有乾淨的 table
   - 由 `Ajax` 請求資料到另一個 `api.php` 但網頁使用者並不知道此事。（可透過 Chrome 開發工具 Network 查詢）
   - 再不進行網頁重載情況下，`Ajax` 的結果會進行 `DOM` 替換。
   - 使用者取得了具備資料結構的內容。

> UPDATE 篇：Ajax 資料處理

先練習 `UPDATE` 部分，我們透過前一章節的練習繼續接著處理。如何透過 `Ajax` 幫我們進行資料提供給後端程式進行 `SQL` 更新。

1. 前一章節的練習，我們有預先指定每次點擊修改按鈕時會觸發 chginput 函式。
2. 此函式重點是將純文字轉變成 input 格式。將同個 tr 內，除了自己其他 td 都列為變數 item 代用
3. 利用 this 或 item 對象，找到 tr 位置，並直接整個 tr>td 替換掉。
4. td 替換透過 `itiem.eq()` 來進行 `text` 轉 `input:value`
5. 最後補上一個儲存按鈕，為了省去因新 `DOM` 需要重新綁定 `click` 事件，透過 `onclick` 直接寫好。

   ```javascript
   /*update before DOM transform*/
   function chginput() {
       // $(this) 指  <button class='mdy'>修改</button>
       // $(this).parent 指該 <button> 所屬的 <td>
       // $(this).parent().siblings() 指所有 <td> 的兄弟
       let item = $(this).parent().siblings();
       // ES6 JavaScript Template String 樣板字串 反引號 (`) 來插入一段字串，並且可以使用 ${} 來加入變數或函式
       item.parent().html(`
         <td><input type="hidden" name="cID" value="${item.eq(0).text()}">${item.eq(0).text()}</td>
         <td><input type="text" name="cName" value="${item.eq(1).text()}"></td>
         <td><input type="text" name="cSex" value="${item.eq(2).text()}"></td>
         <td><input type="text" name="cBirthday" value="${item.eq(3).text()}"></td>
         <td><input type="text" name="cEmail" value="${item.eq(4).text()}"></td>
         <td><input type="text" name="cPhone" value="${item.eq(5).text()}"></td>
         <td><input type="text" name="cAddr" value="${item.eq(6).text()}"></td>
         <td><input type="text" name="cHeight" value="${item.eq(7).text()}"></td>
         <td><input type="text" name="cWeight" value="${item.eq(8).text()}"></td>
         <td>
             <button onclick="chgtxt(this)">儲存</button>
         </td>
      `);
       //像這裡就直接指定 onclick，否則你必須要在宣告一次 click
       //HTML 的 onclick 不像 js event 事件能自身帶 this，所以要塞入 this 才能傳遞
   }
   ```

6. 當使用者按下儲存時，我們需要提交 Ajax 出去給後端做 SQL 更新。
7. 這裡因為資料為多筆，所以需要以物件型態傳遞。但能支援 serialize() 打包函式。
   - `serialize()` 能幫你把多筆資料做成 GET 參數
   - 你可以試著 consol.log(data) 理解透過 serialize() 會得到甚麼
   - 雖然是 URL 參數 (GET)，但 jQuery’s Ajax 會聰明的幫你轉成物件參數
8. 接著透過 `Ajax` 成功的將資料交由後端處理，此時你應該等到回傳後確定更新成功才適合作畫面更新
9. 由於還有更新時間，我們希望後端能告訴我們 cdate 值
10. 畫面更新時，重新將目前 input.val 變回新值 text 並重新翻新 DOM 與 click 事件綁定

    ```javascript
    /*update after DOM transform*/
    function chgtxt(who) {
        const data = $(who).parents("tr").find("input").serialize();
        console.log(data)

        $.post("http://api.com/api.php?do=update", data, function(cdata) {

            let item = $(who).parent().siblings();
            const
                cID = item.eq(0).text(),
                cName = item.eq(1).children().val(),
                cSex = item.eq(2).children().val(),
                cBirthday = item.eq(3).children().val(),
                cEmail = item.eq(4).children().val(),
                cPhone = item.eq(5).children().val(),
                cAddr = item.eq(6).children().val(),
                cHeight = item.eq(7).children().val(),
                cWeight = item.eq(8).children().val();
            item.parent().html(`
               <td>${cID}</td>
               <td>${cName}</td>
               <td>${cSex}</td>
               <td>${cBirthday}</td>
               <td>${cEmail}</td>
               <td>${cPhone}</td>
               <td>${cAddr}</td>
               <td>${cHeight}</td>
               <td>${cWeight}</td>
               <td>
                  <button class='mdy'>修改</button>
                  <button onclick='del(this)'>刪除</button>
               </td>
            `);
            //這裡新的 HTML 已經跟前面出現過的脫節，所以還要重新再綁一次
            $(".mdy").click(chginput); 
        });
    }
    ```

11. 此時整個畫面操作試試，你已經能正常更新內容，即使畫面重整也不會出問題。

> DELETE 篇：Ajax 資料處理

這裡再多練習一次資料處理，透過 Ajax 我們可以完成委託後端來協助 SQL 資料移除。

1. 規劃刪除按鈕動作能導向到 del(this)，這裡直接使用 HTML 的 onclick 來處理省事。
2. 透過 this 位置，我們要找到同排第一個欄位內容 id。
3. data 部分使用 JSON，可以塞變數 {Variables}或 {name:value}，單一個資料傳遞不能直接使用變數。
4. 如果刪除成功後端會提供內容給前端，如果偵測到有內容回傳代表刪除成功，DOM 直接 remove() 就好

```javascript

/*delete*/
function del(who) {
  let id = $(who).parent().siblings().eq(0).text();
  // $.post("api.php?do=delete",{"id":id},function(result){ //DATA=JSON
  $.post("api.php?do=delete", { id }, function (result) {
    if (result) $(who).parent().parent().remove(); //有回傳才做事
  });
}
```

> INSERT 篇：Ajax 資料提交

新增方式跟修改大同小異，主要差別於前端你該用怎樣的畫面呈現資料輸入。我們需要設計一個上層覆蓋的區域進行簡易欄位。

1. 提供新增表單的 HTML 代碼位置，找到任一處新增 div 並設定相關 CSS 屬性。先指定不顯示，之後透過 jQuery FadeIn 呈現

   ```javascript
   <style>
     .insertzone {
       position: fixed;
       background: #333333AA;
       width: 100%;
       height: 100%;
       left: 0;
       top: 0;
       display: flex;
       justify-content: center;
       align-items: center;
       flex-basis: 50%;
       color: white;
       font-weight: bolder;
       text-align: center;
     }
     .insertzone>form {
       width: 100%;
     }
   </style>
   <div class="insertzone" style="display: none;">
     <!--
     這裡不先寫好 HTML 是因為我們網頁不會重整，因此第二次進行新增時 HTML 子元素需要清掉
     因此直接由 JavaScript 來設計較適宜
     -->
   </div>
   ```

2. 透過新增大按鈕觸發函式 activeForm，這裡會生成 HTML 進行淡入。
3. 如果使用者想取消，我們需要幫助淡出，但 form 可以不用特別清除，下次淡入前都會覆蓋該 HTML

   ```javascript
   /*insert*/
   function activeForm() {
     console.log('hi')
     $(".insertzone").html(`
       <form action="">
         <h1>新增學生資料</h1>
         <hr>
         <p>姓名:<input type="text" name="cName"></p>
         <p>性別:<input type="radio" name="cSex" value='M'>男生 <input type="radio" name="cSex" value='F'>女生</p>
         <p>生日:<input type="date" name="cBirthday"></p>
         <p>電子郵件:<input type="email" name="cEmail"></p>
         <p>電話: <input type="tel" name="cPhone"></p>
         <p>住址:<input type="text" name="cAddr"></p>
         <p>身高:<input type="number" name="cHeight"></p>
         <p>體重:<input type="number" name="cWeight"></p>
         <hr>
         <p>
           <!-- 注意 button 沒有 type 會形同 submit -->
           <button type="button" onclick="sendForm(this)">新增</button>
           <button type="button" onclick="closeAddform()">取消</button>
         </p>
       </form>
     `);
     $(".insertzone").fadeIn();
   }

   function closeAddform() {
     $(".insertzone").fadeOut();
   }
   ```

4. 當使用者送出時，因為我們使用 Ajax 進行提交。所以能不使用 form 的 submit 功能（甚至你可以不用 form 了），使用 DOM 進行資料收集再改由 Ajax 發送。
5. 然而有使用分流加載的關係，所以這裡乾脆重新撈取資料。否則考量的狀況會分為目前是否再最後的加載而是否調整 DOM，需要更多機制去判斷。

   ```javascript
   function sendForm(who) {
      const data = $(who).parents("form").find("input").serialize();
      $.post("api.php?do=insert", data, function (result) 
        //由於是分流載入而考慮情況較多。最快就是歸零重新載入初始 select
        $("tbody").empty();
        start = 0;
        loading();
        $(".insertzone").fadeOut();
      });
   }
   ```

到目前為止你能清楚知道，Ajax 主要是取代了傳統表單遞交（需重新加載畫面）的處理。將一切資料再背後偷偷額外遞交執行，前端只需 DOM 重新變化調整就好，不需要重新加載網頁。好處是能讓網頁靈活且將效能歸入給使用者環境，也能減少伺服器負擔（只需處理局部資料做傳遞，省下 request 全 HTML 的流量與效能）。


