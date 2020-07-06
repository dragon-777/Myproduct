<?php 
/* 定義
　 SQL接続
　 SERVER['REQUEST_METHOD']
　 定義
　 トランザクション開始
　 drink_information_tableにINSERT
　 drink_stock_tableにINSERT
　 トランザクション終了
　 在庫の更新
　 公開ステータスの更新
　 商品一覧の情報を取得
　 
*/

//定義
$host = 'localhost';
$user = 'codecamp34516';
$passwd = 'codecamp34516';
$dbname = 'codecamp34516';
$drink_name = '';
$drink_price = '';
$drink_stock = '';
$drink_picture = '';
$drink_list = [];
$err_msg = [];
$img_dir = './img/';

//SQL接続
if ($link = mysqli_connect($host, $user, $passwd, $dbname)) {
    mysqli_set_charset($link, 'UTF8');
    
    //SERVER['REQUEST_METHOD']
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $type = $_POST['type'];
        
        if($type === 'add'){

            //定義
            $drink_name = $_POST['name'];
            $drink_price = $_POST['price'];
            $date = date('Y-m-d H:i:s');
            $update_date = date('Y-m-d H:i:s');
            $status = $_POST['status'];
            $drink_stock = $_POST['stock'];
            $stock_update_date = date('Y-m-d H:i:s');


//exit;

            if($drink_name ===''){
                $err_msg[]='ドリンク名を追加してください';
            }
            if($drink_price ===''){
                $err_msg[]='ドリンクの値段を追加してください';
            }
            else if(preg_match('/^[0-9]+$/', $drink_price) !== 1){
                $err_msg[]='ドリンクの値段は0円以上にしてください';
            }
            if($drink_stock ===''){
                $err_msg[]='ドリンクの個数を追加してください';
            }
            else if(preg_match('/^[0-9]+$/', $drink_stock) !== 1){
                $err_msg[]='ドリンクの個数は0個以上にしてください';
            }
            if($status !=='0'&&$status !=='1'){
                $err_msg[]='ドリンクのステータスを追加してください';
            }
            if (is_uploaded_file($_FILES['picture']['tmp_name']) === TRUE) {
                $drink_picture = $_FILES['picture']['name'];
        
                // 画像の拡張子取得
                $extension = pathinfo($drink_picture, PATHINFO_EXTENSION); // jpg
        
                // 拡張子チェック
                if ($extension === 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                    // フ��イルID生成し保存ファイルの名前を変更
                    $drink_picture = md5(uniqid(mt_rand(), true)) . '.' . $extension; // f78sd6f87dsfa.jpg
        
                    // 同名ファイルが存在するか確認
                    if (is_file($img_dir . $drink_picture) !== TRUE) {
                        // ファイルを移動し保存
                        if (move_uploaded_file($_FILES['picture']['tmp_name'], $img_dir . $drink_picture) !== TRUE) {
                            $err_msg[] = 'ファイルアップロードに失敗しました';
                        }
                    // 生成したIDがかぶることは通常ないため、IDの再生成ではなく再アップロードを促すようにした
                    } else {
                        $err_msg[] = 'ファイルアップロードに失敗しました。再度お試しください。';
                    }
                } else {
                    $err_msg[] = 'ファイル形式が異なります。画像ファイルはJPEG又はPNGのみ利用可能です。';
                }
            } else {
                $err_msg[] = 'ファイルを選択してください';
            }
            if(count($err_msg) === 0){
                //トランザクション開始
                mysqli_autocommit($link, false);
                
                // drink_information_tableにINSERT
                $data = [
                    $drink_name,$drink_price,$date,$update_date,$status,$drink_picture
                ];
                $sql = 'INSERT INTO drink_information_table(drink_name,drink_price,date,update_date,status,drink_picture) VALUES (\'' . implode('\',\'', $data) . '\')';
                if (mysqli_query($link, $sql) !== TRUE) {
                       $err_msg[] = 'drink_information_table: insertエラー:' . $sql;
                }
                $drink_id = mysqli_insert_id($link);
                // drink_stock_tableにINSERT  
                $data = [
                    $drink_id,$drink_stock,$date,$stock_update_date
                ];
                $sql = 'INSERT INTO drink_stock_table(drink_id,drink_stock,date,stock_update_date) VALUES (\'' . implode('\',\'', $data) . '\')';
                if (mysqli_query($link, $sql) !== TRUE) {
                       $err_msg[] = 'drink_stock_table: insertエラー:' . $sql;
                }
                
                //トランザクション終了
                if (count($err_msg) === 0) {
                    mysqli_commit($link);
                    print '追加成功';
                }else {
                    mysqli_rollback($link);
                    $err_msg[]='追加失敗';
                }
            }
        }
        //在庫の更新
        else if($type === 'stock'){
            $update_drink_id = $_POST['drink_id'];
            $update_stock = $_POST['update_stock'];
            $stock_update_date = date('Y-m-d H:i:s');
        
            if(preg_match('/^[0-9]+$/', $update_stock) === 1){
                $sql = 'UPDATE drink_stock_table SET drink_stock='.$update_stock.',stock_update_date='."'".$stock_update_date."'".' WHERE drink_id='.$update_drink_id;   
                if (mysqli_query($link, $sql) !== TRUE) {
                       $err_msg[] = 'drink_stock_table: updateエラー:' . $sql;
                }
                if (count($err_msg) === 0) {
                    print '在庫更新成功';
                }
            }else{
                $err_msg []= '不正なパラメータが送信されました';
            }
        }
        //公開ステータスの更新
        else if($type === 'status'){
            $update_drink_id = $_POST['drink_id'];
            $update_status = $_POST['update_status'];
            $stasus_update_date = date('Y-m-d H:i:s');
            if($update_status ==='非公開→公開'){
                $update_status = 1;
            }
            else if($update_status ==='公開→非公開'){
                $update_status = 0;
            }
            else{
                $err_msg[] = '不正なパラメータが送信されました';
            }
            if(count($err_msg) === 0){
                $sql = 'UPDATE drink_information_table SET status='.$update_status.',update_date='."'".$stasus_update_date."'".' WHERE drink_id='.$update_drink_id;  
                if (mysqli_query($link, $sql) !== TRUE) {
                       $err_msg[] = 'drink_information_table: updateエラー:' . $sql;
                }
                if (count($err_msg) === 0) {
                    print 'ステータス更新成功';
                }
            }
        }
    }
    //商品一覧の情報を取得
        $sql = 'SELECT drink_information_table.drink_id,
                       drink_information_table.drink_name,
                       drink_information_table.drink_price,
                       drink_information_table.status,
                       drink_information_table.drink_picture,
                       drink_stock_table.drink_stock
                       FROM drink_information_table JOIN drink_stock_table 
                       ON drink_information_table.drink_id= drink_stock_table.drink_id';
        if ($result = mysqli_query($link, $sql)) {
               $i = 0;
               while ($row = mysqli_fetch_assoc($result)) {
                   $drink_list[$i]['drink_id'] = $row['drink_id'];
                   $drink_list[$i]['drink_picture']   = $row['drink_picture'];
                   $drink_list[$i]['drink_name']   = $row['drink_name'];
                   $drink_list[$i]['drink_price'] = $row['drink_price'];
                   $drink_list[$i]['drink_stock']   = $row['drink_stock'];
                   $drink_list[$i]['status']   = (int)$row['status'];
                   $i++;
               }
        } else {
               $err_msg[] = '商品一覧情報取得失敗:' . $sql;
        } 
        mysqli_free_result($result);
        mysqli_close($link);    
} else {
    $err_msg[] = 'error: ' . mysqli_connect_error();
}    
?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
   <meta charset="UTF-8">
   <title>自動販売機課題</title>
   <style type="text/css">
       body{
           margin:0 auto;
       }
       section{
           min-width:1100px;
           width:1200px;
           margin:0 auto;
       }
       h1{
           color:blue;
           text-align:center;
       }
       h2{
           color:#87ceeb;
       }
       #goods{
           color:#90ee90;
       }
       table{
           border:solid black 1px;
           border-collapse: collapse;
       }
       th{
           border:solid black 1px;
       }
       td{
           border:solid black 1px;
           padding:10px;
       }
       .img{
           width:150px;
           height:150px;
       }
   </style>
