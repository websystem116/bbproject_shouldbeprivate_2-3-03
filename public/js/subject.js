/*
教科入力フォーム処理
*/
$(function () {
	// 追加ボタン押下時
	$('.add-input-subject').on('click', function () {
		// tableのtbody要素を取得
		var tbodyElems = document.getElementById('subject_table_tbody');
		// 行（tr要素）を取得
		var rowElems = tbodyElems.rows;
		// 取得した行（tr要素）の1回分を取得
		var htmlStr = '';
		for (i = 0, len = rowElems.length; i < 1; i++) {
			//先頭のtr要素を取得
			htmlStr += rowElems[i].outerHTML;
		}
		//tbodyに追加
		$('#subject_table_tbody').append(htmlStr);
		//No更新
		put_result();
		//追加した行のselectedをリセット
		$('.subject_name').last().val("");
		$('.hidden_subject_id').last().val("");

	})

	//No連番ふる処理(教科)
	function put_result() {
		$(document).find(".subject_No").each(function (i) {
			i = i + 1;
			$(this).text(i);
		});
	}

	//削除ボタン押下時
	$(document).on('click', '.subject-delete', function () {
		if (!window.confirm('本当に削除しますか？点数が登録されている場合、復元できません')) {
			window.alert('キャンセルされました');
			return false;
		}
		// tableのtbody要素を取得
		var tbodyElems = document.getElementById('subject_table_tbody');
		// 行（tr要素）を取得
		var rowElems = tbodyElems.rows;

		//tr要素の数を取得
		var size = $(rowElems).length;

		subject_id = $(rowElems).last().find('.hidden_subject_id').val();//消すsubjectのid取得
		console.log(subject_id);
		//コントローラーメソッドのdestroy呼び出し
		if (subject_id) {//追加枠にsubject_idが無ければajaxしない
			$.ajax({
				headers: {
					// POSTのときはトークンの記述がないと"419 (unknown status)"になるので注意
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				// POSTだけではなく、GETのメソッドも呼び出せる
				type: 'POST',
				// ルーティングで設定したURL
				url: '/shinzemi/subject/' + subject_id, // 引数も渡せる
				data: {
					'id': subject_id,
					'identification_flg': 1,//1:教科書削除　2：試験削除
					'_method': 'DELETE'
				},
				dataType: 'text',
			}).done(function (data) {
				alert('データを削除しました。');
			}).fail(function (XMLHttpRequest, textStatus, errorThrown) {
				alert('エラーが発生しました。')//通信失敗
				console.log("XMLHttpRequest : " + XMLHttpRequest.status);
				console.log("textStatus     : " + textStatus);
				console.log("errorThrown    : " + errorThrown.message);
			});
		}

		//最後の一行なら削除できないようにする
		if (size > 1) {
			$(rowElems).last().remove();
		} else {
			alert("最初の一行は削除できません。");
		}

	});

});

//ページ読み込み時にNo振りなおす
//No連番ふる処理(教科)
$(function () {
	$(".subject_No").each(function (i) {
		i = i + 1;
		$(this).text(i);
	});
});

/*
教科入力フォーム処理end
*/
