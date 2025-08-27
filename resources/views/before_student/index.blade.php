@extends("layouts.app")
@section("content")
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
<script>
	$(function() {
		$('.select_search1').select2({
				language: "ja",
				width: '100px'
		});
		$('.select_search2').select2({
				language: "ja",
				width: '150px'
		});
		$('.select_search3').select2({
				language: "ja",
				width: '400px'
		});
});

$(function() {
	$(".reset").on('click', function() {
			console.log('リセット');
			window.location.href = "/shinzemi/before_student"; //URLリセットする
	});
	$('.check_all').on("click", function() {
		if ($('input[name="student_check[]"]:checked').length == 0) {
			$('input[name="student_check[]"]').prop('checked', true);
		} else {
			$('input[name="student_check[]"]').prop('checked', false);
		}
	});
});
$(function() {
	$(document).on('click', '.output', function() {
		var student_id_cnt = $('input[name="student_check[]"]:checked').length;
		if (student_id_cnt == 0) {
			alert('出力する生徒をチェックしてください。');
			return false;
		}
		if (!confirm('CSVを出力します。よろしいですか。')) {
			return false;
		} else {

		var form = $(this).parents('form');
		var action_url = "{{ route('before_student.before_student_info_output') }}";
		form.attr('action', action_url);
		form.submit();
		}
	});
});
</script>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">入塾前生徒リスト</div>
				<div class="panel-body">
					{{ Form::model($student_search, ['route' => 'before_student.index', 'method' => 'GET', 'class' => 'form-horizontal']) }}
					<div class="">
						<div class="panel-group" id="sampleAccordion">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">
										<a data-toggle="collapse" data-parent="#sampleAccordion" href="#sampleAccordionCollapse1">▽検索条件</a>
									</h3>
								</div>
								<div id="sampleAccordionCollapse1" class="panel-collapse collapse in">
									<div class="panel-body">
										<div class="form-group row">
											<div class="col-xs-1 text-left">
												{{ Form::label('number', '管理No', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::number('id_start',$student_search['id_start'] ?? null, ['placeholder' => '管理No', 'class' => 'form-control form-name']) }}
											</div>
											<div class="col-xs-1 text-center">
												{{ Form::label('wave', '～', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::number('id_end', $student_search['id_end'] ?? null, ['placeholder' => '管理No', 'class' => 'form-control form-name']) }}
											</div>
										</div>
										<div class="form-group row">
											<div class="col-xs-1 text-left">
												{{ Form::label('number', '生徒No', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::number('no_start',$student_search['no_start'] ?? null, ['placeholder' => '生徒No', 'class' => 'form-control form-name']) }}
											</div>
											<div class="col-xs-1 text-center">
												{{ Form::label('wave', '～', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::number('no_end', $student_search['no_end'] ?? null, ['placeholder' => '生徒No', 'class' => 'form-control form-name']) }}
											</div>
										</div>
										<div class="form-group row">
											<div class="col-xs-1 text-left">
												{{ Form::label('name', '生徒氏名', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::text('surname',$student_search['surname'] ?? null, ['placeholder' => '姓', 'class' => 'form-control form-name']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::text('name', $student_search['name'] ?? null, ['placeholder' => '名', 'class' => 'form-control form-name']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::text('surname_kana', $student_search['surname_kana'] ?? null, ['placeholder' => '姓カナ', 'class' => 'form-control form-name hira_change']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::text('name_kana', $student_search['name_kana'] ?? null, ['placeholder' => '名カナ', 'class' => 'form-control form-name hira_change']) }}
											</div>
										</div>
										<div class="form-group row">
											<div class="col-xs-1 text-left">
												{{ Form::label('phone', '電話番号', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::text('phone', $student_search['phone'] ?? null, ['placeholder' => '電話番号', 'class' => 'form-control form-name']) }}
											</div>
											<div class="col-xs-1 text-left">
												{{ Form::label('school_name', '学校名', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::select('school_id',$schools_select_list,$student_search['school_id'] ?? null, ['placeholder' => '選択してください', 'class' => 'form-control form-name select_search2']) }}
											</div>
											<div class="col-xs-2 mb-1">
												<label>
													{{Form::checkbox('brothers_flg', '1',$student_search['brothers_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'brothers_flg'])}}兄弟姉妹
												</label>
											</div>
											<div class="col-xs-2 mb-1">
												<label>
													{{Form::checkbox('fatherless_flg', '1',$student_search['fatherless_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'fatherless_flg'])}}ひとり親家庭
												</label>
											</div>
											<div class="col-xs-2 mb-1">
												<label>
													{{Form::checkbox('sign_up_juku_flg', '1', $student_search['sign_up_juku_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'sign_up_juku_flg'])}}未入塾者のみ
												</label>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-xs-1 text-left">
												{{ Form::label('greade', '学年', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::select('grade_start', config('const.school_year'),$student_search['grade_start'] ?? null,['placeholder' => '選択してください', 'class' => 'form-control select_search2']) }}
											</div>
											<div class="col-xs-1 text-center">
												{{ Form::label('wave', '～', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::select('grade_end', config('const.school_year'), $student_search['grade_end'] ?? null,['placeholder' => '選択してください', 'class' => 'form-control select_search2']) }}
											</div>
											<div class="col-xs-1 text-left">
												{{ Form::label('school_building', '校舎名', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::select('school_building_id',$schooolbuildings_select_list,$student_search['school_building_id'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
											</div>
										</div>
										<div class="form-group row">
											<div class="col-xs-1 text-left">
												{{ Form::label('product_select', '商品名', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-5 mb-5">
												{{ Form::select('product_select',$products_select_list,$student_search['product_select'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="text-center">
												{{ Form::submit('検索', ['name' => 'search', 'class' => 'btn btn-primary']) }}
												{{ Form::reset('リセット', ['class' => 'btn btn-primary reset']) }}
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					{{ Form::close() }}
				</div>
			</div>
			{{ Form::model($student_search, ['route' => 'before_student.index', 'method' => 'POST', 'class' => 'form-horizontal']) }}
			<div class="panel-body">
				<div>{{ $before_student->count() }} 件を表示</div>
				<a href="{{ url('/shinzemi/before_student/create') }}" class="btn btn-success btn-sm" title="Add New student">
					新規追加
				</a>
				<br>
				<br>
				<a class="btn btn-primary check_all">
					一括選択
				</a>
				<span>
					{{ Form::button('CSV出力', ['name' => 'output','class' => 'btn btn-primary output']) }}
					<div class="table-responsive">
				</span>
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th style="width: 5%">選択</th>
							<th>管理No</th>
							<th>生徒No</th>
							<th>氏名</th>
							<th>学年</th>
							<th>校舎名</th>
							<th>学校名</th>
							<th>登録年月</th>
							<th>入塾</th>
							<th>編集</th>
						</tr>
					</thead>
					<tbody>
						@foreach($before_student as $item)
						<tr>
							<label>
								<td text-center>{{Form::checkbox('student_check[]', $item->before_student_no, false, ['class'=>'custom-control-input form-checkbox'])}}
								</td>
							</label>
							<td>{{ $item->id}} </td>
							<td>{{ $item->before_student_no}} </td>
							<td>{{ $item->surname}} {{ $item->name}}</td>
							<td>{{ config('const.school_year')[$item->grade] ?? ""}}</td>
							<td>{{ $item->schoolbuilding->name ?? ""}}</td>
							<td>{{ $item->school->name ?? ""}}</td>
							<td>{{ $item->getCreatedAtAttribute()}}</td>
							<td>{{ config('const.sign_up_juku_methods')[$item->sign_up_juku_flg]}}</td>
							<td>
								<a href="{{ url('/shinzemi/before_student/' . $item->id . '/edit') }}" title="Edit bank" class="btn btn-primary btn-xs">編集
								</a>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			{{ Form::close() }}
		</div>
	</div>
</div>
</div>
@endsection
