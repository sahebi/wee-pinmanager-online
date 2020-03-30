<?php echo $header; ?>
<?php echo $column_left; ?>
<style>
    .rtl {
        direction: rtl;
    }
</style>

<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">

                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal" data-toggle="tooltip" title="Add product" class="btn btn-warning"><i class="fa fa-plus"></i></button>

                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="Back" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1>
                <?php echo $heading_title; ?>
            </h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li>
                    <a href="<?php echo $breadcrumb['href']; ?>">
                        <?php echo $breadcrumb['text']; ?>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>
            <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add new product</h4>
                    </div>
                    <div class="modal-body rtl">
                        <form action="" method="POST" id="add-product">
                            <input type="hidden" name="add-product" value="add-product">
                            <select name="product_id">
                  <?php foreach($products as $k => $v){ ?>
                    <option value="<?php echo $v['product_id'] ?>"><?php echo $v['model'] . ' -> ' . $v['name']; ?></option>
                  <?php } ?>
                </select>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" form="add-product" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i>
                    <?php echo $text_edit; ?>
                </h3>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="tetx-center">
                            <td></td>
                            <td>Model</td>
                            <td>Name</td>
                            <td>Price</td>
                            <td>Special</td>

                            <td>Disabled PIN</td>
                            <td>Ready PIN</td>
                            <td>Selled PIN</td>

                            <td>Status</td>
                            <td>Edit</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pins as $k => $v){ ?>
                        <tr>
                            <td><img src="<?php echo $v['image']; ?>"></td>
                            <td>
                                <?php echo $v['model']; ?>
                            </td>
                            <td>
                                <?php echo $v['name']; ?>
                            </td>
                            <td>
                                <?php echo $v['price']; ?>
                            </td>
                            <td>
                                <?php echo $v['special']; ?>
                            </td>

                            <td><span class="label label-default"><?php echo $v['pin_disabled']; ?></span></td>

                            <?php $class = 'label-success'; if($v['pin_ready']==0){ $class = 'label-default';} ?>
                            <td><span class="label <?php echo $class ?>"><?php echo $v['pin_ready']; ?></span></td>

                            <?php $class = 'label-info'; if($v['pin_selled']==0){ $class = 'label-default';} ?>
                            <td><span class="label <?php echo $class ?>"><?php echo $v['pin_selled']; ?></span></td>

                            <td>
                                <?php echo $v['status']; ?>
                            </td>
                            <td>
                                <a class="btn btn-primary" href="<?php echo $v['edit']; ?>">Pin Lists</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?php echo $footer; ?>