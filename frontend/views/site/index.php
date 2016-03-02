<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <div class="alert alert-error <?= Yii::$app->session->hasFlash('error') ?  'collapse' : ''?>" role="alert">
        <p><?= Yii::$app->session->getFlash('error'); ?></p>
    </div>
    <div class="jumbotron">
        <h1>Congratulations!</h1>
        <?php if (!$authorized) { ?>
            <p class="lead">Для того чтобы совершать платежи вам необходимо авторизоваться.</p>
            <p><a class="btn btn-lg btn-success" href="<?=$url?>">Aвторизоваться в Yandex Money</a></p>
        <?php } else {?>
            <p class="lead">Вы успешно авторизованы.</p>
            <p><a class="btn btn-lg btn-info" href="/site/pay">Сделать платеж</a>
                <a class="btn btn-lg btn-warning" href="/site/about">Аккаунт</a></p>
        <?php }?>
    </div>
</div>
