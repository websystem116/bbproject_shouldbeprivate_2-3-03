$(function () {
	$(document).on('click', '.make_sales', function () {

		alert("テスト");
		location.href = '{{ route(\'sales.data_migration\') }}';
	});
});
