<?php
/**
 * Created by PhpStorm.
 * Author: houpeng
 * DateTime: 2017/04/15 11:30
 * Description:
 */
namespace xiaochengfu\alipay\models;

use yii\base\Model;

class AliPayUtility extends Model
{
    /**
     * @return string
     * 创建订单号
     */
    public static function createOrderSn()
    {
        list($usec, $sec) = explode(" ", microtime());
        $usec = substr(str_replace('0.', '', $usec), 0, 4);
        $str = rand(10, 99);
        return date("YmdHis") . $usec . $str;
    }

}
