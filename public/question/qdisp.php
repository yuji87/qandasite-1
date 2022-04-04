<script src=" https://code.jquery.com/jquery-3.4.1.min.js "></script>
<script src="/qandasite/public/question/js/like.js" defer></script>

<?php
session_start();

// ファイルの読み込み
require_once '../../app/QuestionLogic.php';
require_once '../../app/UserLogic.php';

// エラーメッセージ
$err = [];

// 詳細表示する質問の選択処理
$question_id = filter_input(INPUT_GET, 'question_id');
if(!$question_id = filter_input(INPUT_GET, 'question_id', FILTER_SANITIZE_SPECIAL_CHARS)) {
    $err[] = '質問を選択し直してください';
}

// 質問表示処理
if (count($err) === 0) {
    // 質問の取得
    $question = QuestionLogic::displayQuestion($_GET);
    if(!$question) {
        $err['question'] = '質問の読み込みに失敗しました';
    }
  // 質問返答の取得
  $answer = QuestionLogic::displayAnswer($_GET);
      if(!$answer) {
          $err['answer'] = '返信の読み込みに失敗しました';
      }
}

// いいね処理
if(isset($_POST['like_id'])) {
    if(isset($_POST['like_delete'])) {
        $like_btn = QuestionLogic::switchLike(0, $_POST['like_id']);
        if(!$like_btn) {
            $err['like'] = 'いいねの切り替えに失敗しました';
        }
    }
    if(isset($_POST['like_reregist'])) {
        $like_btn = QuestionLogic::switchLike(1, $_POST['like_id']);
        if(!$like_btn) {
            $err['like'] = 'いいねの切り替えに失敗しました';
        }
    }
}

