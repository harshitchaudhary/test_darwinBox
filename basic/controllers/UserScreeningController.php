<?php

namespace app\controllers;

use Yii;
use app\models\UserScreening;
use app\models\UserScreeningSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\Json;
use yii\base\ErrorException;
use yii\helpers\Url;
/**
 * UserScreeningController implements the CRUD actions for UserScreening model.
 */
class UserScreeningController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all UserScreening models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserScreeningSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserScreening model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserScreening model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserScreening();
        if(Yii::$app->request->isPost)
        {   $model->load(Yii::$app->request->post());
            $model->file = UploadedFile::getInstance($model, 'file');
            $filename = Yii::$app->security->generateRandomString(32) . '_' . $model->file->baseName . '.' . $model->file->extension;
            $bool_saved = $model->file->saveAs(Yii::getAlias('@webroot').'/../uploads/' . $filename);
            if($bool_saved)
            {
              $model->status = 0;
              $model->cv = $filename;
              $model->file = null; // So that on saving it should not look for it again
              if($model->save()) {
                  return $this->redirect(Url::to(['user-screening/index']));
              }
              else {
                throw new ErrorException(Json::encode($model->getErrors()));
              }
            }
            else {
                echo "Error in uploading file" ; exit;
            }
        }
        else
        {
          $model = new UserScreening();
          return $this->render('create', [
            'model' => $model
          ]);
        }
    }

    /**
     * Updates an existing UserScreening model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UserScreening model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * This function is used for previewing the application
     * @param integer $id id of the model need to be previewed
     * @return mixed
     */
    public function actionOpenPreview($id)
    {
      $model = UserScreening::findOne($id);
      $next = $this->traverse($id, 'next');
      $prev = $this->traverse($id, 'previous');
      return Json::encode([
        'model' => $model,
        'next' => $next,
        'prev' => $prev
      ]);
    }

    /**
      * This function will be used for updating the status of the applications
      * @param interger $id id of the model for which status need to be updated
      * @param string $type nature of the call, on this nature the status will be updated as 1, 2 i.e. selected, rejected respectively
      * @return mixed
      */
    public function actionUpdateStatus($id, $type="selected")
    {
      $response = array();
      $model = UserScreening::findOne($id);
      if(!empty($model))
      {
        if($type == "selected")
        {
            $model->status = 1;
        }
        else if($type == "rejected")
        {
          $model->status = 2;
        }
        else
        {
            $response = [
              'status' => false,
              'message' => 'Invalid request type'
            ];

            return Json::encode($response);
        }
        if($model->save())
        {
            $response = [
              'status' => true,
              'message' => 'you have updated status as '. $type .' successfully ',
            ];
            return Json::encode($response);
        }
        else
        {
          throw new ErrorException(Json::encode($model->getErrors));
        }
      }
      else
      {
        $response = [
          'status' => false,
          'message' => 'No such record found'
        ];
        return Json::encode($response);
      }
    }

    /**
     * This function is used to traverse the sql next and prev id which are under Screenings
     * @param integer $id id of the node for which we need to find the successor and predecessor
     * @param string $direction direction decides whether we need to find successor or predecessor
     * @return bool
     */

    protected function traverse($id, $direction="next") {
      $max = UserScreening::find()->select('id')->max('id');
      $min = UserScreening::find()->select('id')->min('id');
      if($direction == 'next')
      {
        $next = null;
        while(1)
        {
          if($id >= $max)
          {
            return null;
          }
          if($next = $this->modelExists(++$id))
          {
            return $next;
          }
        }
      }
      if($direction == 'previous')
      {
        $prev = null;
        while(1)
        {
          if($id <= $min)
          {
            return null;
          }
          if($prev = $this->modelExists(--$id))
          {
            return $prev;
          }
        }
      }
    }

    /**
     * Finds the UserScreening model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserScreening the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserScreening::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function modelExists($id)
    {
      $model = UserScreening::findOne(['id' => $id, 'status' => 0]);
      if(empty($model))
      {
        return false;
      }
      else
      {
        return $id;
      }
    }
}
