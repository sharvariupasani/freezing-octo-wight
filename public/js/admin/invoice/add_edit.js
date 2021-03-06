$(document).ready(function() {
	$("#invoice_form").validationEngine();

	$("input#customer").autocomplete({
			source: admin_path()+"customer/autocomplete",
			select: function( event, ui ) {
				$("#cust_id").val(ui.item.c_id);
				return true;
			},
			minLength: 1,
			change: function (event, ui) {
				if (ui.item == null || ui.item == undefined) {
					$("input#customer").val("");
				}
			}
	});

	$('.product_div').each(function(){
		productAutocomp($(this).find("#p_name"));
		productPriceTracking($(this).find("#p_qty"));
	});

	$('.service_div').each(function(){
		servicePriceTracking($(this).find("#s_price"));
	});

	$('.addproduct,.addservice').on('click',function(e){
		e.preventDefault();
		var type = $(this).closest('.box').attr("type");
		$clone = $('.'+type+'_div:eq(0)').clone();
		$clone.find("input").val("");
		$clone.find("input").prop("disabled",false);
		
		var index = $('.'+type+'_div').length;

		if (type == "product")
		{	
			$clone.find("#p_id").attr("name","product["+index+"][p_id]");
			$clone.find("#p_price").attr("name","product["+index+"][p_price]");
			$clone.find("#p_qty").attr("name","product["+index+"][p_qty]");
			$clone.find("#p_oid").attr("name","product["+index+"][p_oid]");

			productAutocomp($clone.find("#p_name"));
			productPriceTracking($clone.find("#p_qty"));
		}
		else
		{
			$clone.find("#s_name").attr("name","service["+index+"][s_name]");
			$clone.find("#s_price").attr("name","service["+index+"][s_price]");
			$clone.find("#s_oid").attr("name","service["+index+"][s_oid]");

			servicePriceTracking($clone.find("#s_price"));
		}
		
		$clone.insertAfter("."+type+"_div:last");
	});

	$(document).delegate('.removeproduct,.removeservice','click',function(e){
		e.preventDefault();
		var that = $(this); 
		var box = $(this).closest('.box');
		var type = $(box).attr("type");
		if($('.remove'+type).length <= 1)
		{
			return;
		}

		var findObj = (type == "service")?"s_oid":"p_oid";

		if($(box).find("#"+findObj).length > 0 && $(box).find("#"+findObj).val() != "")
		{
			var url = admin_path()+'invoice/deleteOrder';
			var param = {id:$(box).find("#"+findObj).val()};
			$.post(url,param,function(e){
				if (e == "success") {
					$(that).closest('.'+type+'_div').remove();
				}else{
					$("#flash_msg").html(error_msg_box ('An error occurred while processing.'));
				}
				updateTotal();
			});
		}
		else
		{
			$(this).closest('.'+type+'_div').remove();
			updateTotal();
		}

	});

	var datepicker = $.fn.datepicker.noConflict();
    $.fn.btdatepicker = datepicker;  
	$("#sale_date").btdatepicker({format: 'mm/dd/yyyy'});
	if ($('#sale_date').val() == "")
		$('#sale_date').btdatepicker('update', Date());

	$("#save").on("click",function(e){
		e.preventDefault();
		$("input").prop("disabled",false);
		$("form").submit();
	});

	$("#print").on("click",function(e){
		e.preventDefault();
		$("#op").val("print");
		$("form").submit();
	});

	updateTotal();
});

function productAutocomp(obj){
	$(obj).autocomplete({
			source: admin_path()+"product/autocomplete",
			select: function( event, ui ) {
				$row = $(event.target).closest(".row");
				$row.find("#p_qty").val(1);
				$row.find("#p_qty").data("price",ui.item.price);
				$row.find("#p_qty").data("qty",ui.item.qty);
				$row.find("#p_price").val(ui.item.price);
				$row.find("#p_id").val(ui.item.p_id);
				updateTotal();
				return true;
			},
			minLength: 1,
			change: function (event, ui) {
				if (ui.item == null || ui.item == undefined) {
					$(event.target).val("");
				}
			}
	});
}

function productPriceTracking(obj)
{
	$(obj).on("keyup",function(e){
		var row = $(this).closest(".row");
		var qty = $(this).val();
		var stock_onhand  = $(this).data('qty');
		if (stock_onhand < qty)
		{
			alert("Only "+stock_onhand+" qty available.");
			return false;
		}
		var price = qty * $(this).data('price');
		row.find("#p_price").val(price);
		updateTotal();
	})
}

function servicePriceTracking(obj)
{
	$(obj).on("keyup",function(e){
		updateTotal();
	})
}

function updateTotal()
{
	var totalP = 0;
	var totalS = 0;
	var total = 0;
	$(".product_div #p_price").each(function(){
		totalP += Number($(this).val());
	});

	$(".service_div #s_price").each(function(){
		totalS += Number($(this).val());
	});
	
	total = totalP + totalS;
	$('#subtotal').html(total);

	var vatP = (totalP * vatRate)/100;
	var taxS = (totalS * taxRate)/100;
	var tax = vatP+taxS;

	tax = Math.round(tax * 100) / 100

	$("#tax").html(tax);
	$("#total").html(total+tax);
}