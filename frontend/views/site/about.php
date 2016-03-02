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
    <div class="alert alert-warning <?= $isAuth ?  'collapse' : ''?>" role="alert">
        <p>Нужно авторизоваться</p>
    </div>
    <?php if($accInfo) {?>
        <div class="row">
            <div class="col-sm-7">
                <h1>Информация об аккаунте плательщика</h1>

                <table class="table table-striped">
                    <?php if(property_exists($accInfo, "account")): ?>
                        <tr><th class="text-right">Номер счета пользователя.</th><td><?= $accInfo->account?></td></tr>
                    <?php endif; ?>
                    <?php if(property_exists($accInfo, "balance")): ?>
                        <tr><th class="text-right">Баланс счета пользователя.</th><td><?= $accInfo->balance?> руб.</td></tr>
                    <?php endif; ?>
                    <?php if(property_exists($accInfo, "account_type")): ?>
                        <tr>
                            <th class="text-right">Тип счета пользователя.</th>
                            <td>
                                <?php switch($accInfo->account_type){
                                    case "personal":
                                        echo "обычный счет пользователя в Яндекс.Деньгах";
                                        break;
                                    case "professional":
                                        echo "профессиональный счет в Яндекс.Деньгах";
                                        break;
                                }?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if(property_exists($accInfo, "account_status")): ?>
                        <tr>
                            <th class="text-right">Статус пользователя.</th>
                            <td>
                                <?php switch($accInfo->account_status){
                                    case "anonymous":
                                        echo "анонимный счет";
                                        break;
                                    case "named":
                                        echo "именной счет";
                                        break;
                                    case "identified":
                                        echo "идентифицированный счет";
                                        break;
                                }?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if(property_exists($accInfo, "balance_details")){ ?>
                        <?php
                            $balance = $accInfo->balance_details;
                            if(property_exists($balance, "available")):
                        ?>
                            <tr><th class="text-right">Сумма доступная для расходных операций.</th><td><?= $balance->available?></td></tr>
                        <?php endif; ?>
                        <?php if(property_exists($balance, "blocked")):?>
                            <tr><th class="text-right">Сумма заблокированных средств по решению исполнительных органов.</th><td><?= $balance->blocked?></td></tr>
                        <?php endif; ?>
                        <?php if(property_exists($balance, "debt")):?>
                            <tr><th class="text-right">Сумма задолженности.</th><td><?= $balance->debt?></td></tr>
                        <?php endif; ?>
                        <?php if(property_exists($balance, "hold")):?>
                            <tr><th class="text-right">Сумма замороженных средств.</th><td><?= $balance->hold?></td></tr>
                        <?php endif; ?>
                    <?php } ?>
                </table>
                <?php if(property_exists($accInfo, "cards_linked")){ ?>
                    <h2>Информация о привязанных банковских картах.</h2>
                    <table class="table table-striped">
                        <?php
                            foreach($accInfo->cards_linked as $item) {?>
                                    <tr><th class="text-right">Маскированный номер карты.</th><td><?= $item['pan_fragment']?></td></tr>
                                    <tr><th class="text-right">Тип карты.</th><td><?= $item['type']?></td></tr>
                        <?php } ?>
                    </table>
                <?php } ?>
            </div>
        </div>
    <?php } //if($accInfo)?>

    <?php if($history) {?>
        <h1>История операций</h1>
        <?php if(property_exists($history, "operations")){ ?>
            <table class="table table-striped">
                <tr>
                    <th>Дата и время</th>
                    <th>Статус</th>
                    <th>Описание</th>
                    <th>Направление</th>
                    <th>Сумма</th>
                    <th>Тип операции</th>
                </tr>
                <?php
                foreach($history->operations as $item) {?>
                    <tr>
                        <td><?= date("d-m-Y H:i:s", strtotime($item->datetime)); ?></td>
                        <td><?= $item->status ?></td>
                        <td><?= $item->title ?></td>
                        <td>
                            <?php switch($item->direction) {
                                case "in":
                                    echo "Поступление";
                                    break;
                                case "out":
                                    echo "Расход";
                                    break;
                            }?>
                        </td>
                        <td><?= $item->amount ?> руб.</td>
                        <td>
                            <?php switch($item->type){
                                case "payment-shop":
                                    echo "Исходящий платеж в магазин";
                                    break;
                                case "outgoing-transfer":
                                    echo "Исходящий";
                                    break;
                                case "deposition":
                                    echo "Зачисление";
                                    break;
                                case "incoming-transfer":
                                    echo "Входящий перевод";
                                    break;
                                case "incoming-transfer-protected":
                                    echo "Входящий перевод с кодом протекции.";
                                    break;
                            }?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>

    <?php } //if($history)?>
</div>
