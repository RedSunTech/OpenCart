<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">

  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form_suntech_24payment" class="btn btn-primary">
          <i class="fa fa-save"></i>
        </button>
        <a href="<?php echo $cancel; ?>" class="btn btn-default">
          <i class="fa fa-reply"></i>
        </a>
      </div>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li>
          <a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        </li>
        <?php } ?>
      </ul>
    </div>
  </div>



  <div class="container-fluid">
    
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger">
      <i class="fa fa-exclamation-circle"></i>
      &nbsp;<?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>

    <div class="panel panel-default">

      <div class="panel-heading">
        <h3 class="panel-title">
          <i class="fa fa-pencil"></i>
          &nbsp;<?php echo $heading_title; ?>
        </h3>
      </div>

      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form_suntech_24payment" class="form-horizontal">
          
        
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="suntech_24payment_account">
              <?php echo $entry_account; ?>
            </label>
            <div class="col-sm-3">
              <input type="text" name="suntech_24payment_account" value="<?php echo $suntech_24payment_account; ?>" id="suntech_24payment_account" class="form-control" />
            </div>
            <div><?php echo $entry_account_note; ?></div>
            <?php if ($error_account) { ?>
                  <div class="text-danger"><?php echo $error_account; ?></div>
            <?php } ?>  
          </div>

          <div class="form-group required" for="suntech_24payment_password">
            <label class="col-sm-2 control-label">
              <?php echo $entry_password; ?>
            </label>
            <div class="col-sm-3">
              <input type="text" name="suntech_24payment_password" value="<?php echo $suntech_24payment_password; ?>" id="suntech_24payment_password" class="form-control" />
            </div>
            <div><?php echo $entry_account_note; ?></div>
            <?php if ($error_password) { ?>
                  <div class="text-danger"><?php echo $error_password; ?></div>
            <?php } ?> 
          </div>

          <div class="form-group required">
            <label class="col-sm-2 control-label" for="entry_duedays">
              <?php echo $entry_duedays; ?>
            </label>
            <div class="col-sm-3">
              <input type="text" name="suntech_24payment_duedays" value="<?php echo $suntech_24payment_duedays; ?>" id="suntech_24payment_duedays" class="form-control" />
              <div><?php echo $entry_duedays_note; ?></div>
            </div>
            <?php if ($error_duedays) { ?>
            <div class="text-danger"><?php echo $error_duedays; ?></div>
            <?php } ?>
          </div>

          <div class="form-group required" for="suntech_24payment_product_name">
            <label class="col-sm-2 control-label">
              <?php echo $entry_product_name; ?>
            </label>
            <div class="col-sm-5">
              <input type="text" name="suntech_24payment_product_name" value="<?php echo $suntech_24payment_product_name; ?>" id="suntech_24payment_product_name" class="form-control" />
              <div><?php echo $entry_product_name_note; ?></div>
            </div>
            <?php if ($error_product_name) { ?>
                  <div class="text-danger"><?php echo $error_product_name; ?></div>
            <?php } ?> 
          </div>

		     <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_paid; ?></label>
            <div class="col-sm-10">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-link"></i></span>
                <input type="text" readonly="" value="<?php echo $paid; ?>" class="form-control">
              </div>
              <div><?php echo $entry_paid_note; ?></div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_callback; ?></label>
            <div class="col-sm-10">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-link"></i></span>
                <input type="text" readonly="" value="<?php echo $callback; ?>" class="form-control">
              </div>
              <div><?php echo $entry_callback_note; ?></div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_confirm; ?></label>
            <div class="col-sm-10">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-link"></i></span>
                <input type="text" readonly="" value="<?php echo $confirm; ?>" class="form-control">
              </div>
              <div><?php echo $entry_confirm_note; ?></div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label">
              <?php echo $entry_order_status; ?>
            </label>
            <div class="col-sm-3">
              <select name="suntech_24payment_order_status_id" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $suntech_24payment_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
         
          <div class="form-group">
            <label class="col-sm-2 control-label">
              <?php echo $entry_geo_zone; ?>
            </label>
            <div class="col-sm-3">
                <select name="suntech_24payment_geo_zone_id" class="form-control">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $suntech_24payment_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label">
              <?php echo $entry_status; ?>
            </label>
            <div class="col-sm-3">
                <select name="suntech_24payment_status" class="form-control">
                <?php if ($suntech_24payment_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
                </select>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label">
              <?php echo $entry_sort_order; ?>
            </label>
            <div class="col-sm-3">
              <input type="text" class="form-control" name="suntech_24payment_sort_order" value="<?php echo $suntech_24payment_sort_order; ?>" placeholder="Sort Order"  />
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>


  
</div>
<?php echo $footer; ?>