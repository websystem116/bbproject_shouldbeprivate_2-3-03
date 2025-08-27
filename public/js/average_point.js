$(function () {
	var isChanged = false;
	$(function () {//保存せずにページ遷移しようとしたらアラート出す処理
		$(document).on('change', 'input,select,textarea', function () {
			isChanged = true;
			console.log(isChanged);
		});
		$(window).on('beforeunload', function () {
			if (isChanged) {
				if (!confirm('保存されていません。\n入力した情報が失われますがよろしいですか？')) {
					return false;
				}
			}
		});

		//確認ダイアログ
		$('.confirm').click(function () {
			if (confirm('登録してもよろしいですか？')) {
				$(window).off('beforeunload');
			} else {
				//キャンセルした場合
				$(window).on('beforeunload');
				return false
			}
		});
	});
	$(function () {
		var elements = "input[type=text]";
		$(elements).keypress(function (e) {
			var c = e.which ? e.which : e.keyCode;
			if (c == 13) {
				var index = $(elements).index(this);
				var criteria = e.shiftKey ? ":lt(" + index + "):last" : ":gt(" + index + "):first";
				$(elements + criteria).focus();
				e.preventDefault();
			}
		});
	});
});