// いいね登録処理
if(isset($_POST['like_regist'])) {
    // 登録処理
    $like_btn = QuestionLogic::createLike($_POST);
    if(!$like_btn) {
        $err['like'] = 'いいねの登録に失敗しました';
    }
    // 経験値を加算する処理
    $plusEXP = UserLogic::plusEXP($_SESSION['login_user']['user_id'], 5);
    if(!$plusEXP) {
        $err['plusEXP'] = '経験値加算処理に失敗しました';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <script src=" https://code.jquery.com/jquery-3.4.1.min.js "></script>
    <script src="/qandasite/public/question/js/like.js" defer></script>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/7bf203e5c7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="../css/mypage.css">
    <link rel="stylesheet" type="text/css" href="../css/top.css">
    <link rel="stylesheet" type="text/css" href="../css/question.css">
    <title>質問表示</title>
</head>

<body>
    <!--メニュー-->
    <header>
        <div class="navtext-container">
            <div class="navtext">Q&A SITE</div>
        </div>
        <input type="checkbox" class="menu-btn" id="menu-btn">
        <label for="menu-btn" class="menu-icon"><span class="navicon"></span></label>
        <ul class="menu">
            <li class="top"><a href="login_top.php">TOPページ</a></li>
            <li><a href="../userEdit/list.php">マイページ</a></li>
            <li><a href="#">TO DO LIST</a></li>
            <li>
                <form type="hidden" action="logout.php" method="POST">
				            <input type="submit" name="logout" value="ログアウト" id="logout" style="text-align:left;">
                </form>
            </li>
        </ul>
    </header>

    <!--コンテンツ-->
    <section class="wrapper">
        <div class="container">
            <div class="content">
                <!--質問の詳細表示-->
                <?php if(isset($err['question'])):  ?>
                    <?php echo $err['question']; ?>
                <?php endif; ?>
                <p class="h4 pb-3 pt-4">詳細表示</p>
                <!--アイコン-->
                <div class="pb-1 small">
                    <?php if(!isset($question['icon'])): ?>
                        <?php echo "<img src="."../user/img/sample_icon.png".">"; ?>
                    <?php else: ?>
                        <img src="../user/img/<?php echo $question['icon']; ?>">
                    <?php endif; ?>
                </div>
                <!--投稿者-->
                <div class="pb-4 small">投稿者：
                    <?php echo $question['name']; ?>
                </div>
                <!--題名-->
                <div class="fw-bold pb-1">題名</div>
                    <div><?php echo $question['title']; ?></div>
                <!--カテゴリー-->
                <div class="fw-bold pt-3 pb-1">カテゴリ</div>
                    <div><?php echo $question['category_name']; ?></div>
                <!--本文-->
                <div class="fw-bold pt-3 pb-1">本文</div>
                    <div><?php echo htmlspecialchars($question['message'], \ENT_QUOTES, 'UTF-8'); ?></div>
                <!--日付-->
                <div class="pt-4 pb-1 small">投稿日時：
                    <?php if (!isset($question['upd_date'])): ?>
                        <?php echo date('Y/m/d H:i', strtotime($question['post_date'])); ?>
                    <?php else: ?>
                        <?php echo $question['upd_date']; ?>
                    <?php endif; ?>
                </div>

                <!-- 質問者本人の時、編集・削除ボタン表示 -->
                <?php if($_SESSION['login_user']['user_id'] == $question['user_id']): ?>
                    <form method="POST" name="question" action="qEdit.php" id="qedit">
                        <input type="hidden" name="question_id" value="<?php echo $question_id; ?>">
                        <i class="fa-solid fa-pen"><input type="submit" id="edit" value="編集"></i>
                    </form>
                    <form method="POST" name="question" action="qDelete.php" id="qDelete">
                        <input type="hidden" name="question_id" value="<?php echo $question_id; ?>">
                        <i class="fa-solid fa-trash-can"><input type="submit" id="delete" value="削除"></i>
                    </form>
                <?php endif; ?>

                <!-- 返答表示部分 -->
                <?php if(!empty($answer)): ?>
                    <?php if(isset($err['answer'])):  ?>
                        <?php echo $err['answer']; ?>
                    <?php endif; ?>
                    <?php foreach($answer as $value): ?>
                        <!--ユーザー名-->
                        <div>名前：<?php echo $value['name']; ?></div>
                        <!--アイコン-->
                        <div>アイコン：
                        <div class="level-icon">
                            <?php if (isset($login_user['icon'])): ?> 
                                <img src="../img/<?php echo $login_user['icon']; ?>">
                            <?php else: ?>
                                <?php echo "<img src="."../../top/img/sample_icon.png".">"; ?>
                            <?php endif; ?>
                        </div>
                        <!--本文-->
                        <div>本文：<?php echo htmlspecialchars($value['message'], FILTER_SANITIZE_SPECIAL_CHARS, 'UTF-8'); ?></div>
                        <!--投稿日時-->
                        <div>
                            <!-- 更新されていた場合、その日付を優先表示 -->
                            <?php if (!isset($value['upd_date'])): ?>
                                投稿：<?php echo date('Y/m/d H:i', strtotime($value['answer_date']));  ?>
                            <?php else: ?>
                                更新：<?php echo date('Y/m/d H:i', strtotime($value['upd_date'])); ?>
                            <?php endif; ?>
                        </div>
                        <!-- フラグがONになっているいいねの数を表示 -->
                        <?php $likes = QuestionLogic::displayLike($value['answer_id']); ?>
                        <div>いいね数：<?php echo count($likes); ?></div>
                        <!-- ベストアンサー選択された返答の目印 -->
                        <?php if($value['best_flg']): ?>
                            <div>ベストアンサー選択されてます！！！！！</div>
                        <?php endif; ?>
                        <div class="col-sm-2">
                            <?php
                            if ($retinfo["article"]["USER_ID"] != $act->getMemberId()) {
                                if ($retinfo["postlike"] == NULL || $retinfo["postlike"]["LIKE_FLG"] == 0) {
                                    // いいねボタン押下で、いいねにする
                                    print('<a class="btn btn-primary" id="btnlike">いいね</a>');
                                } else {
                                    // いいね済み。ボタン押下で、いいねを解除
                                    print('<a class="btn btn-primary active" id="btnlike">いいね[済]</a>');
                                }
                            }
                            ?>
                        </div>
            
                        <!-- いいねボタンの表示部分 -->
                        <?php $checkLike = QuestionLogic::checkLike($_SESSION['login_user']['user_id'], $value['answer_id']); ?>
                        <form class="favorite_count" action="#" method="post">
                            <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['login_user']['user_id']; ?>">
                            <input type="hidden" id="answer_id" name="answer_id" value="<?php echo $value['answer_id']; ?>">
                            <!-- いいねの有無チェック -->
                            <?php if(!empty($checkLike)): ?>
                            <input type="hidden" name="like_id" value="<?php echo $checkLike['q_like_id']; ?>">
                            <!-- いいねがある場合 -->
                            <!-- いいねフラグが1の場合、いいね解除のボタンに -->
                            <?php if($checkLike['like_flg'] == 1): ?>
                                <input type="submit" name="like_delete" value="いいね仮">
                                <button type="button" name="like_delete" class="like_btn">
                                いいね解除
                            <!-- いいねフラグが0の場合、いいね再登録のボタンに -->
                            <?php else: ?>
                                <input type="submit" name="like_reregist" value="いいね仮">
                                <button type="button" name="like_reregist" data-answer_id="<?php echo $value['answer_id']; ?>" class="like_btn">
                                いいね再登録 <!-- どっちのいいねが表示されてるかの仮置き -->
                            <?php endif; ?>
                            <!-- いいねがない場合、いいね登録のボタンに -->
                            <?php else: ?>
                                <input type="submit" name="like_regist" value="いいね仮">
                                <button type="button" name="like_regist" class="like_btn">
                                いいね登録 <!-- どっちのいいねが表示されてるかの仮置き -->
                            <?php endif; ?>
                        </form>
          
                        <!-- 質問者本人 ＆ 返答が質問者以外の場合、ベストアンサーボタンの表示 -->
                        <?php if($_SESSION['login_user']['user_id'] == $question['user_id'] && $question['best_select_flg'] == 0 && $_SESSION['login_user']['user_id'] != $value['user_id']): ?>
                            <form method="POST" action="bestAnswer.php">
                                <input type="hidden" name="question_id" value="<?php echo $question_id; ?>">
                                <input type="hidden" name="answer_id" value="<?php echo $value['answer_id']; ?>">
                                <input type="submit" value="ベストアンサー">
                            </form>
                        <?php endif; ?>
    
                        <!-- 本人の返答に対して、返答の編集・削除ボタン表示 -->
                        <?php if($_SESSION['login_user']['user_id'] == $value['user_id']): ?>
                            <form method="POST" action="aEdit.php">
                                <input type="hidden" name="question_id" value="<?php echo $question_id; ?>">
                                <input type="hidden" name="answer_id" value="<?php echo $value['answer_id']; ?>">
                                <input type="submit" name="a_edit" value="編集">
                            </form>
                            <form method="POST" action="aDelete.php">
                                <input type="hidden" name="question_id" value="<?php echo $question_id; ?>">
                                <input type="hidden" name="answer_id" value="<?php echo $value['answer_id']; ?>">
                                <input type="submit" name="a_edit" value="削除">
                            </form>
                        <?php endif; ?>
                        <div>----------------</div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- ベストアンサーが選択されていると新規投稿できなくなる処理 -->
                <?php if($question['best_select_flg'] == 0): ?>
                    <form method="POST" action="aCreateConf.php">
                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['login_user']['user_id']; ?>">
                        <input type="hidden" name="question_id" value="<?php echo $question['question_id'] ?>">
                        <textarea placeholder="ここに返信を入力してください" name="a_message" class="w-75" rows="3"></textarea>
                        <br><input type="submit" class="btn btn-warning mt-2" value="返信">
                    </form>
                <?php endif; ?>
                <button type="button" class="mb-4 mt-3 btn btn-outline-dark" onclick="location.href='index.php'">戻る</button>
            </div>
        </div>
    </section>

    <!-- フッタ -->
    <footer class="h-10"><hr>
		    <div class="footer-item text-center">
		    	  <h4>Q&A SITE</h4>
		    	  <ul class="nav nav-pills nav-fill">
                <li class="nav-item">
		    			      <a class="nav-link small" href="../article/index.php">記事</a>
		    		    </li>
		    		    <li class="nav-item">
		    		    	  <a class="nav-link small" href="index.php">質問</a>
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

