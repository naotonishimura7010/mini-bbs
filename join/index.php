<?php
session_start();
require('../dbconnect.php');
// 項目の入力有無をチェック
if (!empty($_POST)) {
		if ($_POST['name'] === '') {
				$error['name'] = 'blank';
		}
		if ($_POST['email'] === '') {
				$error['email'] = 'blank';
		}
		if (strlen($_POST['password']) < 4) {
				$error['password'] = 'length';
		}
		if ($_POST['password'] === '') {
				$error['password'] = 'blank';
		}
		// 画像以外のアップロードをエラーにする処理
		$fileName = $_FILES['image']['name'];
		if (!empty($fileName)) {
				$ext = substr($fileName, -3);
			if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
					$error['image'] = 'type';
			}
		}

		// アカウントの重複をチェック
		if (empty($error)) {
				$member = $db->prepare('SELECT COUNT(*) AS cnt
				FROM members WHERE email=?');
				$member->execute(array($_POST['email']));
				$record = $member->fetch();
				if ($record['cnt'] > 0) {
						$error['email'] = 'duplicate';
				}
		}
		
		// 上記全てにエラーが無い場合の処理
		if (empty($error)) {
				// アップロードした画像にファイル名を付加する処理
				$image = date('YmdHis') . $_FILES['image']['name'];
				// ファイルをmember_pictureに保存する処理
				move_uploaded_file($_FILES['image']['tmp_name'],
				'../member_picture/' . $image);
				// check.phpに値を参照させるための処理
				$_SESSION['join'] = $_POST;
				$_SESSION['join']['image'] = $image;
				// check.phpにジャンプする処理
				header('Location: check.php');
				exit();
		}
}
// check.phpにて書き直しを選択した場合に入力した値を再現する処理
if ($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])) {
		$_POST = $_SESSION['join'];
}
?>

<!DOCTYPE html>

<head>
<meta charset="utf-8">
<title>会員登録</title>
<link rel="stylesheet" href="../css/html5reset-1.6.1.css">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
</head>

<body>
	<div class="wrap">
		<header>
			<i class="far fa-comments login_title_icon"></i><h1 class="login_title">会員登録</h1>
		</header>
		<div class="content">
			<div class="lead">
				<p>次のフォームに必要事項をご記入ください。</p>
			</div>
			<form action="" method="post" enctype="multipart/form-data">
				<dl>
					<dt>ニックネーム<span class="required">必須</span></dt>
					<dd>
								<input type="text" name="name" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['name'], ENT_QUOTES)); ?>" />
								<?php if ($error['name'] === 'blank'): ?>
								<p class="error">* ニックネームを入力してください</p>
								<?php endif; ?>
					</dd>
					<dt>メールアドレス<span class="required">必須</span></dt>
					<dd>
						<input type="text" name="email" size="35" maxlength="255" value="<?php print(htmlspecialchars($_POST['email'], ENT_QUOTES)); ?>" />
						<?php if ($error['email'] === 'blank'): ?>
						<p class="error">* メールアドレスを入力してください</p>
						<?php endif; ?>
						<?php if ($error['email'] === 'duplicate'): ?>
						<p class="error">* 指定されたメールアドレスは、すでに登録されています。</p>
						<?php endif; ?>
					<dt>パスワード<span class="required">必須</span></dt>
					<dd>
						<input type="password" name="password" size="10" maxlength="20" value="<?php print(htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>" />
						<?php if ($error['password'] === 'length'): ?>
						<p class="error">* パスワードは４文字以上で入力してください</p>
						<?php endif; ?>
						<?php if ($error['password'] === 'blank'): ?>
						<p class="error">* パスワードを入力してください</p>
						<?php endif; ?>
					</dd>
					<dt>写真など</dt>
					<dd>
						<input type="file" name="image" size="35" value="test"  />
						<?php if ($error['image'] === 'type'): ?>
						<p class="error">* 写真などは「.gif」または「.jpg」「.png」の画像を指定してください</p>
						<?php endif; ?>
						<?php if (!empty($error)): ?>
						<p class="error">* 恐れ入りますが、画像を改めて指定してください。</p>
						<?php endif; ?>
					</dd>
				</dl>
				<div><input type="submit" value="入力内容を確認する" /></div>
			</form>
		</div>
	<footer>
	</footer>
	</div>
</body>
</html>
