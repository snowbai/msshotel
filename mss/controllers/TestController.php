<?php
namespace mss\controllers;

use mss\components\MssController;
use common\Medeen;
use Yii;
use yii\web\Response;

/**
 * Site controller
 */
class TestController extends MssController
{


    /**
     * @inheritdoc
     */
    public function actions()
    {

    }

    public function actionIndex()
    {
      //return \Yii::$app->db1;
      //var_dump(Yii::$app);
      //return $this->render_json(true,['oo'=>'xxxxx0'],[]);
      return ['2134','2154155'=>'xxxxx'];
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


}
