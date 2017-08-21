<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\broadcast;

use AliyunMNS\Model\SubscriptionAttributes;
use AliyunMNS\Model\TopicAttributes;
use AliyunMNS\Model\UpdateSubscriptionAttributes;
use AliyunMNS\Requests\GetSubscriptionAttributeRequest;
use AliyunMNS\Requests\GetTopicAttributeRequest;
use AliyunMNS\Requests\ListSubscriptionRequest;
use AliyunMNS\Requests\SetSubscriptionAttributeRequest;
use AliyunMNS\Requests\SetTopicAttributeRequest;
use AliyunMNS\Requests\SubscribeRequest;
use AliyunMNS\Requests\UnsubscribeRequest;
use AliyunMNS\Responses\GetSubscriptionAttributeResponse;
use AliyunMNS\Responses\GetTopicAttributeResponse;
use AliyunMNS\Responses\ListSubscriptionResponse;
use AliyunMNS\Responses\PublishMessageResponse;
use AliyunMNS\Responses\SetSubscriptionAttributeResponse;
use AliyunMNS\Responses\SetTopicAttributeResponse;
use AliyunMNS\Responses\SubscribeResponse;
use AliyunMNS\Responses\UnsubscribeResponse;
use yii\base\Object;
use AliyunMNS\Client;
use AliyunMNS\Topic as TopicBackend;
use AliyunMNS\Requests\PublishMessageRequest;

/**
 * Class Topic
 * @package xutl\broadcast
 */
class Topic extends Object
{
    /**
     * @var \AliyunMNS\Http\HttpClient;
     */
    public $client;

    /**
     * @var string 主题名称
     */
    public $topicName;

    /**
     * 返回主题名称
     * @return string
     */
    public function getTopicName()
    {
        return $this->topicName;
    }

    /**
     * 设置主题属性
     * @param TopicAttributes $attributes
     * @return \AliyunMNS\Responses\BaseResponse
     */
    public function setAttribute(TopicAttributes $attributes)
    {
        $request = new SetTopicAttributeRequest($this->topicName, $attributes);
        $response = new SetTopicAttributeResponse();
        return $this->client->sendRequest($request, $response);
    }

    /**
     * 获取主题属性
     * @return \AliyunMNS\Responses\BaseResponse
     */
    public function getAttribute()
    {
        $request = new GetTopicAttributeRequest($this->topicName);
        $response = new GetTopicAttributeResponse();
        return $this->client->sendRequest($request, $response);
    }

    /**
     * 生成队列接入点
     * @param string $queueName
     * @return string
     */
    public function generateQueueEndpoint($queueName)
    {
        return "acs:mns:" . $this->client->getRegion() . ":" . $this->client->getAccountId() . ":queues/" . $queueName;
    }

    /**
     * 生成邮件接入点
     * @param string $mailAddress
     * @return string
     */
    public function generateMailEndpoint($mailAddress)
    {
        return "mail:directmail:" . $mailAddress;
    }

    /**
     * 生成短信接入点
     * @param null $phone
     * @return string
     */
    public function generateSmsEndpoint($phone = null)
    {
        if ($phone) {
            return "sms:directsms:" . $phone;
        } else {
            return "sms:directsms:anonymous";
        }
    }

    /**
     * 生成批量短信接入点
     * @return string
     */
    public function generateBatchSmsEndpoint()
    {
        return "sms:directsms:anonymous";
    }

    /**
     * 向主题推送消息
     * @param string $messageBody
     * @param string $messageTag
     * @param null $messageAttributes
     * @return \AliyunMNS\Responses\BaseResponse
     */
    public function publishMessage($messageBody, $messageTag = null, $messageAttributes = null)
    {
        $request = new PublishMessageRequest($messageBody, $messageTag, $messageAttributes);
        $request->setTopicName($this->topicName);
        $response = new PublishMessageResponse();
        return $this->client->sendRequest($request, $response);
    }

    /**
     * 创建主题订阅
     * @param null|string $subscriptionName 订阅名称
     * @param null|string $endPoint 接收端地址
     * @param null|string $tag 消息过滤标签
     * @param null|string $strategy 重试策略
     * @param null|string $contentFormat 消息推送格式
     * @param null|string $topicName 主题名称
     * @param null|string $topicOwner 主题所有者
     * @param null|string $createTime 创建时间
     * @param null|string $lastModifyTime 最后修改时间
     * @return \AliyunMNS\Responses\BaseResponse
     */
    public function subscribe($subscriptionName = null, $endPoint = null, $tag = null, $strategy = null, $contentFormat = null, $topicName = null, $topicOwner = null, $createTime = null, $lastModifyTime = null)
    {
        $attributes = new SubscriptionAttributes($subscriptionName, $endPoint, $tag, $strategy, $contentFormat, $topicName, $topicOwner, $createTime, $lastModifyTime);
        $attributes->setTopicName($this->topicName);
        $request = new SubscribeRequest($attributes);
        $response = new SubscribeResponse();
        return $this->client->sendRequest($request, $response);
    }

    /**
     * 解除主题订阅
     * @param string $subscriptionName 订阅名称
     * @return \AliyunMNS\Responses\BaseResponse
     */
    public function unSubscribe($subscriptionName)
    {
        $request = new UnsubscribeRequest($this->topicName, $subscriptionName);
        $response = new UnsubscribeResponse();
        return $this->client->sendRequest($request, $response);
    }

    /**
     * 获取指定订阅的属性
     * @param string $subscriptionName
     * @return \AliyunMNS\Responses\BaseResponse
     */
    public function getSubscriptionAttribute($subscriptionName)
    {
        $request = new GetSubscriptionAttributeRequest($this->topicName, $subscriptionName);
        $response = new GetSubscriptionAttributeResponse();
        return $this->client->sendRequest($request, $response);
    }

    /**
     * 设置订阅属性
     * @param UpdateSubscriptionAttributes $attributes
     * @return \AliyunMNS\Responses\BaseResponse
     */
    public function setSubscriptionAttribute(UpdateSubscriptionAttributes $attributes)
    {
        $attributes->setTopicName($this->topicName);
        $request = new SetSubscriptionAttributeRequest($attributes);
        $response = new SetSubscriptionAttributeResponse();
        return $this->client->sendRequest($request, $response);
    }

    /**
     * 获取订阅列表
     * @param null $retNum
     * @param null $prefix
     * @param null $marker
     * @return \AliyunMNS\Responses\BaseResponse
     */
    public function listSubscription($retNum = NULL, $prefix = NULL, $marker = NULL)
    {
        $request = new ListSubscriptionRequest($this->topicName, $retNum, $prefix, $marker);
        $response = new ListSubscriptionResponse();
        return $this->client->sendRequest($request, $response);
    }
}