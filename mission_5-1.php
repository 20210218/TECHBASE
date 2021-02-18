<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
<?php

    $value_str = "";
    $value_name = "";
    $value_num = "";
    $datetime = date("Y/m/d H:i:s");
    
    $comment = $_POST["str"];
    $name = $_POST["name"];
    $delnum = $_POST["delnum"];//削除番号
    $h_num = $_POST["henshunum"];//編集番号（編集ボタン用）
    $h_pass = $_POST["passhenshu"];//編集するときに入力したパスワード
    $t_num = $_POST["toukounum"];//編集番号（送信ボタン用）
    $s_pass = $_POST["passsakujo"];//削除するときに入力したパスワード
    $pass = $_POST["pass"];//新規登録の際のパスワード
    
//データベースに接続
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//テーブルを作成	
	$sql = "CREATE TABLE IF NOT EXISTS tbtest1"
	." ("
	."id INT AUTO_INCREMENT PRIMARY KEY,"
	."name char(32),"
	."comment TEXT,"
	."pass TEXT,"
	."datetime DATETIME"
	.");";
	$stmt = $pdo->query($sql);

//テーブル名表示	
	$sql ='SHOW TABLES';
	$result = $pdo -> query($sql);
	foreach ($result as $row){
		echo $row[0];
		echo '<br>';
	}
	echo "<hr>";

//テーブル詳細
	$sql ='SHOW CREATE TABLE tbtest1';
	$result = $pdo -> query($sql);
	foreach ($result as $row){
		echo $row[1];
	}
	echo "<hr>";
	
//削除フォーム
    echo "<br>";
    echo "<form action='' method='post'>";
    echo "<input type='num' name='delnum' placeholder = '削除対象番号'> ";
    echo "<input type='text' name='passsakujo' placeholder = 'パスワード'>";
    echo "<input type='submit' name='submit' value='削除'> ";
    echo "</form>";	
	
//削除
    if($delnum != ""){
        $id = $delnum; //削除番号の行のデータだけを抽出したい、とする
        $sql = 'SELECT * FROM tbtest1 WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll(); 
	    foreach ($results as $array){
		    //$rowの中にはテーブルのカラム名が入る
		    $delpass = $array['pass'];
	    }
        if($s_pass == $delpass){
            $id = $delnum;
	        $sql = 'delete from tbtest1 where id=:id';
	        $stmt = $pdo->prepare($sql);
	        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	        $stmt->execute(); 
	        echo "パスワードが一致しました。<br>";
	        echo "削除が完了しました。<br>";
        } else {
            echo "パスワードが違います。<br>";
        }
    }
    
//編集番号
    echo "<br>";
    echo "<form action='' method='post'>";
    echo "<input type='num' name='henshunum' placeholder = '編集対象番号'> ";
    echo "<input type='text' name='passhenshu' placeholder = 'パスワード'>";
    echo "<input type='submit' name='submit' value='編集'> ";
    echo "</form>";
    
//編集番号入力フォームに入力あったら送信フォームに内容送る
  if($h_num != ""){//編集フォームに入力があったら
        $id = $h_num; //編集番号の行のデータだけを抽出したい、とする
        $sql = 'SELECT * FROM tbtest1 WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll(); 
	    foreach ($results as $array){
		    //$rowの中にはテーブルのカラム名が入る
		    $value_pass = $array['pass'];
	    }
    if($h_pass == $array['pass']){//パスワードが一致したら
        echo "パスワードが一致しました。<br>";
        $id = $h_num; //編集番号の行のデータだけを抽出したい、とする
        $sql = 'SELECT * FROM tbtest1 WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll(); 
	    foreach ($results as $array){
		    //$rowの中にはテーブルのカラム名が入る
		    $value_num = $array['id'];
		    $value_name = $array['name'];
		    $value_str = $array['comment'];
	    }
    } else {
        $value_num = "";
		$value_name = "";
		$value_str = "";
        echo "パスワードが違います。<br>";
    }
  }
  
//投稿フォーム表示
    echo "<br>";
    echo "<form action='' method='post'>";
    echo "<input type='text' name='str' placeholder = 'コメント' value='".$value_str."'> ";
    echo "<input type='text' name='name' placeholder = '名前' value='".$value_name."'> ";
    echo "<input type='hidden' name='toukounum' placeholder = '番号' value='".$value_num."'> ";
    echo "<input type='text' name='pass' placeholder = 'パスワード'>";
    echo "<input type='submit' name='submit'> ";
    echo "</form>";
    echo "<br>";

//投稿フォーム	
	if($name != "" && $comment != ""){
	    if($t_num != ""){
//編集
//bindParamの引数（:nameなど）は4-2でどんな名前のカラムを設定したかで変える必要がある。
	$id = $t_num; //変更する投稿番号
	$sql = 'UPDATE tbtest1 SET name=:name,comment=:comment,datetime=:datetime WHERE id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':datetime', $datetime, PDO::PARAM_STR);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	echo "編集が完了しました。<br>";
	
	    } else if($pass != ""){  
	        
//新規投稿	
	$sql = $pdo -> prepare("INSERT INTO tbtest1 (name, comment, pass, datetime) VALUES (:name, :comment, :pass, :datetime)");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
	$sql -> bindParam(':datetime', $datetime, PDO::PARAM_STR);
	$sql -> execute();
	echo "新規投稿しました。<br>";
	    } else {
	       echo "パスワードを入力してください。<br>"; 
	    }
	} else {
	    echo "入力してください。<br>";
	}
  
	
//表示
    $sql = 'SELECT * FROM tbtest1';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();//fetchAllで結果を全件配列で取得
	//複数件のデータの取得方法として「fetchAllメソッド」を使う方法もあります。
    //fetchは1件しかデータを取得しませんでしたが、fetchAllは結果データを全件まとめて配列で取得します。
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['datetime'].'<br>';
		//echo $row['pass'].'<br>';
	echo "<hr>";
	}
 

?>
</body>
</html>