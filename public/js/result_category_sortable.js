$(function () {
	Sortable.create(SortableArea, {
		onSort: function () {
			let RowCount = $('.js_result_category_row').length;//何行あるか
			console.log(RowCount);

			//行インデックス振り直し
			if (RowCount > 1) {//行インデックス振り直し
				let i = 1;
				var result_category_id_array = new Array();
				var sort_order_array = new Array();
				$('.sort_order').each(function (index, ele) {
					$(this).find('.sort_order_lavel').text(i);
					let result_category_id = $(this).find('.hidden_result_category_id').val();
					result_category_id_array.push(result_category_id);
					sort_order_array.push(i);
					i++;
				});
			}

			//成績カテゴリーの表示順更新(result_category_idと表示順を配列で渡して更新)
			$.ajax({
				headers: {
					// POSTのときはトークンの記述がないと"419 (unknown status)"になるので注意
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				// POSTだけではなく、GETのメソッドも呼び出せる
				type: 'POST',
				// ルーティングで設定したURL
				url: '/shinzemi/result_category/order_save', // 引数も渡せる
				data: {
					'result_category_id_array': result_category_id_array,
					'sort_order_array': sort_order_array
				},
				dataType: 'text',
			}).done(function (data) {
				// alert('成功');
			}).fail(function (XMLHttpRequest, textStatus, errorThrown) {
				alert('エラーが発生しました。')//通信失敗
				console.log("XMLHttpRequest : " + XMLHttpRequest.status);
				console.log("textStatus : " + textStatus);
				console.log("errorThrown : " + errorThrown.message);
			});

		}
	});
});