</head>
<body>
    <?php foreach ($err_msg as $value) { ?>
    <p><?php print $value; ?></p>
    <?php } ?>
   <h1>自動販売機管理ツール</h1>
   <section>
       <h2>新規商品追加</h2>
       <form method="post" enctype="multipart/form-data">
           <input type="hidden" name="type" value="add">
           名前:<input type="text" name="name">
           値段:<input type="text" name="price">
           個数:<input type="text" name="stock">
           <input type="file" name="picture" value="商品画像を選択" accept="image/jpeg","image/png">
           <select name="status">
               <option value="0">非公開</option>
               <option value="1">公開</option>
           </select>
           <input type="submit" name="submit" value="商品追加">
       </form>   
   </section>
   <section>
       <h2>商品情報変更</h2>
       <p id="goods">商品一覧</p>
       <table>
           <tr><th>商品画像</th>
               <th>商品名</th>
               <th>価格</th>
               <th>在庫数</th>
               <th>ステータス</th>
           </tr>
           <?php foreach($drink_list as $value){
           ?>
           <tr>
               <td>
                   <img src="<?php print 'img/'.htmlspecialchars($value['drink_picture'], ENT_QUOTES, 'UTF-8'); ?>" class="img"></img>
               </td>
               <td><?php print htmlspecialchars($value['drink_name'], ENT_QUOTES, 'UTF-8'); ?></td>
               <td><?php print htmlspecialchars($value['drink_price'], ENT_QUOTES, 'UTF-8'); ?>円</td>
               <td>
                   <form method="post">
                       <input type="hidden" name="type" value="stock">
                       <input type="hidden" name="drink_id" value="<?php print $value['drink_id']; ?>">
                       <input name="update_stock" value="<?php print $value['drink_stock']; ?>">個
                       <input type="submit" name="submit" value="変更">
                   </form>
               </td>
               <td>
                   <form method="post">
                       <input type="hidden" name="type" value="status">
                       <input type="hidden" name="drink_id" value="<?php print $value['drink_id']; ?>">
                       <input type="submit" name="update_status" value="<?php if($value['status']===1){
                            print '公開→非公開';
                       }else{ print '非公開→公開';
                       }
                       ?>">
                       
                   </form>
               </td>
           </tr>
           <?php }
           ?>
       </table>
   </section>
</body>
</html>