# yii2-alipay
支付宝的手机网站支付、扫码即时到账付款、Oauth授权、单笔转账整合，保证能够正常使用
Usage

Once the extension is installed, simply use it in your code by :

安装：
```
composer require xiaochengfu/yii2-alipay "v1.0.2"
```

网页端使用:

1.在 config/main.php 添加如下配置
```
'modules' => [
        'alipay' => [
            'class' => 'xiaochengfu\alipay\Module',
        ]
    ],
```
手机网站支付与单笔转账配置不同，所以在config/params.php添加
```
return [
    //手机网站支付配置
    'aliPay_rsa_config' => [
        'partner' => '你的商户id',
        'seller_id' => '你的商户id',
        'private_key' => '私钥',
        'alipay_public_key' => '公钥',
        'sign_type' => strtoupper('RSA'),
        'notify_url' => "xxx/notify-ali-pay",
        'input_charset' => strtolower('utf-8'),
        'cacert' => getcwd() . '\\cacert.pem',
        'transport' => 'http',
        'payment_type' => "1",//支付类型
        'service' => "create_direct_pay_by_user",  //即时到账（扫码）
//        'service'       => "alipay.wap.create.direct.pay.by.user",//手机网站支付
        'return_url' => "http://xxx/callback",
        'anti_phishing_key' => "",
        'exter_invoke_ip' => "",
    ],
    //单笔转账配置，appid要在支付宝开放平台添加应用，私钥、公钥、回调地址均在开发平台设置
    'aliPayOauthConfig' =>[
        'appId'=>'xxxx',
        'callBack'=>'http://xxx/alipay/default/ali-callback',//扫码授权回调地址
        'rsaPrivateKey' =>'你的私钥',
        'alipayrsaPublicKey' => '你的公钥'
    ]
];
```
2.进入链接
扫码即时到账、手机网站支付进入方式：
```
http://域名/alipay
```
扫码授权进入方式：
```
http://域名/alipay/defautl/oauth
```
单笔转账进入方式：
```
http://域名/alipay/default/ali-pay-ts
```
3.如果想将业务代码移到backend项目下，可修改`vendor/xiaochengfu/yii2-alipay/Module.php`下的命名空间，要在backend/controllers下创建名为ZfbControllers的控制器

```
namespace xiaochengfu\alipay;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'backend\controllers';
    public $defaultRoute = 'zfb';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
```

此时以下两种进入方式均可：
```
http://域名/alipay
http://域名/zfb
```
4.如有疑问，可在这里进行交流：
https://github.com/xiaochengfu/yii2-alipay/issues/1
可联系qq：1033426413，验证回答：支付宝接入，我会到交流区查看的
