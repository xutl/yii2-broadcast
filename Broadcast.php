<?php

namespace xutl\broadcast;


use yii\base\Component;
use yii\base\InvalidConfigException;
use AliyunMNS\Client;
use AliyunMNS\Topic;

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
     * @var Client
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
        $this->client = new Client($this->endPoint, $this->accessId, $this->accessKey);
    }

    /**
     * 获取主题(广播)
     * @param string $topic
     * @return Topic
     */
    public function getTopicRef($topic)
    {
        return $this->client->getTopicRef($topic);
    }
}