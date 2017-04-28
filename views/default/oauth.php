<?php
use xiaochengfu\alipay\assets\AlipayAsset;

/** @var $model MigrationUtility */
/** @var $output String */
/** @var $output_drop String */
/** @var $tables array */
/** @var ActiveForm $form */
AlipayAsset::register($this);
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title> 扫码授权 </title>
<script type="text/javascript">
  function message(){
      $("#code").qrcode({
          render: "table", //table方式
          width: 200, //宽度
          height:200, //高度
          text: "<?= $url?>" //任意内容
      });}
</script>
</head>
<body onload="message()">
<div id="code"></div>
</body>
</html>