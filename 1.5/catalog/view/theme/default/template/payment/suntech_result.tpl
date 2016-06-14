<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $language; ?>" xml:lang="<?php echo $language; ?>">
<head>
<meta http-equiv="refresh" content="10;url=<?php echo $continue; ?>">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $title; ?></title>
</head>
<body>
<div style="text-align: center;">
  <h1><?php echo $heading_title; ?></h1>
  <p><?php echo $text_response; ?></p>
  <div style="border: 1px solid #DDDDDD; margin-bottom: 20px; width: 350px; margin-left: auto; margin-right: auto;">
    <WPDISPLAY ITEM=banner>
  </div>
  <p><?php echo $text_message; ?></p>
  <p><?php echo $text_message_wait; ?></p>
</div>
</body>
</html>