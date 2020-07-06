<?php 
$host = 'localhost';
$user = 'codecamp34516';
$passwd = 'codecamp34516';
$dbname = 'codecamp34516';
$drink_list = [];

if ($link = mysqli_connect($host, $user, $passwd, $dbname)) {
   
   mysqli_set_charset($link, 'UTF8');
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
                $status = htmlspecialchars($row['status'],   ENT_QUOTES, 'UTF-8');
                if($status==='1'){
                    $drink_list[$i]['drink_id'] = htmlspecialchars($row['drink_id'],   ENT_QUOTES, 'UTF-8');
                    $drink_list[$i]['drink_picture']   = htmlspecialchars($row['drink_picture'],   ENT_QUOTES, 'UTF-8');
                    $drink_list[$i]['drink_name']   = htmlspecialchars($row['drink_name'],   ENT_QUOTES, 'UTF-8');
                    $drink_list[$i]['drink_price'] = htmlspecialchars($row['drink_price'],   ENT_QUOTES, 'UTF-8');
                    $drink_list[$i]['drink_stock']   = htmlspecialchars($row['drink_stock'],   ENT_QUOTES, 'UTF-8');
                    $drink_list[$i]['status']   = htmlspecialchars($row['status'],   ENT_QUOTES, 'UTF-8');
                }
                  $i++;
            }
   } else{
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
       table{
           margin-top:50px;
           margin-bottom:50px;
           border:solid black 1px;
           border-collapse: collapse;
       }
       td{
           width:150px;
           border:solid black 1px;
       }
       img{
           width:150px;
           height:150px;
       }
       .name,.money,.stock,.no_stock{
           width:150px;
           text-align:center;
       }
       .no_stock{
           color:red;
       }
   </style>
</head>
<body>
   <h1>自動販売機</h1>
   <section>
       <form method="post" action="result.php">
           所持金<input type="text" name="money">
           <table>
           <?php 
           $i =0;
           foreach($drink_list as $value){
               $i = $i++ ; 
               if($i %4==1){
           ?>
           <tr>
           <?php }
           ?>
               <td>
                   <div id="<?php print $i; ?>">
                       <img src="<?php print 'img/'.$value['drink_picture']; ?>"></img>
                       <div class="name"><?php print $value['drink_name']; ?></div>
                       <div class="money"><?php print $value['drink_price']; ?>円</div>
                       <?php if($value['drink_stock']>=1){?>
                       <div class="stock">
                           <input type="radio" name="drink_id" value="<?php print $value['drink_id']; ?>">
                       </div>         
                       <?php
                       }
                       ?>
                       <?php if($value['drink_stock']==='0'){?>
                       <div class="no_stock">
                           <?php print '売り切れ';?>
                       </div>
                       <?php 
                       }
                       ?>
                       
                   </div>    
               </td>
           <?php
               if($i %4==1){
           ?>
           </tr>
           <?php } 
           ?>
           <?php }
           ?>
           </table>
           <input type="submit" name="submit" value="購入">
       </form>   
   </section>
</body>
</html>