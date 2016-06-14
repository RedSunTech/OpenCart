<?php
  if ($_POST['SendType']=='1') {exit;}
  $formStr="";

  foreach ($_GET as $key => $value) {
    $formStr.= "<input type='hidden' name='$key' value='".urldecode($value)."'>\r\n";
  }
  if (isset($_GET['note2'])) {
    if ($_GET['note2']!='') {$url=explode(',',urldecode($_GET['note2']));}
  }

  foreach ($_POST as $key => $value) {
    $formStr.= "<input type='hidden' name='$key' value='".urldecode($value)."'>\r\n";
  }
  if (isset($_POST['note2'])) {
     if ($_POST['note2']!='') {$url=explode(',',urldecode($_POST['note2']));}
  }
?>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>
<body oncontextmenu="return false" onkeydown="if(event.keyCode==8 || event.keyCode==37 || event.keyCode==116) return false;">
<form action="index.php?route=<?php echo $url[1]; ?>" method="POST" name="form1">
<?php echo $formStr; ?>
</form>
<script Language="JavaScript">
<!--
  document.form1.submit();
//-->
</script>
</body>
</html>
