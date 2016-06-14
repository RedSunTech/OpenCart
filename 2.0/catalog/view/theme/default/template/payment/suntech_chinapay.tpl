<form class="form-horizontal" action="<?php echo $action; ?>" method="POST" id="payment">
  
  <input type="hidden" name="web" value="<?php echo $web; ?>" />
  <input type="hidden" name="MN" value="<?php echo $MN; ?>" />
  <input type="hidden" name="OrderInfo" value="<?php echo $OrderInfo; ?>" />
  <input type="hidden" name="Td" value="<?php echo $Td; ?>" />
  <input type="hidden" name="sna" value="<?php echo $sna; ?>" />
  <input type="hidden" name="sdt" value="<?php echo $sdt; ?>" />
  <input type="hidden" name="email" value="<?php echo $email; ?>" />
  <input type="hidden" name="note1" value="<?php echo $note1; ?>" />
  <input type="hidden" name="note2" value="<?php echo $note2; ?>" />
  <input type="hidden" name="ChkValue" value="<?php echo $ChkValue; ?>" />
  <input type="hidden" name="Card_Type" value="1" /> <!--chinpay-->

  <fieldset>
    <div class="buttons">
      <div class="pull-right">
        <input type="button" value="<?php echo $button_confirm; ?>" class="btn btn-primary" onclick="saveOrder();"/>
      </div>
    </div>
  </fieldset>
</form>

<script>
	// 訂單送出前 , 清空購物車 , 建立訂單
	function saveOrder(){
		$.post( "index.php?route=payment/suntech_chinapay/saveOrder", function( data ) {
  			document.getElementById('payment').submit();
		});
	}
</script>