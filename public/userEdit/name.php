<?php
session_start();

// ファイル読み込み
require_once '../../app/UserLogic.php';
require_once '../../app/Functions.php';

// エラーメッセージ
$err = [];

// ログインしているか判定して、していなかったらログインへ移す
$result = UserLogic::checkLogin();
if(!$result) {
    $_SESSION['login_err'] = '再度ログインして下さい';
    header('Location: ../userLogin/form.php');
    return;
}
$login_user = $_SESSION['login_user'];

// セッションに保存データがあるかを確認
if(isset($_SESSION['nameEdit'])) {
    // セッションから情報を取得
    $name = $_SESSION['nameEdit'];
} else {
    // セッションがなかった場合
    $name = array();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/mypage.css">
    <link rel="stylesheet" type="text/css" href="../css/top.css">
    <title>会員情報変更[name]</title>
</head>

<body>
    <!--メニュー-->
    <header>
        <div class="navbar bg-dark text-white">
            <div class="navtext h2" id="headerlogo">novus</div>
            <ul class="nav justify-content-center">
            <li id="li"><a class="nav-link active small text-white" href="../userLogin/home.php">TOPページ</a></li>
            <li id="li"><a class="nav-link active small text-white" href="../myPage/index.php">MyPageに戻る</a></li>
			<li id="li"><a class="nav-link active small text-white" href="../userEdit/index.php">【編集】会員情報</a></li>
            <li id="li"><a class="nav-link small text-white" href="qHistory.php">【履歴】質問</a></li>
            <li id="li"><a class="nav-link small text-white" href="aHistory.php">【履歴】記事</a></li>
            <li id="li"><a class="nav-link small text-white" href="<?php echo "logout.php?=user_id=".$login_user['user_id']; ?>">ログアウト</a></li>
        </ul>
        </div>
    </header>

    <div class="wrapper">
        <div class="container">
            <div class="content">
                <h2 class="heading mt-5">アカウント編集画面</h2>
                <form action="nameC.php" method="POST" name="confirm">
                    <input type="hidden" name="formcheck" value="checked">
                    <div class="list">
                        <!--ユーザーが登録した名前を表示-->
                        <div class="text">
                            <label for="name" style="text-align:center">[Name]</label>
                            <p><input id="editdetail" type="text" name="name" value="<?php echo htmlspecialchars($login_user['name'], ENT_QUOTES, 'UTF-8'); ?>"></p>
                            <p class="small text-muted">（15文字以下）</p>
                        </div>
                        <br><br>
                        <a href="index.php" id="back">戻る</a>
                        <p><input type="submit" class="mt-3" value="変更"></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- フッタ -->
    <footer class="h-10"><hr>
		<div class="footer-item text-center">
			<h3>novus</h3>
			<ul class="nav nav-pills nav-fill">
                <li class="nav-item">
				    <a class="nav-link small" href="../article/index.php">記事</a>
				</li>
				<li class="nav-item">
					<a class="nav-link small" href="../question/index.php">質問</a>
				</li>
				<li class="nav-item">
					<a class="nav-link small" href="../bookApi/index.php">本検索</a>
				</li>
				<li class="nav-item">
					<a class="nav-link small" href="../contact/index.php">お問い合わせ</a>
				</li>
			</ul>
		</div>
		<p class="text-center small mt-2">Copyright (c) HTMQ All Rights Reserved.</p>
  	</footer>
</body>
</html>
