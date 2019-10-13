<?php
  session_start();
  require('dbconnect.php');

  if(empty($_REQUEST['id'])) {
    header('Location: index.php');
    exit();
  }

  $posts = $db->prepare('SELECT m.name, m.picture, p.*
  FROM members m, posts p WHERE m.id=p.member_id AND
  p.id=?');
  $posts->execute(array($_REQUEST['id']));

?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>メッセージ詳細</title>
    <link rel="stylesheet" href="css/html5reset-1.6.1.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
  </head>
  <body>
    <div class="wrap">
      <header>
        <i class="far fa-comments login_title_icon"></i><h1 class="login_title">メッセージ詳細</h1>
      </header>
      <div class="content">
        <p>&laquo;<a href="index.php">一覧にもどる</a></p>
        <br>
        
        <!-- メッセージ詳細の表示処理 -->
        <?php if ($post = $posts->fetch()): ?>
          <div class="msg">
            <img src="member_picture/<?php print(htmlspecialchars($post['picture'])); ?>" />
            <p><?php print(htmlspecialchars($post['message'])); ?><span class="name">
            （<?php print(htmlspecialchars($post['name'])); ?>）</span></p>
            <p class="day"><?php print(htmlspecialchars($post['created'])); ?></p>
          </div>
        <?php else: ?>
          <p>その投稿は削除されたか、URLが間違えています</p>
        <?php endif; ?>
      </div>
      <footer></footer>
    </div>
  </body>
</html>
