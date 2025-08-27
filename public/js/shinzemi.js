$(function () {


	//ひらがな→カタカナ
	$(".hira_change").on("compositionend blur", function (event) {
		hiraChange($(this));
	});

	hiraChange = function (ele) {

		var val = ele.val();
		var hira = val.replace(/[ぁ-ん]/g, function (s) {
			return String.fromCharCode(s.charCodeAt(0) + 0x60)
		});

		if (val.match(/[ァ-ン]/g)) {
			$(ele).val(hira);
		} else {
			$(ele).val(hira);
		}

	};

	$(".char_change").on("compositionend blur", function (event) {
		charactersChange($(this));
	});

	charactersChange = function (ele) {
		var val = ele.val();
		var han = val.replace(/[Ａ-Ｚａ-ｚ０-９]/g, function (s) {
			return String.fromCharCode(s.charCodeAt(0) - 0xFEE0)
		});

		if (val.match(/[Ａ-Ｚａ-ｚ０-９]/g)) {
			$(ele).val(han.replace(/[‐－―]/g, "-"));
		} else {
			$(ele).val(han.replace(/[‐－―]/g, "-"));
		}
	}

	$(".hankaku_kana_change").on("compositionend blur", function (event) {
		hankakukanaChange($(this));
	});

	hankakukanaChange = function (ele) {
		var val = ele.val();
		var kanaMap = {
			"ガ": "ｶﾞ", "ギ": "ｷﾞ", "グ": "ｸﾞ", "ゲ": "ｹﾞ", "ゴ": "ｺﾞ",
			"ザ": "ｻﾞ", "ジ": "ｼﾞ", "ズ": "ｽﾞ", "ゼ": "ｾﾞ", "ゾ": "ｿﾞ",
			"ダ": "ﾀﾞ", "ヂ": "ﾁﾞ", "ヅ": "ﾂﾞ", "デ": "ﾃﾞ", "ド": "ﾄﾞ",
			"バ": "ﾊﾞ", "ビ": "ﾋﾞ", "ブ": "ﾌﾞ", "ベ": "ﾍﾞ", "ボ": "ﾎﾞ",
			"パ": "ﾊﾟ", "ピ": "ﾋﾟ", "プ": "ﾌﾟ", "ペ": "ﾍﾟ", "ポ": "ﾎﾟ",
			"ヴ": "ｳﾞ", "ヷ": "ﾜﾞ", "ヺ": "ｦﾞ",
			"ア": "ｱ", "イ": "ｲ", "ウ": "ｳ", "エ": "ｴ", "オ": "ｵ",
			"カ": "ｶ", "キ": "ｷ", "ク": "ｸ", "ケ": "ｹ", "コ": "ｺ",
			"サ": "ｻ", "シ": "ｼ", "ス": "ｽ", "セ": "ｾ", "ソ": "ｿ",
			"タ": "ﾀ", "チ": "ﾁ", "ツ": "ﾂ", "テ": "ﾃ", "ト": "ﾄ",
			"ナ": "ﾅ", "ニ": "ﾆ", "ヌ": "ﾇ", "ネ": "ﾈ", "ノ": "ﾉ",
			"ハ": "ﾊ", "ヒ": "ﾋ", "フ": "ﾌ", "ヘ": "ﾍ", "ホ": "ﾎ",
			"マ": "ﾏ", "ミ": "ﾐ", "ム": "ﾑ", "メ": "ﾒ", "モ": "ﾓ",
			"ヤ": "ﾔ", "ユ": "ﾕ", "ヨ": "ﾖ",
			"ラ": "ﾗ", "リ": "ﾘ", "ル": "ﾙ", "レ": "ﾚ", "ロ": "ﾛ",
			"ワ": "ﾜ", "ヲ": "ｦ", "ン": "ﾝ",
			"ァ": "ｧ", "ィ": "ｨ", "ゥ": "ｩ", "ェ": "ｪ", "ォ": "ｫ",
			"ッ": "ｯ", "ャ": "ｬ", "ュ": "ｭ", "ョ": "ｮ",
			"。": "｡", "、": "､", "ー": "ｰ", "「": "｢", "」": "｣", "・": "･", "　": " "
		}
		var reg = new RegExp('(' + Object.keys(kanaMap).join('|') + ')', 'g');

		var hankana = val.replace(reg, function (match) {
			return kanaMap[match];
		})

		if (val.match(/゛/g)) {
			$(ele).val(hankana.replace(/゛/g, 'ﾞ'));
		} else if (val.match(/゜/g)) {
			$(ele).val(hankana.replace(/゜/g, 'ﾟ'));
		}
		$(ele).val(hankana);
	}
	$(function () {
		$("input").keypress(function (ev) {
			if ((ev.which && ev.which === 13) || (ev.keyCode && ev.keyCode === 13)) {
				return false;
			} else {
				return true;
			}
		});
	});


	$(window).on('load', function () {
		if (document.URL.match("average_point")) {
			//指定する文字列がURLに含まれる場合に実行する内容
			//成績情報のページだけ確認ダイアログ個別に書いてます。
		} else {
			//確認ダイアログ
			$(function () {
				$('.confirm').click(function () {
					if (confirm('登録してもよろしいですか？')) {
						// 「OK」ならそのまま通る
					} else {
						//キャンセルした場合
						return false
					}
				});
			});
		}
	});
});
