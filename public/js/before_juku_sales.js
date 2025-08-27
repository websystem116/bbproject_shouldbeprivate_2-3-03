$(function () {
	$(".product_id").select2({
		placeholder: "選択してください",
		allowClear: true
	});
	// 追加ボタン押下時
	$(document).on('click', ".add-input-sale", function () {
		// tableのtbody要素を取得
		var tbodyElems = document.getElementById('sales_table_tbody');
		// 行（tr要素）を取得
		var rowElems = tbodyElems.rows;
		// 取得した行（tr要素）の1回分を取得
		var htmlStr = '';
		for (i = 0, len = rowElems.length; i < 1; i++) {
			//先頭のtr要素を取得
			htmlStr += rowElems[i].outerHTML;
		}
		console.log(htmlStr);
		//tbodyに追加
		$('tbody').append(htmlStr);
		//No更新

		put_result();
		//追加した行のselectedをリセット
		$('.sales_date').last().attr("value", "");
		$('.payment_date').last().attr("value", "");
		// $('.product_id option:selected').last().removeAttr("selected");

		// $('.product_id').last().select2().val(null).trigger('change');
		// console.log($(document).find('.product_id').last().siblings('.select2'));
		// $('.product_id').val("val").trigger('change');
		$('.price_after_discount').last().attr("value", "");
		$('.price_after_discount').last().val("");
		$('.note').last().val("");

		$('.select2').last().remove();
		$(".product_id").select2({
			placeholder: "選択してください",
			width: "320px"
		}).last().val(0).trigger('change');

	})

	//No連番ふる処理
	function put_result() {
		$("td.No").each(function (i) {
			i = i + 1;
			$(this).text(i);
		});
	}

	//削除ボタン押下時
	$(document).on('click', '.sale-delete', function () {
		// tableのtbody要素を取得
		var tbodyElems = document.getElementById('sales_table_tbody');
		// 行（tr要素）を取得
		var rowElems = tbodyElems.rows;
		//tr要素の数を取得
		var size = $(rowElems).length;
		//最後の一行なら削除できないようにする
		if (size > 1) {
			$(rowElems).last().remove();
		} else {
			alert("最初の一行は削除できません。");
		}

	});

});

//ページ読み込み時にNo振りなおす
$(function () {
	$("td.No").each(function (i) {
		i = i + 1;
		$(this).text(i);
	});
});

$(function () {
	// $(".product_id").change(function () {
	$(document).on("change", ".product_id", function () {
		var product_id_index = $('.product_id').index(this);//何行目の商品が変更されたか
		// コントローラーのメソッドに渡す値
		var id = $(this).val();//DBproductsのid
		console.log(id);
		$.ajax({
			headers: {
				// POSTのときはトークンの記述がないと"419 (unknown status)"になるので注意
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: 'GET',
			// ルーティングで設定したURL
			url: '/shinzemi/before_juku_sales/' + id + '/get_product_price',
		}).done(function (results) {//成功
			var result = Number(results);
			if (!result == 0) {
				$('.price_after_discount').eq(product_id_index).val(result);
			} else {
				$('.price_after_discount').eq(product_id_index).val('');
			}
		}).fail(function (jqXHR, textStatus, errorThrown) {//失敗
			console.log(jqXHR, textStatus, errorThrown);
		}).always(function () {
			// 成否に関わらず実行されるコールバック
		});
	})
});
