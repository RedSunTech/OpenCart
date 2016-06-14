<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
  
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form_suntech_chinapay" class="btn btn-primary">
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

       <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form_suntech_chinapay" class="form-horizontal">

          <div class="form-group required">
            <label class="col-sm-2 control-label" for="suntech_chinapay_account">
              <?php echo $entry_account; ?>
            </label>
            <div class="col-sm-3">
              <input type="text" name="suntech_chinapay_account" value="<?php echo $suntech_chinapay_account; ?>" id="suntech_chinapay_account" class="form-control" />
            </div>
            <div><?php echo $entry_account_note; ?></div>
            <?php if ($error_account) { ?>
                  <div class="text-danger"><?php echo $error_account; ?></div>
            <?php } ?>  
          </div>

          <div class="form-group required" for="suntech_chinapay_password">
            <label class="col-sm-2 control-label">
              <?php echo $entry_password; ?>
            </label>
            <div class="col-sm-3">
              <input type="text" name="suntech_chinapay_password" value="<?php echo $suntech_chinapay_password; ?>" id="suntech_chinapay_password" class="form-control" />
            </div>
            <div><?php echo $entry_account_note; ?></div>
            <?php if ($error_password) { ?>
                  <div class="text-danger"><?php echo $error_password; ?></div>
            <?php } ?> 
          </div>

         <!--     <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_success; ?></label>
            <div class="col-sm-10">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-link"></i></span>
                <input type="text" readonly="" value="<?php echo $success; ?>" class="form-control">
              </div>
              <div><?php echo $entry_success_note; ?></div>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_error; ?></label>
            <div class="col-sm-10">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-link"></i></span>
                <input type="text" readonly="" value="<?php echo $error; ?>" class="form-control">
              </div>
              <div><?php echo $entry_error_note; ?></div>
            </div>
          </div> -->

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
              <select name="suntech_chinapay_order_status_id" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $suntech_chinapay_order_status_id) { ?>
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
                <select name="suntech_chinapay_geo_zone_id" class="form-control">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $suntech_chinapay_geo_zone_id) { ?>
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
                <select name="suntech_chinapay_status" class="form-control">
                <?php if ($suntech_chinapay_status) { ?>
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
              <input type="text" class="form-control" name="suntech_chinapay_sort_order" value="<?php echo $suntech_chinapay_sort_order; ?>" placeholder="Sort Order" />
            </div>
          </div>


     </form>
      </div>
    </div>
  </div>


  
</div>
<?php echo $footer; ?>