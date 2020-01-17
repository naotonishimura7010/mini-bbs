<?php
session_start();
// DBに接続する共通プログラムの参照
require('dbconnect.php');

if ($_COOKIE['email'] !== '') {
  $email = $_COOKIE['email'];
}
if (!empty($_POST)) {
  $email = $_POST['email'];
  
  if($_POST['email'] !== '' && $_POST['password'] !== '') {
    // DBのデータと照会
    $login = $db->prepare('SELECT * FROM members WHERE email=?
    AND password=?');
    $login->execute(array(
      $_POST['email'],
      // 暗号化されたパスワードの参照
      sha1($_POST['password'])
    ));
    $member = $login->fetch();
    
    // ログインに成功した場合の処理
    if($member) {
      // セッションの保存
      $_SESSION['id'] = $member['id'];
      $_SESSION['time'] = time();

    // 次回から自動的にログインする場合の処理
    if ($_POST['save'] === 'on') {
      setcookie('email', $_POST['email'], time()+60*60*24*14);
    }
      //index.phpにアクセスする処理
      header('Location: index.php');
      exit();
    //ログインに失敗した場合の処理
    } else {
      $error['login'] = 'failed';
    }
  } else {
    $error['login'] = 'blank';
  }
}
?>

<!doctype html>
<html>

<head>
<meta charset="utf-8">
<title>ログイン画面</title>
<link rel="stylesheet" href="css/html5reset-1.6.1.css">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
</head>

<body>
  <div class="wrap">
    <header>
      <i class="far fa-comments login_title_icon"></i><h1 class="login_title">ログイン画面</h1>
    </header>
    <div class="content">
      <div class="lead">
        <p>メールアドレスとパスワードを記入してログインしてください。</p>
        <p>入会手続きがまだの方はこちらからどうぞ。</p>
        <p>&raquo;<a href="join/">入会手続きをする</a></p>
      </div>
      <form action="" method="post">
        <dl>
          <dt>メールアドレス</dt>
          <dd>
            <input type="text" name="email" size="35" maxlength="255"
            value="<?php print(htmlspecialchars($_POST['email'], ENT_QUOTES)); ?>">
            <?php if ($error['login'] === 'blank'): ?>
            <p class="error">* メールアドレスとパスワードをご記入ください</p>
            <?php endif ?>
            <?php if ($error['login'] === 'failed'): ?>
            <p class="error">* ログインに失敗しました。正しくご記入ください</p>
            <?php endif ?>
          </dd>
          <dt>パスワード</dt>
          <dd>
            <input type="password" name="password" size="35" maxlength="255"
            value="<?php print(htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>">
          </dd>
          <dt>ログイン情報の記録</dt>
          <dd>
            <input id="save" type="checkbox" name="save" value="on">
            <label for="save">次回からは自動的にログインする</label>
          </dd>
        </dl>
        <div>
          <input type="submit" value="ログインする">
        </div>
      </form>
    </div>
    <footer>
      
    </footer>
  </div>
</body>

</html>
