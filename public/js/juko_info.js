$(function () {
	// 追加ボタン押下時
	$('.add-input-sale').on('click', function () {
		// tableのtbody要素を取得
		var tbodyElems = document.getElementById('juko_table_tbody');
		// 行（tr要素）を取得
		var rowElems = tbodyElems.rows;
		// 取得した行（tr要素）の1回分を取得
		var htmlStr = '';
		for (i = 0, len = rowElems.length; i < 1; i++) {
			//先頭のtr要素を取得
			htmlStr += rowElems[i].outerHTML;
		}
		//tbodyに追加
		$('tbody').append(htmlStr);
		//No更新
		put_result();
		//data-id更新
		put_data_id();
		//追加した行のselectedをリセット
		// $('.product_id option:selected').last().removeAttr("selected");
		$('.select2').last().remove();
		$(".product_id").select2({
			placeholder: "選択してください",
			width: "100%"
		}).last().val(0).trigger('change');
	})

	//No連番ふる処理
	function put_result() {
		$("td.No").each(function (i) {
			i = i + 1;
			$(this).text(i);
		});
	}

	//削除用data-idの振り直し
	function put_data_id() {
		$(".product_delete").each(function (i) {
			console.log($(this));
			$(this).data('id', i);
			i = i + 1;
		});
	}

	//入力フォームの削除ボタン押下時
	$(document).on('click', '.product_delete', function () {
		// tableのtbody要素を取得
		var tbodyElems = document.getElementById('juko_table_tbody');
		// 行（tr要素）を取得
		var rowElems = tbodyElems.rows;

		var click = $(this).data('id');
		// console.log(click);
		//tr要素の数を取得
		var size = $(rowElems).length;
		//最後の一行なら削除できないようにする
		if (size > 1) {
			$(rowElems).eq(click).remove();
		} else {
			alert("最初の一行は削除できません。");
		}
		put_data_id();
		put_result();
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
	$(".reset").on('click', function () {
		console.log('リセット');
		window.location.href = "/shinzemi/juko_info";//URLリセットする
	});
});
