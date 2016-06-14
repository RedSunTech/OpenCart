<form action="<?php echo $action; ?>" method="post" id="payment">
  <input type="hidden" name="web" value="<?php echo $web; ?>" />
  <input type="hidden" name="MN" value="<?php echo $MN; ?>" />
  <input type="hidden" name="OrderInfo" value="<?php echo $OrderInfo; ?>" />
  <input type="hidden" name="Td" value="<?php echo $Td; ?>" />
  <input type="hidden" name="sna" value="<?php echo $sna; ?>" />
  <input type="hidden" name="sdt" value="<?php echo $sdt; ?>" />
  <input type="hidden" name="email" value="<?php echo $email; ?>" />
  <input type="hidden" name="note1" value="<?php echo $note1; ?>" />
  <input type="hidden" name="note2" value="<?php echo $note2; ?>" />
  <input type="hidden" name="DueDate" value="<?php echo $DueDate; ?>" />
  <input type="hidden" name="UserNo" value="<?php echo $UserNo; ?>" />
  <input type="hidden" name="BillDate" value="<?php echo $BillDate; ?>" />
  <input type="hidden" name="ChkValue" value="<?php echo $ChkValue; ?>" />
  <div class="buttons">
    <div class="right"><a onclick="$('#payment').submit();" class="button"><span><?php echo $button_confirm; ?></span></a></div>
  </div>
</form>
