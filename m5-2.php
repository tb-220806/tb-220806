<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
<?php 
// DB接続設定
    $dsn = 'mysql:dbname=データベース名;host=ホスト名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//DBテーブルの作成
    $sql = "CREATE TABLE IF NOT EXISTS tbtable"
    ." ("
    ."id INT AUTO_INCREMENT PRIMARY KEY,"
    ."name char(32),"
    ."str TEXT,"
    ."date char(32),"
    ."pass char(32)"
    .");";
    $stmt = $pdo->query($sql);    
            
//変数定義   
    $name = $_POST["name"];
    $str = $_POST["str"];
    //投稿日時を取得
    $date = date("Y/m/d H:i:s");
    $pass=$_POST["pass"];
    
    $deletenum =$_POST["deletenum"];
    $deletepass=$_POST["deletepass"];
       
    $editnum=$_POST["editnum"];
    $editpass=$_POST["editpass"];
    $judge=$_POST["judge"];
    $correctpass=$_POST["correctpass"];
	
//どちらのフォームにも入力があり新規の送信フォームを送信した場合
    if(isset($_POST["submit"])&&$_POST["name"] != ""
        &&$_POST["str"] != ""&&$_POST["pass"] != ""&&$_POST["judge"] == "")
    {
        //データレコードの挿入
        $sql = $pdo -> prepare("INSERT INTO tbtable (name, str,date,pass)  
                                VALUES (:name, :str,:date,:pass)");
	    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
	    $sql -> bindParam(':str', $str, PDO::PARAM_STR);
	    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
	    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
	    $sql -> execute();
	    
	    echo "送信しました。<br>";
    }
    
//削除フォームを送信し、削除対象番号に０以上の入力がある場合
    if(isset($_POST["delete"]) && $_POST["deletenum"] != "" 
        && $_POST["deletenum"]>0 && $_POST["deletepass"] != "")
    {
        $sql = 'SELECT * FROM tbtable';
	    $stmt = $pdo->query($sql);
	    $results = $stmt->fetchAll();
	    foreach ($results as $row)
	    {
            //削除対象のデータでパスワードが合っている場合
            if($row['id']==$deletenum&&$row['pass']==$deletepass)
            {
                //入力したデータレコードを削除
                $id = $deletenum;
	            $sql = 'delete from tbtable where id=:id';
	            $stmt = $pdo->prepare($sql);
	            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	            $stmt->execute();
	    
	            echo"削除しました。<br>";
            }
            //削除対象のデータでパスワードが合っていない場合
            elseif($row['id']==$deletenum&&$row['pass']!=$deletepass)
            {
                echo"パスワードが正しくありません。<br>";
            }
	    }
    }

//編集対象番号が入力されていて、編集フォームを送信した場合
    if(isset($_POST["edit"])&&$_POST["editnum"]!="" && $_POST["editpass"]!="")
    {
        $sql = 'SELECT * FROM tbtable';
	    $stmt = $pdo->query($sql);
	    $results = $stmt->fetchAll();
	    foreach ($results as $row)
	    {
            //編集対象のデータでパスワードが合っている場合
            if($row['id']==$editnum&&$row['pass']==$editpass)
            {
                //編集するデータを取得
                $hiddeneditnum=$row['id'];
                $editname=$row['name'];
                $editstr=$row['str'];
                $hiddeneditpass=$row['pass'];
            }
            //パスワードが合っていない場合
            elseif($row['id']==$editnum&&$row['pass']!=$editpass)
            {
                echo"パスワードが正しくありません。<br>";
            }
	    }
    }
    
//編集後のフォームが入力されていて、送信フォームを送信した場合
    if(isset($_POST["submit"])&&$_POST["name"]!=""
    &&$_POST["str"]!=""&&$_POST["judge"]!="")
    {
        //入力されているデータレコードの内容を編集
        $id = $judge;
        $pass = $correctpass;
	    $sql = 'UPDATE tbtable SET name=:name,str=:str,date=:date,pass=:pass WHERE id=:id';
	    $stmt = $pdo->prepare($sql);
	    
	    $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
	    $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
	    $stmt -> bindParam(':str', $str, PDO::PARAM_STR);
	    $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
	    $stmt -> bindParam(':pass', $pass, PDO::PARAM_STR);
	    $stmt->execute();
	    
	    echo"編集しました。<br>";
    }    
        
?><form action="" method="post">
<!--編集フォーム送信時、名前・コメントの内容が既に入っている状態で表示-->
<label>氏名</label><br>
<input type="text" name="name" value="<?php echo $editname?>" placeholder="氏名を入力してください"><br>
<label>コメント</label><br>
<input type="text" name="str" value="<?php echo $editstr?>" placeholder="コメントを入力してください"><br>
<label>パスワード</label><br>
<input type="password" name="pass">
<input type="submit" name="submit"><br>
<br>
<label>- - - - - - - - - - - - - - - - - - - -</label><br>
<label>削除対象番号</label><br>
<input type="number" name="deletenum" ><br>
<label>パスワード</label><br>
<input type="password" name="deletepass">
<input type="submit" name="delete" value="削除"><br>
<br>
<label>- - - - - - - - - - - - - - - - - - - -</label><br>
<label>編集対象番号</label><br>
<input type="number" name="editnum" ><br>
<label>パスワード</label><br>
<input type="password" name="editpass">
<input type="submit" name="edit" value="編集"><br>
<!--「いま送信された場合は新規投稿か、編集か」を判断する情報を追加する-->
<input type="hidden" name="judge" value="<?php echo $hiddeneditnum?>"><br>
<!--編集内容入力後パスワードの入力を省略するための情報を追加-->
<input type="hidden" name="correctpass" value="<?php echo $hiddeneditpass?>"><br>
</form><?php

//入力したデータレコードを抽出し、表示する
    $sql = 'SELECT * FROM tbtable';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row)
	{
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['str'].',';
		echo $row['date'].'<br>';
	    echo "<hr>";
	}
?>
</body>
</html>