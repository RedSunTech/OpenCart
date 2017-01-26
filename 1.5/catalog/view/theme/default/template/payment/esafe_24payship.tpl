
<?php echo $shtml; ?>
<div class="checkout-heading" style="background: #F7F7F7; border: 1px solid #DDDDDD; margin-bottom: 0px;"><?php echo $text_payment; ?></div>
<div class="buttons" style="border: 1px solid #DDDDDD; border-top: 0px; padding: 10px; margin-bottom: 0px; <?php echo ($esafe_24payship_description == '') ? ' display:none;' : '';?>">
    <?php echo ($esafe_24payship_description == "") ? "" : $text_instruction . "<br /><font color='#FF9900'>" . $esafe_24payship_description . "</font><br />"; ?>
</div>
<div class="buttons">
    <div class="right"><a  id="button-confirm" class="button" onclick="checkTotal()"><span><?php echo $button_confirm; ?></span></a></div>
</div>
<script type="text/javascript">
//<![CDATA[
    function checkTotal() {
        $.ajax({
            type: 'GET',
            url: '<?php echo $continue; ?>',
            success: function () {
                document.forms['myform'].submit();
            }
        });
    }
//]]>
</script>
