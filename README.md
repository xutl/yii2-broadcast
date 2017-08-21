# yii2-broadcast


[![Latest Stable Version](https://poser.pugx.org/xutl/yii2-broadcast/v/stable.png)](https://packagist.org/packages/xutl/yii2-broadcast)
[![Total Downloads](https://poser.pugx.org/xutl/yii2-broadcast/downloads.png)](https://packagist.org/packages/xutl/yii2-broadcast)
[![Reference Status](https://www.versioneye.com/php/xutl:yii2-broadcast/reference_badge.svg)](https://www.versioneye.com/php/xutl:yii2-broadcast/references)
[![Build Status](https://img.shields.io/travis/xutl/yii2-broadcast.svg)](http://travis-ci.org/xutl/yii2-broadcast)
[![Dependency Status](https://www.versioneye.com/php/xutl:yii2-broadcast/dev-master/badge.png)](https://www.versioneye.com/php/xutl:yii2-broadcast/dev-master)
[![License](https://poser.pugx.org/xutl/yii2-broadcast/license.svg)](https://packagist.org/packages/xutl/yii2-broadcast)

## Installation

Next steps will guide you through the process of installing yii2-admin using [composer](http://getcomposer.org/download/). Installation is a quick and easy three-step process.

### Install component via composer

Either run

```
composer require --prefer-dist xutl/yii2-broadcast
```

or add

```json
"yuncms/yii2-broadcast": "~1.0.0"
```

to the `require` section of your composer.json.

### Configuring your application

Add following lines to your main configuration file:

```php
'components' => [
    'broadcast' => [
        'class' => 'xutl\broadcast\Broadcast',
        'endPoint' => 'http://abcdefg.mns.cn-hangzhou.aliyuncs.com/',
        'accessId' => '1234567890',
        'accessKey' => '1234567890',
    ],
    //etc
],
```

### Use broadcast

```php
/** @var \xutl\broadcast\Broadcast $broadcast */
$broadcast = Yii::$app->broadcast;

$topicName = 'CreateTopicAndPublishMessageExample';
$broadcast->create($topicName);

$topic = $broadcast->getTopicRef($topicName);
$subscriptionName = "SubscriptionExample";

try{
    $res = $topic->subscribe($subscriptionName,'https://www.baidu.com','test.test');
    echo "SubscriptionCreated! \n";
}catch (MnsException $e){
    echo "CreateSubscriptionFailed: " . $e;
    return;
}

$messageBody = "test";
$res = $topic->publishMessage($messageBody, 'test.test');
var_dump($res);

```

```php
class ApiController extends \yii\web\Controller
{
    public function actions()
    {
        return [
            /**
             * Returns an access token.
             */
            'callback' => [
                'class' => \xutl\broadcast\TopicAction::classname(),
                'callback'=>[$this, 'callback'],
            ],
        ];
    }
    
    /**
     * 
     */
    public function callback($params)
    {
        print_r($params);
    }
}
```

## License

This is released under the MIT License. See the bundled [LICENSE.md](LICENSE.md)
for details.