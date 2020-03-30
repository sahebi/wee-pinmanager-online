<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">

        <button type="submit" form="pin-form" name="delete-pin" id="delete-pin" class="btn btn-danger" data-toggle="tooltip" title="Delete Selected Items"><i class="fa fa-trash"></i></button>

        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal" data-toggle="tooltip" title="Add pin" class="btn btn-warning"><i class="fa fa-plus"></i></button>
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalImportFile" data-toggle="tooltip" title="Load File" class="btn btn-warning"><i class="fa fa-file"></i></button>
        <!--<button type="submit" form="form-wee" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>-->

        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="Back" class="btn btn-default"><i class="fa fa-reply"></i></a></div>

      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>

  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <!-- MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW -->
    <div id="myModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">New PIN</h4>
          </div>
          <div class="modal-body">
              <div class="form-group">
                <form action="" method="POST" id="add-pins">
                    <input type="hidden" name="add-pin" value="add-pin">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-secret">Serial</label>
                        <div class="col-sm-10">
                            <input type="text" name="wee_serial" value="" placeholder="Enter serial number" id="input-secret" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-secret">Pin Code</label>
                        <div class="col-sm-10">
                            <input type="text" name="wee_col1" value="" placeholder="Pin code" id="input-secret" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-secret">Password</label>
                        <div class="col-sm-10">
                            <input type="text" name="wee_col2" value="" placeholder="Password" id="input-secret" class="form-control" />
                        </div>
                    </div>
                </form>
              </div>
          </div>
          <div class="modal-footer">
            <button type="submit" form="add-pins" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>

    <!-- MWMWMWMWMWM Upload CSV Files WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW -->
    <div id="modalImportFile" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><strong>Upload files</strong></h4>
          </div>
          <div class="modal-body">
                <form action="" method="POST" id="upload-pins" enctype="multipart/form-data">
                    <div class="form-group">
                    <input type="hidden" name="upload-pin" value="upload-pin">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-secret">File (*.CSV)</label>
                        <div class="col-sm-10">
                            Sample: <kbd> Serial, PinCode, Password</kbd>
                            <input type="file" name="wee_serial" value="" placeholder="Enter serial number" id="input-secret" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-secret">Status</label>
                        <div class="col-sm-10">
                            <select name="status">
                                <option value="READY">Ready</option>
                                <option value="SELLED">Selled</option>
                                <option value="DISABLE">Disabled</option>
                            </select>
                        </div>
                    </div>
                    </div>
                </form>
          </div>
          <div class="modal-footer">
            <button type="submit" form="upload-pins" class="btn btn-primary">Upload</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>
    <!-- MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMW -->
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
          <div class="panel panel-default">
            <div class="panel-heading">
              <strong>Filters</strong>
            </div>
            <div class="panel-body">
              <div class="col-md-4">
                  <a href="<?php echo $link_all; ?>" class="btn btn-primary">ALL</a>
                  <a href="<?php echo $link_ready; ?>" class="btn btn-primary">Ready</a>
                  <a href="<?php echo $link_selled; ?>" class="btn btn-primary">Selled</a>
                  <a href="<?php echo $link_disable; ?>" class="btn btn-primary">Disable</a>
              </div>
              <div class="col-md-4">
                <form method="POST" action="<?php echo $link_all; ?>" class="form-inline">
                  <input type="hidden" name="form-order-id" value="form-order-id">
                  <div class="form-group">
                    <label for="order_id">Order ID</label>
                    <input type="text" class="form-control" id="order_id" name="order_id" placeholder="Enter order id">
                  </div>
                  <button type="submit" class="btn btn-default">Find</button>
                </form>
              </div>

              <div class="col-md-4">
                <form method="POST" action="<?php echo $link_all; ?>" class="form-inline">
                  <input type="hidden" name="form-pin" value="form-pin">
                  <div class="form-group">
                    <label for="pin_id">Pin ID</label>
                    <input type="text" class="form-control" id="pin_id" name="pin_id" placeholder="Enter pin id">
                  </div>
                  <button type="submit" class="btn btn-default">Find</button>
                </form>
              </div>
            </div>
          </div>

          <div class="panel panel-primary">
            <div class="panel-heading">
              <strong>Product</strong>
            </div>
            <div class="panel-body">
              <table class="table">
                <tr>
                  <td rowspan="4"><img src="<?php echo $products[0]['image']; ?>" alt="<?php echo $products[0]['name']; ?> <?php echo $products[0]['model']; ?> "></td>
                </tr>
                <tr>
                  <td><strong>Model:</strong></td>
                  <td><?php echo $products[0]['model']; ?></td>
                  <td><strong>Name:</strong></td>
                  <td><?php echo $products[0]['name']; ?></td>
                  <td><strong>Price:</strong></td>
                  <td><?php echo $products[0]['price']; ?></td>
                </tr>
              </table>              
            </div>
          </div>

          <form method="POST" action="" id="pin-form">
            <input type="hidden" value="pin-form" name="pin-form">
          <table class="table table-bordered table-hover">
            <thead>
              <tr class="tetx-center">
                <td></td>
                <td>PIN</td>
                <td>Serial</td>
                <td>Code</td>
                <td>Pass</td>
                <td>Status</td>
                <td>Edit/Delete</td>
              </tr>
            </thead>
            <tbody>
                <?php foreach($pins as $key => $val){ ?>
                    <tr class="tetx-center">
                        <td><input name="select_item[]" type="checkbox" value="<?php echo $val['id']; ?>"></td>
                        <td>PIN-<?php echo $val['id']; ?></td>
                        <td><?php echo $val['serial']; ?></td>
                        <td><?php echo $val['col1']; ?></td>
                        <td><?php echo $val['col2']; ?></td>
                        <td><?php echo $val['status']; ?></td>
                        <td>
                            <a data-id="<?= $val['id'] ?>" data-serial="<?= $val['serial'] ?>" data-col1="<?= $val['col1'] ?>" data-col2="<?= $val['col2'] ?>" data-status="<?= $val['status'] ?>" data-smsresult="<?= $val['sms_result'] ?>" data-sell-dt="<?= $val['sell_dt'] ?>" class="btn btn-primary btn-edit" data-toggle="modal" data-target="#modalEdit" data-toggle="tooltip" title="Edit Record">Edit</a>
                            <a class="btn btn-danger" href="<?php echo $val['delete']; ?>">Delete</a>
                        </td
                    </tr>                
                <?php } ?>
            </tbody>
          </table>
          </form>
      </div>
    </div>
  </div>
