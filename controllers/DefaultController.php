<?php
/**
 * Created by PhpStorm.
 * Author: houpeng
 * DateTime: 2017/04/15 11:30
 * Description:
 */
namespace xiaochengfu\alipay\controllers;

use xiaochengfu\alipay\models\Instant\AlipayNotify;
use xiaochengfu\alipay\models\Instant\AlipaySubmit;
use xiaochengfu\alipay\models\Oauth\AlipayAop;
use Yii;
use yii\web\Controller;
use xiaochengfu\alipay\models\AliPayUtility;

class DefaultController extends Controller
{

    /**
     * author:hp
     * user:hp
     * @return \common\lib\AliPay\Instant\提交表单HTML文本
     * PC端扫码支付、即时到账、手机网站支付，配置参数不同，调取的接口不同
     * 'service' => "create_direct_pay_by_user",//即时到账
     * 'service' => "alipay.wap.create.direct.pay.by.user",//手机网站支付
     */
    public function actionIndex()
    {
        /**************************请求参数**************************/
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = AliPayUtility::createOrderSn();

        //订单名称，必填
        $subject = '小程府整合demo';

        //付款金额，必填
        $total_fee = 0.01;

        //商品描述，可空
        $body = '绑定支付宝账号打款验证';

        /************************************************************/

        //构造要请求的参数数组，无需改动
        $aliPay_config = Yii::$app->params['aliPay_rsa_config'];
        $parameter = array(
            "service" => $aliPay_config['service'],
            "partner" => $aliPay_config['partner'],
            "seller_id" => $aliPay_config['seller_id'],
            "payment_type" => $aliPay_config['payment_type'],
            "notify_url" => $aliPay_config['notify_url'],
            "return_url" => $aliPay_config['return_url'],

            "anti_phishing_key" => $aliPay_config['anti_phishing_key'],
            "exter_invoke_ip" => $aliPay_config['exter_invoke_ip'],
            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "total_fee" => $total_fee,
            "body" => $body,
            "_input_charset" => trim(strtolower($aliPay_config['input_charset'])),
//            "extra_common_param" => '',   //这是扩展字段
            "app_pay" =>'Y'     //是否调起支付宝客户端
            // "qr_pay_mode" => 4
            //其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
            //如"参数名"=>"参数值"
        );

        //建立请求
        $aliPaySubmit = new AlipaySubmit($aliPay_config);
        $html_text = $aliPaySubmit->buildRequestForm($parameter, "get", "确认");
        return $html_text;
    }

    /**
     * @return mixed
     * 引导扫码授权页
     */
    public function actionOauth(){
        $appId = Yii::$app->params['aliPayOauthConfig']['appId'];
        $callback = Yii::$app->params['aliPayOauthConfig']['callBack'];
        $url = "https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=$appId&scope=auth_user&redirect_uri=$callback";
        return $this->render('oauth',['url'=>$url]);
    }

    /**
     * author：hp
     * user：hp
     * @throws Exception
     * 支付宝授权验证回调，获取依次auth_code，user_id
     */
    public function actionAliCallback(){
        $oauth = Yii::$app->request->get();
        $Aop = new AlipayAop();
        $msg = $Aop->AlipaySystemOauthTokenRequest($oauth);
        if($msg['status'] == 1){
            $userInfo = $Aop->AlipayUserInfoShareRequest($msg['message']['access_token']);
            var_dump($userInfo);
        }
    }

    /**
     * @return array
     * 支付宝单笔转账
     */
    public function actionAliPayTs(){
        $cashInfo = [
            'payee_account'=>'你看支付宝userid',//卡号
            'payee_real_name'=>'真实姓名',//姓名
            'amount'=>0.1
        ];
        $Aop = new AlipayAop();
        $result = $Aop->AlipayFundTransToaccountTransfer(AliPayUtility::createOrderSn(),$cashInfo['payee_account'],$cashInfo['amount'],$cashInfo['payee_real_name']);
        var_dump($result);
    }


    /**
     * author:hp
     * user:hp
     * 同步绑定验证页
     */
    public function actionCallback()
    {
        $aliPay_config = Yii::$app->params['aliPay_rsa_config'];
        ///////////////////////////////////////////////////////////////////////////
        $aliPayNotify = new AlipayNotify($aliPay_config);
        $verify_result = $aliPayNotify->verifyReturn();
        if ($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代码

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

            //商户订单号

            $out_trade_no = $_GET['out_trade_no'];

            //支付宝交易号

            $trade_no = $_GET['trade_no'];

            //交易状态
            $trade_status = $_GET['trade_status'];


            if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
            } else {
                echo "trade_status=" . $_GET['trade_status'];
            }
            echo '支付宝打款成功！';

//            echo "验证成功<br />";

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////s/
        } else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数
            echo "验证失败";
        }
    }

    /**
     * author：hp
     * user：hp
     * @throws ErrorException
     * 支付宝即时到账异步回调
     */
    public function actionNotifyAliPay(){
        $aliPay_config = Yii::$app->params['aliPay_rsa_config'];
        /////////////////////////////////////////////////////////////////////////
        $aliPayNotify = new AlipayNotify($aliPay_config);
        $verify_result = $aliPayNotify->verifyNotify();
        if($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代


            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //商户订单号

            $out_trade_no = $_POST['out_trade_no'];

            //支付宝交易号

            $trade_no = $_POST['trade_no'];

            //交易状态
            $trade_status = $_POST['trade_status'];


            if($_POST['trade_status'] == 'TRADE_FINISHED') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {


               //这里写自己的业务逻辑，如入库记录


            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "success";		//请不要修改或删除

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            //验证失败
            echo "fail";

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }

    
}
