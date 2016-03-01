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
            <p><a class="btn btn-lg btn-primary" href="/site/about">Далее</a></p>
        <?php }?>
    </div>

    <div class="body-content">
<!--        <form method="POST" action="https://money.yandex.ru/quickpay/confirm.xml">-->
<!--            <input type="hidden" name="receiver" value="410011347493333">-->
<!--            <input type="hidden" name="formcomment" value="Проект «Железный человек»: реактор холодного ядерного синтеза">-->
<!--            <input type="hidden" name="short-dest" value="Проект «Железный человек»: реактор холодного ядерного синтеза">-->
<!--            <input type="hidden" name="label" value="$order_id">-->
<!--            <input type="hidden" name="quickpay-form" value="donate">-->
<!--            <input type="hidden" name="targets" value="транзакция 100500">-->
<!--            <input type="hidden" name="comment" value="Хотелось бы дистанционного управления.">-->
<!--            <input type="hidden" name="need-fio" value="false">-->
<!--            <input type="hidden" name="need-email" value="false">-->
<!--            <input type="hidden" name="need-phone" value="false">-->
<!--            <input type="hidden" name="need-address" value="false">-->
<!--            <input type="hidden" name="paymentType" value="PC">-->
<!--            <input type="text" name="sum" value="4568.25" data-type="number">-->
<!--            <input type="submit" value="Перевести">-->
<!--        </form>-->
<!--        <br/><br/><br/><br/><br/>-->
<!--        <iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/shop.xml?account=410013957869492&quickpay=shop&writer=seller&targets=magazin&targets-hint=&default-sum=&button-text=01&successURL=" width="450" height="162"></iframe>-->
    </div>
</div>
