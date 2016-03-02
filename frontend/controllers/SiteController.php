<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\ErrorException;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use YandexMoney\API;
use yii\base\DynamicModel;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $info = null;
        $authorized = false;
        $code = Yii::$app->request->get('code');
        try {
            if(Yii::$app->ym->isAuthorized()) {
                $authorized = true;
            } elseif($code) {
                $authorized = Yii::$app->ym->setAuthToken($code);
            }
        } catch(\Exception $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
        }
        return $this->render('index', ['url' => Yii::$app->ym->getAuthURL(), 'authorized' => $authorized]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionPay()
    {
        $request_payment = null;
        $process_payment = null;

        $model = new DynamicModel(['amount_due' => 1]);
        $model->addRule('amount_due', 'number');

        try {
            if(Yii::$app->ym->isAuthorized()) {
                $api = Yii::$app->ym->getApi();

                if($model->load(Yii::$app->request->post()) && $model->validate()) {

                    $request_payment = $api->requestPayment(array(
                        "pattern_id" => "p2p",
                        "to" => Yii::$app->params['wallet'],
                        "amount_due" => $model->amount_due,
                        "comment" => "Test YM payment to Nariman :)",
                        "message" => "Test YM payment to Nariman :)",
                    ));

                    if($request_payment->status == 'success') {
                        $process_payment = $api->processPayment(array(
                            "request_id" => $request_payment->request_id,
                        ));
                    } else {
                        throw new \Exception("Something gone wrong");
                    }
                }
            }
        } catch(\Exception $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
        }

        return $this->render('pay', [
            'model' => $model,
            'request_payment' => $request_payment,
            'process_payment' => $process_payment,
            'isAuth' => Yii::$app->ym->isAuthorized(),
        ]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        $history = null;
        $accInfo = null;

        try {
            if(Yii::$app->ym->isAuthorized()) {
                $api = Yii::$app->ym->getApi();

                $accInfo = $api->accountInfo();
                $history = $api->operationHistory(array("records" => 20));
            }
        } catch(\Exception $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
        }

        return $this->render('about', [
            'history'=>$history,
            'accInfo' =>$accInfo,
            'isAuth' => Yii::$app->ym->isAuthorized(),
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
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

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
