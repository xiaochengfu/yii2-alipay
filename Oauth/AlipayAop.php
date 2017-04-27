<?php
namespace xiaochengfu\alipay\Oauth;
require_once 'AopClient.php';
require_once 'AlipayFundTransToaccountTransferRequest.php';
require_once 'AlipaySystemOauthTokenRequest.php';
require_once 'AlipayUserInfoShareRequest.php';

class AlipayAop
{
    private $appId;
    private $rsaPrivateKey;
    private $alipayrsaPublicKey;

    function __construct()
    {
        $this->appId = \Yii::$app->params['aliPayOauthConfig']['appId'];
        $this->rsaPrivateKey = \Yii::$app->params['aliPayOauthConfig']['rsaPrivateKey'];
        $this->alipayrsaPublicKey = \Yii::$app->params['aliPayOauthConfig']['alipayrsaPublicKey'];
    }

    /**
     * author：hp
     * user：hp
     *该方法为支付宝转账方法
     *osn 交易单号
     *payee_account 收款人帐号
     *amount 转账金额
     *payee_real_name 收款方真实姓名
     */
    public function AlipayFundTransToaccountTransfer($osn, $payee_account, $amount, $payee_real_name)
    {
        $msg = array();
        if (!$osn) {
            $msg['success'] = 2;
            $msg['text'] = '交易单号为空';
            return $msg;
        } elseif (!$payee_account) {
            $msg['success'] = 2;
            $msg['text'] = '收款人帐号为空';
            return $msg;
        } elseif (!$amount || $amount < 0.1) {
            $msg['success'] = 2;
            $msg['text'] = '转账金额不能小于0.1';
            return $msg;
        } elseif (!$payee_real_name) {
            $msg['success'] = 2;
            $msg['text'] = '收款人姓名为空';
            return $msg;
        } else {
            $aop = new \AopClient();
            $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
            $aop->appId = $this->appId;
            $aop->rsaPrivateKey = $this->rsaPrivateKey;
            $aop->alipayrsaPublicKey = $this->alipayrsaPublicKey;
            $aop->apiVersion = '1.0';
            $aop->signType = 'RSA2';
            $aop->postCharset = 'UTF-8';
            $aop->format = 'json';
            $request = new \AlipayFundTransToaccountTransferRequest();
            $request->setBizContent("{" .
                "    \"out_biz_no\":\"" . $osn . "\"," .
                "    \"payee_type\":\"ALIPAY_LOGONID\"," .
                "    \"payee_account\":\"" . $payee_account . "\"," .
                "    \"amount\":\"" . $amount . "\"," .
                // "    \"payer_real_name\":\"fpypvx4005@sandbox.com\"," .
                "    \"payer_show_name\":\"fpypvx4005@sandbox.com\"," .
                "    \"payee_real_name\":\"" . $payee_real_name . "\"," .
                "    \"remark\":\"转账备注\"," .
                "    \"ext_param\":\"{\\\"order_title\\\":\\\"用户提现\\\"}\"" .
                "  }");
            $result = $aop->execute($request);
            $resultCode = $result->alipay_fund_trans_toaccount_transfer_response->code;
            if (!empty($resultCode) && $resultCode == 10000) {
                $msg['success'] = 1;
                $msg['text'] = '支付宝转账成功';
                return $msg;
            } else {
                $resultMsg = $result->alipay_fund_trans_toaccount_transfer_response->sub_msg;
                $msg['success'] = 2;
                $msg['text'] = $resultMsg;
                return $msg;
            }
        }
    }

    /**
     * author：hp
     * user：hp
     * @param $oauthDate
     * @return mixed
     * @throws \Exception
     * 支付宝获取token方法
     */
    public function AlipaySystemOauthTokenRequest($oauthDate){
        $aop = new \AopClient();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $this->appId;
        $aop->rsaPrivateKey = $this->rsaPrivateKey;
        $aop->alipayrsaPublicKey = $this->alipayrsaPublicKey;
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new \AlipaySystemOauthTokenRequest ();
        $request->setGrantType("authorization_code");
        $request->setCode($oauthDate['auth_code']);
        $result = $aop->execute ( $request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        if(empty($result->$responseNode->code)){
            $msg['status'] = 1;
            $msg['message'] = (array)$result->$responseNode;
        } else {
            $msg['status'] = 2;
            $msg['message'] = $result->$responseNode->sub_msg;
        }
        return $msg;
    }

    /**
     * author：hp
     * user：hp
     * @param $accessToken
     * @return mixed
     * @throws \Exception
     * 支付宝获取用户公共信息，如头像，昵称等
     */
    public function AlipayUserInfoShareRequest($accessToken){
        $aop = new \AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $this->appId;
        $aop->rsaPrivateKey = $this->rsaPrivateKey;
        $aop->alipayrsaPublicKey = $this->alipayrsaPublicKey;
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new \AlipayUserInfoShareRequest();
        $result = $aop->execute ( $request , $accessToken );
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            $msg['status'] = 1;
            $msg['message'] = (array)$result->$responseNode;
        } else {
            $msg['status'] = 2;
            $msg['message'] = $result->$responseNode->sub_msg;
        }
        return $msg;
    }
}

?>