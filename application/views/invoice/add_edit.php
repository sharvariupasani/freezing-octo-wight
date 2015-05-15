<section class="content-header">
    <h1>
        Invoice
        <small> <?=($this->router->fetch_method() == 'add')?'Add':'Edit'?></small>
    </h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<?php
				if (@$flash_msg != "") {
			?>
				<div id="flash_msg"><?=$flash_msg?></div>
			<?php
				}
			?>

			<?php
				if (@$error_msg != "") {
			?>
				<div id="error_msg" class='alert alert-warning alert-dismissable'>
						<button class="close" aria-hidden="true" data-dismiss="alert" type="button">�</button>
						<h4><i class="icon fa fa-warning"></i>Alert!</h4>
						<?=$error_msg?>
				</div>
			<?php
				}
			?>
		</div>
		<div class='col-md-12'>
    	<div class="col-md-8">
			<form role="form" action="" method="post" id='takein_form' name='product_form' enctype="multipart/form-data">
				<div class='box box-solid'>
				<div class='box-header'>
						<h3 class='box-title'>Customer</h3>
				</div>
				<div class="box-body">
					<div class="form-group">
                        <label>Customer:</label>
                        <input type="text" placeholder="Search using mobile" class="form-control validate[required]" name="customer" id="customer" value="<?=@$customer->customer?>" >
                        <input type="hidden" name="cust_id" id="cust_id" value="<?=@$customer->c_id?>" >
                    </div>
            </div>
			</div>

			<div class='box' type="product">
						<div class='box-header'>
							<h3 class='box-title'>Add Product</h3>
						</div>
						<div class='box-body'>
							<div class='product_div'>
								<div class="row">
									<div class="col-xs-7 form-group">
										<label>Product:</label>
										<input type="text" placeholder="Search product" id="p_name" class="product form-control" value="" >
									</div>

									<div class="col-xs-2 form-group">
										<label>Qty:</label>
										<input type="text" placeholder="Enter ..." id="p_qty" class="form-control"  value="" >
									</div>

									<div class="col-xs-2 form-group">
										<label>Price:</label>
										<input type="text" placeholder="Enter ..." id="p_price" class="form-control" value="" >
									</div>

									<div class="col-xs-1 form-group">
										<label>&nbsp</label>
										<span class="form-control form-del"><a class="fa fa-trash-o removeproduct"href="#"></a></span>
									</div>
								</div>
							</div>
						</div>
						<div class='box-footer clearfix'>
							<button class="btn btn-default pull-right addproduct"><i class="fa fa-plus"></i>Add Another Product</button>
						</div>
					</div>
					<div class='box' type="service">
						<div class='box-header'>
							<h3 class='box-title'>Add Service</h3>
						</div>
						<div class='box-body'>
							<div class='service_div'>
								<div class="row">
									<div class="col-xs-9 form-group">
										<label>Service:</label>
										<input type="text" placeholder="Service detail" id="s_name" class="product form-control" value="" >
									</div>

									<div class="col-xs-2 form-group">
										<label>Price:</label>
										<input type="text" placeholder="Rate" id="s_price" class="form-control" value="" >
									</div>

									<div class="col-xs-1 form-group">
										<label>&nbsp</label>
										<span class="form-control form-del"><a class="fa fa-trash-o removeservice"href="#"></a></span>
									</div>
								</div>
							</div>
						</div>
						<div class='box-footer clearfix'>
							<button class="btn btn-default pull-right addproduct"><i class="fa fa-plus"></i>Add Another Service</button>
						</div>
					</div>
			</form>
    	</div>

		<div class="col-md-4">
				<div class='box box-solid'>
					<div class='box-header'>
						<h3 class='box-title'>Summery</h3>
					</div>
					<div class="box-body">
						<div class="form-group">
							<label>Total:</label>
							<input type="text" placeholder="" class="form-control validate[required]" name="total" id="total" value="<?=@$invoice[0]->total?>" >
						</div>
					</div>
					<div class='box-footer'>
						<div class="form-group">
							<button class="btn btn-primary btn-flat" type="submit" id="submit">Submit</button>
							<button class="btn btn-primary btn-flat" type="submit" id="submit">Print</button>
						</div>
					</div>
				</div>
			</div>
			</div>
    </div>
</section>