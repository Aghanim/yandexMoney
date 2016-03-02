<?php
namespace common\components;

use Yii;
use yii\base\Component;
use YandexMoney\API;
use yii\base\InvalidConfigException;

class YM extends Component {
    /** @var  Array */
    public $scope = null;
    /** @var  int */
    public $client_id;
    /** @var  string */
    public $code = null;
    /** @var  string */
    public $redirect_uri;
    /** @var null|string */
    public $client_secret = null;
    /** @var  \YandexMoney\API */
    private $api = null;

    private $access_token = null;

    public function init()
    {
        if (!$this->client_id) {
            throw new InvalidConfigException("Client_id can't be empty!");
        }

        if (!$this->scope) {
            throw new InvalidConfigException("Scope can't be empty!");
        }

        if (!$this->redirect_uri) {
            throw new InvalidConfigException("Redirect_uri can't be empty!");
        }
    }

    public function getAuthUrl()
    {
        return API::buildObtainTokenUrl($this->client_id, $this->redirect_uri, $this->scope);
    }

    public function setAuthToken($code)
    {
        $access_token_response = API::getAccessToken(
            $this->client_id, $code, $this->redirect_uri, $this->client_secret);

        if(property_exists($access_token_response, "error")) {
            throw new \Exception("Error on fetching access token: " . $access_token_response->error);
        }
        $this->access_token = Yii::$app->security->encryptByPassword(
            $access_token_response->access_token, md5($this->client_secret));

        Yii::$app->session->set('ym_access_token', $this->access_token);

        return true;
    }

    public function getAccessToken()
    {
        if($this->access_token) {
            return $this->access_token;
        } elseif(Yii::$app->session->has('ym_access_token')) {
            $this->access_token = Yii::$app->session->get('ym_access_token');
            return $this->access_token;
        } else {
            throw new \Exception("Access token does not exist");
        }
    }

    public function revokeAuthToken()
    {
        $token = $this->decryptToken();
        $api = $this->getApi();

        $api->revokeToken($token);

        $this->access_token = null;

        Yii::$app->session->remove('ym_access_token');
    }

    public function decryptToken() {
        return Yii::$app->security->decryptByPassword($this->getAccessToken(), md5($this->client_secret));
    }

    /**
     * @return \YandexMoney\API
     */
    public function getApi()
    {
        if($this->api) {
            return $this->api;
        } else {
            try {
                $token = $this->decryptToken();
                $this->api = new API($token);
            } catch (\Exception $e) {

            }

            return $this->api;
        }
    }

    public function isAuthorized()
    {
        return ($this->access_token || Yii::$app->session->has('ym_access_token'));
    }
}