<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace xutl\broadcast;


use Yii;
use yii\console\Controller;
use AliyunMNS\Exception\MnsException;

/**
 * Class BroadcastController
 * @package xutl\broadcast
 */
class BroadcastController extends Controller
{
    /** @var  \xutl\broadcast\Broadcast */
    private $broadcast;

    /**
     * 初始化
     */
    public function init()
    {
        parent::init();
        $this->broadcast = Yii::$app->get('broadcast');
    }

    /**
     * Create Topic
     * @param null|string $topic
     */
    public function actionCreate($topic = null)
    {
        try {
            $this->broadcast->create(!is_null($topic) ? $topic : $this->broadcast->topicName);
            echo "TopicCreated! \n";
        } catch (MnsException $e) {
            echo "CreateTopicFailed: " . $e;
            return;
        }
    }

    /**
     * Delete Topic
     * @param null|string $topic
     */
    public function actionDelete($topic = null)
    {
        try {
            $this->broadcast->delete(!is_null($topic) ? $topic : $this->broadcast->topicName);
            echo "TopicDeleted! \n";
        } catch (MnsException $e) {
            echo "DeleteTopicFailed: " . $e;
            return;
        }
    }
}