$(function () {
	$(".resultcategory_select").change(function () {
		// コントローラーのメソッドに渡す値
		console.log('implementation_select');
		var id = $(this).val();
		console.log(id);
		$.ajax({
			headers: {
				// POSTのときはトークンの記述がないと"419 (unknown status)"になるので注意
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: 'GET',
			// ルーティングで設定したURL
			url: '/shinzemi/score/' + id + '/get_implementations',
		}).done(function (results) {//成功
			// console.log(results);
			count = $(results).length;
			// console.log(count);
			$('.implementation_select > option').remove();
			for (var i = 0; i < count; i++) {
				$('.implementation_select').append($('<option>').html(results[i]['implementation_name']).val(results[i]['implementation_no']));
				// console.log(results[i]['id']);
			}
		}).fail(function (jqXHR, textStatus, errorThrown) {//失敗
			console.log(jqXHR, textStatus, errorThrown);
		}).always(function () {
		});
	});
});
