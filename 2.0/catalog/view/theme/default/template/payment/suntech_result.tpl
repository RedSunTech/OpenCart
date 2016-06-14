<?php echo $header; ?>

<meta http-equiv="refresh" content="10;url=<?php echo $continue; ?>">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<div class="container">
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>

	    <div id="content" class="<?php echo $class; ?>">
	    
	 <h1><?php echo $heading_title; ?></h1>
     <p><?php echo $text_response; ?></p>
     
     <br><br>
     
     <p><?php echo $text_message; ?></p>
     <p><?php echo $text_message_wait; ?></p>
         
	    </div>


    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>