<?php
/**
 * Created by PhpStorm.
 * Author: houpeng
 * DateTime: 2017/04/15 11:30
 * Description:
 */
namespace xiaochengfu\alipay\assets;

class AlipayAsset extends  \yii\web\AssetBundle
{
    public $sourcePath = '@vendor/xiaochengfu/yii2-alipay/static';
    public $css = [

    ];
    public $js = [
        'jquery-1.8.0.min.js',
        'jquery.qrcode.min.js',
    ];
    public $depends = [
        '\backend\assets\AppAsset',
    ];
}