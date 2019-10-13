<?php
session_start();
require('dbconnect.php');

  // セッション機能
  if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    $_SESSION['time'] = time();
    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();
  } else {
    header('Location: login.php');
    exit();
  }

  // メッセージの投稿をDBに送信する処理
  if (!empty($_POST)) {
    if($_POST['message'] != '') {
      $message = $db->prepare('INSERT INTO posts SET member_id=?,
      message=?, reply_message_id=?, created=NOW()');
      $message->execute(array(
        $member['id'],
        $_POST['message'],
        $_POST['reply_post_id']
      ));
      header('Location: index.php');
      exit();
    }
  }

  // ページネーション
  $page = $_REQUEST['page'];
  if($page == '') {
    $page = 1;
  }
  $page = max($page, 1);
  $counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
  $cnt = $counts->fetch();
  $maxPage = ceil($cnt['cnt'] / 5);
  $page = min($page, $maxPage);
  $start = ($page - 1) * 5;

  // DBから投稿情報を取得する処理
  $posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members
  m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?,5');
  $posts->bindParam(1, $start, PDO::PARAM_INT);
  $posts->execute();

  // 返信の処理
  if(isset($_REQUEST['res'])) {
    $response = $db->prepare('SELECT m.name, m.picture,
    p.* FROM members m, posts p WHERE m.id=p.member_id
    AND p.id=?');
    $response->execute(array($_REQUEST['res']));
    $table = $response->fetch();
    $message = '@' . $table['name'] . ' ' . $table
    ['message'];
  }
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>BBS</title>
    <link rel="stylesheet" href="css/html5reset-1.6.1.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
  </head>
  <body>
    <div class="wrap">
      <header>
        <i class="far fa-comments login_title_icon"></i><h1 class="login_title">BBS</h1>
      </header>
      <div class="content">
        <div style="text-align: right"><a href="logout.php">ログアウト</a></div>
        <form action="" method="post">
          <dl>
            <dt><?php print(htmlspecialchars($member['name'], ENT_QUOTES)); ?>さん、メッセージをどうぞ</dt>
            <dd>
              <textarea class="bbsText" name="message" cols="50" rows="3"><?php print(htmlspecialchars($message, ENT_QUOTES)); ?></textarea>
              <input type="hidden" name="reply_post_id" value="<?php print(htmlspecialchars($_REQUEST['res'], ENT_QUOTES)); ?>" />
            </dd>
          </dl>
          <div>
            <p>
              <input type="submit" value="投稿する" style= " -webkit-appearance: none; width:120px;height:30px;">
            </p>
          </div>
        </form>
        <br>
        <!-- メッセージ一覧の表示処理 -->
        <?php foreach ($posts as $post): ?>
          <div class="msg">
            <img src="member_picture/<?php print(htmlspecialchars($post['picture'], ENT_QUOTES)); ?>"
            width="48" height="48" alt="<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>" />
            <p>
              <?php print(htmlspecialchars($post['message'], ENT_QUOTES)); ?>
              <span class="name">（<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>）</span>
              [<a href="index.php?res=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)); ?>">Re</a>]
            </p>
            <p class="day">
              <a href="view.php?id=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)); ?>">
                <?php print(htmlspecialchars($post['created'], ENT_QUOTES)); ?>
              </a>
              <?php if ($post['reply_message_id'] > 0): ?>
                <a href="view.php?id=<?php print(htmlspecialchars($post['reply_message_id'], ENT_QUOTES)); ?>" style="color: blue;">返信元のメッセージ</a>
              <?php endif; ?>
              <?php if($_SESSION['id'] == $post['member_id']): ?>
                [<a href="delete.php?id=<?php print(htmlspecialchars($post['id'])); ?>" style="color: #F33;">削除</a>]
              <?php endif; ?>
            </p>
          </div>
        <?php endforeach; ?>
        <ul class="paging">
          <?php if($page > 1): ?>
            <li><a href="index.php?page=<?php print($page-1); ?>">←前のページへ</a></li>
          <?php else: ?>
            <li></li>
          <?php endif; ?>
            <li>/</li>
          <?php if($page < $maxPage): ?>
            <li><a href="index.php?page=<?php print($page+1); ?>">次のページへ→</a></li>
          <?php else: ?>
            <li></li>
          <?php endif; ?>
        </ul>
      </div>
      <footer>
      </footer>
    </div>
  </body>
</html>