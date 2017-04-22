<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserScreeningSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Screenings';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
@media (min-width: 768px) {
  #myModal .modal-dialog {
      min-width: 1024px;
      width: 90%;
  }
}
</style>
<div class="user-screening-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User Screening', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            [
              'label' => 'Student Name',
              'format' => 'html',
              'value' => function($model) {
                return Html::a($model->first_name .' '. $model->last_name, ['user-screening/open-preview', 'id' => $model->id], ['class' => 'preview-link']);
              },
            ],
            [
              'label' => 'STATUS',
              'value' => function($model){
                if($model->status == 0)
                {
                  return "Screening";
                }
                else if($model->status == 1)
                {
                  return "Selected";
                }
                else if($model->status == 2)
                {
                  return "Rejected";
                }
              }
            ],
            [
              'label' => 'Application Date',
              'value' => function($model) {
                return date('Y-m-d', strtotime($model->timestamp));
              }
            ],
            [
              'label' => 'Email & Phone',
              'format' => 'html',
              'value' => function($model) {
                return $model->email_id . '<br />' . $model->phone_number;
              }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

<!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Header</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col col-md-8 resume-preview">
            </div>
            <div class="col col-md-4 user-details">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>

<?php
  $this->registerJs('
    var statusUrl = "'.Url::to(['user-screening/update-status']).'" ;
    var previewUrl = "'.Url::to(['user-screening/open-preview']).'" ;
  ', View::POS_BEGIN);
  $this->registerJs('
    $(".preview-link").click(function(e) {
      e.preventDefault();
      $("#myModal").modal("show");
      var elem = $(this);;
      $.ajax({
        url : elem.attr("href"),
        method: "GET",
      }).done(function(data){
          var response = $.parseJSON(data);
          var file_src = response.model.cv ;
          var frame_html = "<iframe style=\'width:100%; height:600px;\' src=\''.Yii::$app->getUrlManager()->getBaseUrl().'/../uploads/"+file_src+"\'> </iframe>";
          $("#myModal .resume-preview").html(frame_html);
          var userAction = "<div class=\'row\'><div class=\'col col-md-6\'><button type=\"button\" data-id=\'"+response.model.id+"\' class=\"btn btn-success user-selected\">Selected</button></div><div class=\'col col-md-6\'><button type=\"button\"  data-id=\'"+response.model.id+"\' class=\"btn btn-danger user-rejected\">Rejected</button></div></div>";
            $("#myModal .user-details").html(userAction+"<hr />" + "<h3> Application Details </h3>");
          var userBody = "<div class=\'row\'> <div class=\'col col-md-4\'> <strong> First Name: </strong> </div> <div class=\'col col-md-8\'> "+response.model.first_name+" </div> </div>";
          userBody += "<div class=\'row\'> <div class=\'col col-md-4\'> <strong> Last Name: </strong> </div> <div class=\'col col-md-8\'> "+response.model.last_name+" </div> </div>";
          userBody += "<div class=\'row\'> <div class=\'col col-md-4\'> <strong> Email Id: </strong> </div> <div class=\'col col-md-8\'> "+response.model.email_id+" </div> </div>";
          userBody += "<div class=\'row\'> <div class=\'col col-md-4\'> <strong> Phone Number: </strong> </div> <div class=\'col col-md-8\'> "+response.model.phone_number+" </div> </div>";
          $("#myModal .user-details").append(userBody + "<hr />");

          var dataAction = "<div class=\'row\'> <div class=\'col col-md-6\'><button type=\"button\" data-id=\'"+response.prev+"\' class=\"btn btn-info nav-user\" "+(response.prev == null ? "disabled" : "" )+"> < Previous </button></div><div class=\'col col-md-6\'><button type=\"button\"  data-id=\'"+response.next+"\' class=\"btn btn-info nav-user\" "+(response.next == null ? "disabled" : "" )+"> Next > </button></div></div>";
          $("#myModal .user-details").append(dataAction + "<hr />");
      });

    });

    $("body").on("click", "#myModal .user-selected", function() {
        var elem = $(this)
        $.ajax({
          url: statusUrl + "&id=" + elem.attr("data-id") + "&type=selected",
          method: "GET",
        }).done(function(data){
            var statusResponse = $.parseJSON(data);
            var alertMessage = "";
            if(statusResponse.status == true)
            {
              alertMessage += "Success";
            }
            else
            {
              alertMessage += "Error/Failure";
            }
            alertMessage += statusResponse.message;
            alert(alertMessage);
        });
    });

    $("body").on("click", "#myModal .user-rejected", function() {
        var elem = $(this)
        $.ajax({
          url: statusUrl + "&id=" + elem.attr("data-id") + "&type=rejected",
          method: "GET",
        }).done(function(data){
            var statusResponse = $.parseJSON(data);
            var alertMessage = "";
            if(statusResponse.status == true)
            {
              alertMessage += "Success";
            }
            else
            {
              alertMessage += "Error/Failure";
            }
            alertMessage += statusResponse.message;
            alert(alertMessage);
        });
    });

    $("body").on("click", ".nav-user", function() {
        var elem = $(this);
        $.ajax({
          url: previewUrl + "&id=" + elem.attr("data-id"),
          method: "GET",
          beforeSend: function() {
            $("#myModal .user-details, #myModal .resume-preview").html("<img src =\'http://img.pandawhale.com/169593-vote-splash-Imgur-loader-gif-u-pVyP.gif\' style=\'width:96px; margin:0 auto;\' />");
          }
        }).done(function(data) {
          var response = $.parseJSON(data);
          var file_src = response.model.cv ;
          var frame_html = "<iframe style=\'width:100%; height:600px;\' src=\''.Yii::$app->getUrlManager()->getBaseUrl().'/../uploads/"+file_src+"\'> </iframe>";
          $("#myModal .resume-preview").html(frame_html);
          var userAction = "<div class=\'row\'><div class=\'col col-md-6\'><button type=\"button\" data-id=\'"+response.model.id+"\' class=\"btn btn-success user-selected\">Selected</button></div><div class=\'col col-md-6\'><button type=\"button\"  data-id=\'"+response.model.id+"\' class=\"btn btn-danger user-rejected\">Rejected</button></div></div>";
            $("#myModal .user-details").html(userAction+"<hr />" + "<h3> Application Details </h3>");
          var userBody = "<div class=\'row\'> <div class=\'col col-md-4\'> <strong> First Name: </strong> </div> <div class=\'col col-md-8\'> "+response.model.first_name+" </div> </div>";
          userBody += "<div class=\'row\'> <div class=\'col col-md-4\'> <strong> Last Name: </strong> </div> <div class=\'col col-md-8\'> "+response.model.last_name+" </div> </div>";
          userBody += "<div class=\'row\'> <div class=\'col col-md-4\'> <strong> Email Id: </strong> </div> <div class=\'col col-md-8\'> "+response.model.email_id+" </div> </div>";
          userBody += "<div class=\'row\'> <div class=\'col col-md-4\'> <strong> Phone Number: </strong> </div> <div class=\'col col-md-8\'> "+response.model.phone_number+" </div> </div>";
          $("#myModal .user-details").append(userBody + "<hr />");

          var dataAction = "<div class=\'row\'> <div class=\'col col-md-6\'><button type=\"button\" data-id=\'"+response.prev+"\' class=\"btn btn-info nav-user\" "+(response.prev == null ? "disabled" : "" )+"> < Previous </button></div><div class=\'col col-md-6\'><button type=\"button\"  data-id=\'"+response.next+"\' class=\"btn btn-info nav-user\" "+(response.next == null ? "disabled" : "" )+"> Next > </button></div></div>";
          $("#myModal .user-details").append(dataAction + "<hr />");
        })
    });

  ', View::POS_READY);
?>
