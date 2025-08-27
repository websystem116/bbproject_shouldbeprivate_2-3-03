$(function () {
	$(".school_classification").change(function () {
		// コントローラーのメソッドに渡す値
		var id = $(this).val();//1:高校 2: 3:
		// console.log(id);
		$.ajax({
			headers: {
				// POSTのときはトークンの記述がないと"419 (unknown status)"になるので注意
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: 'GET',
			// ルーティングで設定したURL
			url: '/shinzemi/student/' + id + '/get_shools',
		}).done(function (results) {//成功
			// console.log(results);
			count = $(results).length;
			// console.log(count);
			$('.choice_school > option').remove();
			for (var i = 0; i < count; i++) {
				$('.choice_school').append($('<option>').html(results[i]['name']).val(results[i]['id']));
				// console.log(results[i]['id']);
			}
		}).fail(function (jqXHR, textStatus, errorThrown) {//失敗
			console.log(jqXHR, textStatus, errorThrown);
		}).always(function () {
		});
	});
});


$(function () {
	$(".reset").on('click', function () {
		console.log('リセット');
		window.location.href = "/shinzemi/student";//URLリセットする
		// $(this).parent('form').find(':text').val("");
		// $(this).parent('form').find(':password').val("");
		// $(this).parent('form').find('input[type="number"]').val("");
		// $(this.form).find("textarea, :text, select, radio, checkbox").val("").end().find(":checked").prop("checked", false);
	});
});

$(function () {
	$(".banks_select").change(function () {
		// コントローラーのメソッドに渡す値
		console.log('banks_select');
		var id = $(this).val();
		console.log(id);
		$.ajax({
			headers: {
				// POSTのときはトークンの記述がないと"419 (unknown status)"になるので注意
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: 'GET',
			// ルーティングで設定したURL
			url: '/shinzemi/student/' + id + '/get_branch_banks',
		}).done(function (results) {//成功
			// console.log(results);
			count = $(results).length;
			// console.log(count);
			$('.branch_banks > option').remove();
			for (var i = 0; i < count; i++) {
				$('.branch_banks').append($('<option>').html(results[i]['code'] + "　" + results[i]['name']).val(results[i]['code']));
				// console.log(results[i]['id']);
			}
		}).fail(function (jqXHR, textStatus, errorThrown) {//失敗
			console.log(jqXHR, textStatus, errorThrown);
		}).always(function () {
		});
	});
});
