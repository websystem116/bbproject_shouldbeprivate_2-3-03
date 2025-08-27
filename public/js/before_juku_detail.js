$(function () {
	// $(".product_id").change(function () {
	$(document).on('click', '.sales_disp', function () {
		console.log('click');
		var select_year = $('.year').val();
		console.log(select_year);

		const num = month.selectedIndex;
		const str = month.options[num].value;
		console.log(str);
		// コントローラーのメソッドに渡す値
		// 	var id = $(this).val();//DBproductsのid
		// 	console.log(id);
		// 	$.ajax({
		// 		headers: {
		// 			// POSTのときはトークンの記述がないと"419 (unknown status)"になるので注意
		// 			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		// 		},
		// 		type: 'GET',
		// 		// ルーティングで設定したURL
		// 		url: '/shinzemi/before_juku_sales/' + id + '/get_product_price',
		// 	}).done(function (results) {//成功
		// 		$('.price_after_discount').eq(product_id_index).val(results);
		// 	}).fail(function (jqXHR, textStatus, errorThrown) {//失敗
		// 		console.log(jqXHR, textStatus, errorThrown);
		// 	}).always(function () {
		// 		// 成否に関わらず実行されるコールバック
		// 	});
	})
});