</div>

<!-- MWMWMWMWMWM Edit Popup WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWW -->
<div id="modalEdit" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><strong>Edit</strong></h4>
      </div>

      <div class="modal-body">
            <form action="" method="POST" id="edit-pins" enctype="multipart/form-data">
                <div class="form-group">
                <input type="hidden" name="pin-id" id="pin-id" value="">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="serial">Serial</label>
                    <div class="col-sm-10">
                        <input type="text" name="serial" value="" placeholder="Enter serial number" id="serial" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="col1">Code</label>
                    <div class="col-sm-10">
                        <input type="text" name="col1" value="" placeholder="Enter code number" id="col1" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="col2">Password</label>
                    <div class="col-sm-10">
                        <input type="text" name="col2" value="" placeholder="Enter password" id="col2" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-secret">Status</label>
                    <div class="col-sm-10">
                        <select name="status" id="status" class="form-control">
                            <option value="READY">Ready</option>
                            <option value="SELLED">Selled</option>
                            <option value="DISABLE">Disabled</option>
                        </select>
                    </div>
                </div>
                <div id="selled-sec">
                  <div class="form-group">
                      <label class="col-sm-2 control-label" for="sms-result">SMS Result</label>
                      <div class="col-sm-10">
                          <input type="text" name="sms-result" value="" placeholder="SMS result coded" id="sms-result" class="form-control" />
                      </div>
                  </div>
                  <div class="form-group">
                      <label class="col-sm-2 control-label" for="sell-dt">Sell DT</label>
                      <div class="col-sm-10">
                          <input type="text" value="" id="sell-dt" class="form-control" />
                      </div>
                  </div>
                </div>
                </div>
            </form>
      </div>
      <div class="modal-footer">
        <button type="submit" form="edit-pins" class="btn btn-primary">Update</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<script>
  $(document).ready(function(){
    $('.btn-edit').click(function(){
      $('#pin-id').val($(this).data('id'));

      $('#serial').val($(this).data('serial'));
      $('#col1').val($(this).data('col1'));
      $('#col2').val($(this).data('col2'));


      var selected = '#status option[value='+$(this).data('status')+']';
      $(selected).attr('selected','selected');

      $('#sms-result').val( $(this).data('smsresult') );
      $('#sell-dt').val( $(this).data('sell-dt') );

      if($(this).data('status')=='READY'){
        $('#selled-sec').hide();
      }else
      if($(this).data('status')=='SELLED'){
        $('#selled-sec').show();
      }else
      if($(this).data('status')=='DISABLED'){
        $('#selled-sec').hide();
      }
    });
  });
</script>
<?php echo $footer; ?>