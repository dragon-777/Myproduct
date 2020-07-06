<!DOCTYPE html>
<html lang="ja">
<head>
   <meta charset="UTF-8">
   <title>課題</title>
   <style>
       body{
    background-color:#ccf580
    }
    h1{
        color:blue;
        text-align:center;
    }
</style>
</head>
<body>
   <h1>ひとこと掲示板</h1>
   
<?php
$filename = './bbs.txt';
$name = '';
$comment = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    date_default_timezone_set('Asia/Tokyo');
    $name =  $_POST['name'];
    $comment = $_POST['comment'];
    $log = '・'.$name ."：". $comment."  ".date('Y-m-d H:i:s')."\n" ;
    $name_length = mb_strlen($name,'UTF-8');
    $comment_length = mb_strlen($comment,'UTF-8');
    $name_space = ctype_space($name);
    $comment_space = ctype_space($comment);
    
    if (($name === '')||($name_space === TRUE )){
        print "<p>".'名前を入力してください'."</p>";
    }
    if (($comment === '')||($comment_space === TRUE)){
        print "<p>".'ひとことを入力してください'."</p>";
    }
    if ($name_length>20){
        print "<p>".'名前は20文字以内で入力してください'."</p>";
    }
    if ($comment_length>100){
        print "<p>".'ひとことは100文字以内で入力してください'."</p>";
    }
    if (($name !== '')&&($name_space !== TRUE )&&($comment !== '')&&($comment_space !== TRUE)&&($name_length<=20)&&($comment_length<=100)){
        if (($fp = fopen($filename, 'a')) !== FALSE) {
       
            if (fwrite($fp, $log) === FALSE) {
                print 'ファイル書き込み失敗:  ' . $filename;
            }
            fclose($fp);
        }
    }
    
}

$data = [];

if (is_readable($filename) === TRUE) {
    if (($fp = fopen($filename, 'r')) !== FALSE) {
        while (($tmp = fgets($fp)) !== FALSE) {
            $data[] = htmlspecialchars($tmp, ENT_QUOTES, 'UTF-8');
            $reversed = array_reverse($data);
        }
        fclose($fp);
        }
    } 
    else {
    $data[] = 'ファイルがありません';
    }
?>

   <form method="post">
        名前：<input type="text" name="name">
        ひとこと：<input type="text" name="comment">&nbsp;<input type="submit" name="submit" value="送信">
   </form>
    <?php if (is_readable($filename) === TRUE) {
    foreach ($reversed as $read) { ?>
    <p><?php print $read; ?></p>
    <?php }} ?>
</body>
</html>