$(function () {
	var performance_no = 0;
	var tranceportation_no = 0;
	var other_performance_no = 0;

	if ($(".performance_add_row").length == undefined) {
		performance_no = $(".performance_add_row").length;

	}
	if ($(".tranceportation_add_row").length == undefined) {
		tranceportation_no = $(".add-tranceportation_add_row").length;

	}
	if ($(".other_performance_add_row").length == undefined) {
		other_performance_no = $(".other_performance_add_row").length;

	}
	// 追加ボタン押下時
	$(document).on("click", ".add-performance-row", function () {
		performance_no = parseInt(performance_no) + 1;

		let clone1 = $(".performance_row").clone();
		clone1.removeClass("performance_row").addClass("performance_add_row").appendTo(".performance_tr").find(".select_flg").prop("checked", false);
		clone1.find(".apploval_flg").prop("checked", false);
		clone1.find(".apploval_flg").attr("value", performance_no);
		clone1.find(".school_building").val("");
		clone1.find(".job_description").val("");
		clone1.find(".working_time").val("");
		clone1.find(".remarks").val("");

	});
	$(document).on("click", ".delete-performance-row", function () {
		if (performance_no != 0) {
			performance_no = parseInt(performance_no) - 1;
		}
		$(this).parent().parent().find(".performance_tr").find(".performance_add_row").last().remove();
	});
	$(document).on("click", ".add-tranceportation-row", function () {
		tranceportation_no = parseInt(tranceportation_no) + 1;

		let clone1 = $(".tranceportation_row").clone();
		clone1.removeClass("tranceportation_row").addClass("tranceportation_add_row").appendTo(".tranceportation_tr").find(".tranceportation_select_flg").prop("checked", false);
		clone1.find(".tranceportation_apploval_flg").prop("checked", false);
		clone1.find(".tranceportation_apploval_flg").attr("value", tranceportation_no);
		clone1.find(".tranceportation_round_trip_flg").attr("value", tranceportation_no);
		clone1.find("tranceportation_school_building").val("");
		clone1.find(".tranceportation_route").val("");
		clone1.find(".tranceportation_boarding_station").val("");
		clone1.find(".tranceportation_get_off_station").val("");
		clone1.find(".tranceportation_unit_price").val("");
		clone1.find(".tranceportation_round_trip_flg").prop("checked", false);
		clone1.find(".tranceportation_fare").val("");
		clone1.find(".tranceportation_remarks").val("");

	});
	$(document).on("click", ".delete-tranceportation-row", function () {
		if (tranceportation_no != 0) {
			tranceportation_no = parseInt(tranceportation_no) - 1;
		}
		$(this).parent().parent().find(".tranceportation_tr").find(".tranceportation_add_row").last().remove();
	});
	$(document).on("click", ".add-other-performance-row", function () {
		other_performance_no = parseInt(other_performance_no) + 1;

		let clone1 = $(".other_performance_row").clone();
		clone1.removeClass("other_performance_row").addClass("other_performance_add_row").appendTo(".other_performance_tr").find(".other_select_flg").prop("checked", false);
		clone1.find(".other_apploval_flg").prop("checked", false);
		clone1.find(".other_apploval_flg").attr("value", other_performance_no);
		clone1.find("other_superior_approval").prop("checked", false);
		clone1.find(".other_school_building").val("");
		clone1.find(".other_job_description").val("");
		clone1.find(".other_remarks").val("");

	});
	$(document).on("click", ".delete-other-performance-row", function () {
		if (other_performance_no != 0) {
			other_performance_no = parseInt(other_performance_no) - 1;
		}
		$(this).parent().parent().find(".other_performance_tr").find(".other_performance_add_row").last().remove();
	});
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
		var tbodyElems = document.getElementById('juko_table_tbody');
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
