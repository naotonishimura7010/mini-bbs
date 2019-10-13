<?php
session_start();
require('../dbconnect.php');

// 不正にcheck.phpが呼び出された場合index.phpに強制移動させる処理
if (!isset($_SESSION['join'])) {
		header('Location: index.php');
		exit();
}

// データーベースへの登録処理
if (!empty($_POST)) {
		$statement = $db->prepare('INSERT INTO members SET
		name=?, email=?, password=?, picture=?, created=NOW()');
		echo $statement->execute(array(
			$_SESSION['join']['name'],
			$_SESSION['join']['email'],
			// パスワードの暗号化処理
			sha1($_SESSION['join']['password']),
			$_SESSION['join']['image']
		));
		// セッション変数の削除処理
		unset($_SESSION['join']);
		header('Location: thanks.php');
		exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>確認画面</title>
<link rel="stylesheet" href="../css/html5reset-1.6.1.css">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
</head>

<body>
	<div class="wrap">

		<header>
			<i class="far fa-comments login_title_icon"></i><h1 class="login_title">確認画面</h1>
		</header>

	<div class="content">
	<p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>
	<form action="" method="post">
		<input type="hidden" name="action" value="submit" />
		<dl>
			<dt>ニックネーム</dt>
			<dd>
			<?php print(htmlspecialchars($_SESSION['join']
			['name'], ENT_QUOTES)); ?>
					</dd>
			<dt>メールアドレス</dt>
			<dd>
			<?php print(htmlspecialchars($_SESSION['join']
			['email'], ENT_QUOTES)); ?>
					</dd>
			<dt>パスワード</dt>
			<dd>
			【表示されません】
			</dd>
			<dt>写真など</dt>
			<dd>
			<?php if ($_SESSION['join']['image'] !== ''): ?>
				<img src="../member_picture/<?php print(htmlspecialchars
				($_SESSION['join']['image'], ENT_QUOTES)); ?>">
			<?php endif; ?>
			</dd>
		</dl>
		<div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a>  /  <input type="submit" value="登録する" style= " -webkit-appearance: none; width:100px;height:20px;" /></div>
	</form>
	</div>
		<footer></footer>
	</div>
</body>
</html>
