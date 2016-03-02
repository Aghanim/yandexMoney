<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Pay';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <div class="alert alert-error <?= Yii::$app->session->hasFlash('error') ?  'collapse' : ''?>" role="alert">
        <p><?= Yii::$app->session->getFlash('error'); ?></p>
    </div>
    <div class="alert alert-warning <?= $isAuth ?  'collapse' : ''?>" role="alert">
        <p>Нужно авторизоваться</p>
    </div>
    <?php if($process_payment && $request_payment) {?>
        <div class="alert alert-success <?= $process_payment ?  '' : 'collapse'?>" role="alert">
            <table class="table">
                <?php if(property_exists($process_payment, "status")): ?>
                    <tr>
                        <th class="text-right">Статус перации.</th>
                        <td>
                            <?php switch($process_payment->status){
                                case "success":
                                    echo "Успешно";
                                    break;
                                case "refused":
                                    echo "Отказано";
                                    break;
                                case "in_progress":
                                    echo "Повторите запрос пожже";
                                    break;
                                default:
                                    echo "Что то пошло не так :(";
                                    break;
                            }?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if(property_exists($request_payment, "contract_amount")): ?>
                    <tr>
                        <th class="text-right">Списано:</th>
                        <td><?= $request_payment->contract_amount ?> руб.</td>
                    </tr>
                <?php endif; ?>
                <?php if(property_exists($process_payment, "credit_amount")): ?>
                    <tr>
                        <th class="text-right">Оплачено:</th>
                        <td><?= $process_payment->credit_amount ?> руб.</td>
                    </tr>
                <?php endif; ?>
                <?php if(property_exists($process_payment, "balance")): ?>
                    <tr>
                        <th class="text-right">Остаток:</th>
                        <td><?= $process_payment->balance ?> руб.</td>
                    </tr>
                <?php endif; ?>
                <?php if(property_exists($process_payment, "payer")): ?>
                    <tr>
                        <th class="text-right">Номер отправителя:</th>
                        <td><?= $process_payment->payer ?></td>
                    </tr>
                <?php endif; ?>
                <?php if(property_exists($process_payment, "payee")): ?>
                    <tr>
                        <th class="text-right">Номер получателя:</th>
                        <td><?= $process_payment->payee ?></td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    <?php }
        if($isAuth) {
            ?>
            <h4>Внимание спишуться ваши денюжки!</h4>
            <?php
            $form = ActiveForm::begin([
                'id' => 'pay-form',
                'options' => ['class' => 'form-horizontal'],
            ])
            ?>
            <div>
                <div class="col-sm-2">
                    <?= $form->field($model, 'amount_due')->label(false) ?>
                </div>
                <div class="col-sm-4">
                    <?= Html::submitButton('Тестовый платеж', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
            <?php ActiveForm::end();
        }
        ?>
</div>
