$(function () {
	// 追加ボタン押下時
	$(document).on("click", ".add-payment-row", function () {

		let clone1 = $(".payment_row").clone();
		clone1.removeClass("payment_row").addClass("payment_add_row").appendTo(".payment_tr");
		clone1.find(".payment_id").val("");
		clone1.find(".payment_date").val("");
		clone1.find(".sale_month").val("");
		clone1.find(".price").val("");
		clone1.find(".division").val("");
		clone1.find(".remarks").val("");

	});
	$(document).on("click", ".delete-payment-row", function () {

		$(this).parent().parent().parent().find(".payment_tr").find(".payment_add_row").last().remove();
	});
});
$(function () {
	$(document).on("click", ".panel-body", function () {
		$(".monthPick").datepicker({
			autoclose: true,
			language: 'ja',
			clearBtn: true,
			format: "yyyy-mm",
			minViewMode: 1,
			maxViewMode: 2
		});
	});
});
