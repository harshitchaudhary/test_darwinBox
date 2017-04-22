<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\UserScreening */

$this->title = 'Create User Screening';
$this->params['breadcrumbs'][] = ['label' => 'User Screenings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-screening-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
