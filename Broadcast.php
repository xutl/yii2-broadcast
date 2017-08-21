<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\broadcast;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use AliyunMNS\Config;
use AliyunMNS\Http\HttpClient;
use AliyunMNS\Requests\CreateTopicRequest;
use AliyunMNS\Requests\DeleteTopicRequest;
use AliyunMNS\Responses\CreateTopicResponse;
use AliyunMNS\Responses\DeleteTopicResponse;

/**
 * Class Broadcast
 * @package xutl\broadcast
 */
class Broadcast extends Component
{
    /**
     * @var  string
     */
    public $endPoint;

    /**
     * @var string
     */
    public $accessId;

    /**
     * @var string
     */
    public $accessKey;

    /**
     * @var null|string
     */
    public $securityToken = null;

    /** @var  string 默认操作的主题 */
    public $topicName;

    /**
     * @var null|Config
     */
    public $config = null;

    /**
     * @var HttpClient
     */
    protected $client;

    /**
     * 初始化组件
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (empty ($this->endPoint)) {
            throw new InvalidConfigException ('The "endPoint" property must be set.');
        }
        if (empty ($this->accessId)) {
            throw new InvalidConfigException ('The "accessId" property must be set.');
        }
        if (empty ($this->accessKey)) {
            throw new InvalidConfigException ('The "accessKey" property must be set.');
        }
        if (empty ($this->topicName)) {
            $this->topicName = Yii::$app->id;
        }
        $this->client = new HttpClient($this->endPoint, $this->accessId, $this->accessKey, $this->securityToken, $this->config);
    }

    /**
     * 获取主题(广播)
     * @param string $topicName
     * @return Topic
     */
    public function getTopicRef($topicName = null)
    {
        return new Topic([
            'client' => $this->client,
            'topicName' => !is_null($topicName) ? $topicName : $this->topicName
        ]);
    }

    /**
     * 向主题推送消息
     * @param string|array $messageBody
     * @param string $messageTag
     * @param null $messageAttributes
     * @param string $topicName
     * @return \AliyunMNS\Responses\BaseResponse
     */
    public function publishMessage($messageBody, $messageTag = null, $messageAttributes = null, $topicName = null)
    {
        $topic = $this->getTopicRef($topicName);
        return $topic->publishMessage($messageBody, $messageTag, $messageAttributes);
    }

    /**
     * 创建主题
     * @param string $topicName
     * @return \AliyunMNS\Responses\BaseResponse
     */
    public function create($topicName)
    {
        $request = new CreateTopicRequest($topicName);
        $response = new CreateTopicResponse($request->getTopicName());
        return $this->client->sendRequest($request, $response);
    }

    /**
     * 删除主题
     * @param string $topicName
     * @return \AliyunMNS\Responses\BaseResponse
     */
    public function delete($topicName)
    {
        $request = new DeleteTopicRequest($topicName);
        $response = new DeleteTopicResponse();
        return $this->client->sendRequest($request, $response);
    }
}