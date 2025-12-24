<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var $apples \common\models\Apple[] */

$this->registerJs(
    '$(".alert-dismissible").animate({opacity: 1.0}, 2000).fadeOut("slow");', yii\web\View::POS_READY
);

$this->title = 'Яблоки';

?>
    <style>
        em {
            font-weight: bold;
            color: #0a53be;
        }

        .apple-container {
            border: 2px dashed maroon;
            padding: 10px;
            margin-top: 10px;
        }
    </style>

<?= Html::beginForm(Url::to(['apple/generate']), 'get'); ?>
<?= Html::hiddenInput('count', 5); ?>
<?= Html::submitButton('Add apples'); ?>
<?= Html::endForm(); ?>

<?php foreach ($apples as $apple): ?>
    <div class='apple-container'>
        Цвет: <em><?= $apple->color ?></em><br>
        Статус: <em><?= $apple->status ?></em><br>
        Возраст: <em><?= (time() - $apple->created_at) ?> сек</em><br>
        Откушено: <em><?= $apple->eaten_percent ?> %</em>
        <hr>

        <div style="float:left;">
            <?= Html::beginForm(); ?>
            <?= Html::hiddenInput('id', $apple->id); ?>
            <?= Html::hiddenInput('action', 'fall'); ?>
            <?= Html::submitButton('Уронить'); ?>
            <?= Html::endForm(); ?>
        </div>

        <div style="float:left; margin-left: 50px;">
            <?= Html::beginForm(); ?>
            <?= Html::textInput('percent', 10, ['min' => 1, 'max' => 100]); ?>
            <?= Html::hiddenInput('id', $apple->id); ?>
            <?= Html::hiddenInput('action', 'eat'); ?>
            <?= Html::submitButton('Откусить'); ?>
            <?= Html::endForm(); ?>
        </div>
        <div style="clear: both;"></div>
    </div>
<?php endforeach;