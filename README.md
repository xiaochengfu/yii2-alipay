# yii2-alipay
支付宝的手机网站支付、扫码即时到账付款、Oauth授权、单笔转账整合
Usage

Once the extension is installed, simply use it in your code by :

网页端使用:

1.在 config/main.php 添加如下配置
```
'modules' => [
        'alipay' => [
            'class' => 'xiaochengfu\alipay\Module',
        ]
    ],
```
在config/params.php添加
```
return [
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
];
```