<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\broadcast;

use Yii;
use yii\base\Action;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * Class TopicAction
 * @package xutl\broadcast
 */
class TopicAction extends Action
{
    /**
     * @var callable
     */
    public $callback;

    /**
     * 初始化
     */
    public function init()
    {
        parent::init();
        $this->controller->enableCsrfValidation = false;
        $this->preInit();
    }

    /**
     * 执行前
     */
    public function preInit()
    {
        $tmpHeaders = [];
        foreach (Yii::$app->request->getHeaders() as $key => $val) {
            if (0 === strpos($key, 'x-mns-')) {
                $tmpHeaders[$key] = $val[0];
            }
        }
        ksort($tmpHeaders);
        $canonicalizedMnsHeaders = implode("\n", array_map(function ($v, $k) {
            return $k . ":" . $v;
        }, $tmpHeaders, array_keys($tmpHeaders)));

        $method = Yii::$app->request->method;
        $md5 = Yii::$app->request->getHeaders()->get('Content-MD5');
        $contentType = Yii::$app->request->getHeaders()->get('Content-Type');
        $date = Yii::$app->request->getHeaders()->get('Date');
        $canonicalizedResource = Yii::$app->request->url;
        $signature = Yii::$app->request->getHeaders()->get('Authorization');

        $stringToSign = strtoupper($method) . "\n" . $md5 . "\n" . $contentType . "\n" . $date . "\n" . $canonicalizedMnsHeaders . "\n" . $canonicalizedResource;

        $publicKey = $this->getPublicKey();

        $pass = $this->verifySign($stringToSign, $signature, $publicKey);
        if (!$pass) {//抛400
            throw  new BadRequestHttpException();
        }
        if (!empty($md5) && $md5 != base64_encode(md5(Yii::$app->request->rawBody))) {
            throw new UnauthorizedHttpException();
        }
    }

    /**
     * 获取URL内容
     * @param string $url
     * @return null|string
     */
    protected function getPublicKey()
    {
        $url = base64_decode(Yii::$app->request->getHeaders()->get('x-mns-signing-cert-url'));
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->send();
        if ($response->isOk) {
            return $response->content;
        }
        return null;
    }

    /**
     * 验证签名
     * @param string $stringToSign
     * @param string $signature
     * @param string $publicKey 公钥字符串
     * @return bool
     */
    protected function verifySign($stringToSign, $signature, $publicKey)
    {
        $res = openssl_get_publickey($publicKey);
        $result = (bool)openssl_verify($stringToSign, base64_decode($signature), $res);
        openssl_free_key($res);
        return $result;
    }

    /**
     * 执行
     * @throws UnauthorizedHttpException
     */
    public function run()
    {
        $params = Yii::$app->request->bodyParams;
        if ($this->callback) {
            return call_user_func($this->callback, $params);
        }
    }
}