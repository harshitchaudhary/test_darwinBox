<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserScreening */

$this->title = 'Update User Screening: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Screenings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-screening-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
