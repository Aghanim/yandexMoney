<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <div class="alert alert-error <?= Yii::$app->session->hasFlash('error') ?  'collapse' : ''?>" role="alert">
        <p><?= Yii::$app->session->getFlash('error'); ?></p>
    </div>
    <?php print_r($accInfo) ?>
    <br/><br/>
    <?php print_r($request_payment) ?>
    <br/><br/>
    <?php print_r($process_payment) ?>
    <h1>История операций</h1>
    <p>This is the About page. You may modify the following file to customize its content:</p>

    <code><?= __FILE__ ?></code>
</div>
