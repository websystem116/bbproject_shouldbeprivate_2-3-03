@extends("layouts.app")
@section("content")
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
<script src="{{ asset('/js/juko_info.js') }}"></script>
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
    $('.select_search_2').select2({
        language: "ja",
        width: '400px'
    });

		$('.check_all').on("click", function() {
			if ($('input[name="check[]"]:checked').length == 0) {
				$('input[name="check[]"]').prop('checked', true);
			} else {
				$('input[name="check[]"]').prop('checked', false);
			}
		});
});



function getChecked() {
    var checked = [];
    $('input[name="check[]"]:checked').each(function() {
        checked.push($(this).val());
    });

    // validations
    var error_message = [];

    // 1件以上選択されているかチェック
    if (checked.length == 0) {
        error_message.push('1件以上チェックしてください。');
    }

    // 講座が選択されているかチェック
    if ($('select[name="selected_product"]').val() == 0) {
        error_message.push('商品を選択してください。');
    }

    // エラーメッセージがある場合は、アラートを表示して、submitしない
    if (error_message.length > 0) {
        alert(error_message.join(' '));
        return false;
    }


    // formを取得　id = bulk_create
    var form = document.getElementById('bulk_store');

    // もしinput.name='checked[]'が存在していたら、削除する
    if (document.getElementsByName('checked[]').length > 0) {
        var inputs = document.getElementsByName('checked[]');
        for (var i = 0; i < inputs.length; i++) {
            form.removeChild(inputs[i]);
        }
    }

    // formに配列を追加 foreachで回す
    checked.forEach(function(value) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'checked[]';
        input.value = value;
        form.appendChild(input);
    });

    form.submit();

    return checked;
}

function getChecked_for_delete() {
    var checked = [];
    $('input[name="check[]"]:checked').each(function() {
        checked.push($(this).val());
    });

    // validations
    var error_message = [];

    // 1件以上選択されているかチェック
    if (checked.length == 0) {
        error_message.push('1件以上チェックしてください。');
    }

    // 講座が選択されているかチェック
    if ($('select[name="selected_product_for_delete"]').val() == 0) {
        error_message.push('商品を選択してください。');
    }


    // エラーメッセージがある場合は、アラートを表示して、submitしない
    if (error_message.length > 0) {
        alert(error_message.join(' '));
        return false;
    }


    // formを取得
    var form = document.getElementById('bulk_delete');

    // もしinput.name='checked[]'が存在していたら、削除する
    if (document.getElementsByName('checked[]').length > 0) {
        var inputs = document.getElementsByName('checked[]');
        for (var i = 0; i < inputs.length; i++) {
            form.removeChild(inputs[i]);
        }
    }

    // formに配列を追加 foreachで回す
    checked.forEach(function(value) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'checked[]';
        input.value = value;
        form.appendChild(input);
    });

    form.submit();

    return checked;
}
</script>
<div class="container">
	<div class="panel panel-default">
		<div class="panel-heading">受講情報　生徒リスト</div>
		<div class="panel-body">
			<div class="panel-body">
				{{ Form::model($student_search, ['route' => 'juko_info.index', 'method' => 'GET', 'class' => 'form-horizontal']) }}
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
									<div class="form-group ">
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
									<div class="form-group ">
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
									<div class="form-group ">
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
									<div class="form-group ">
										<div class="col-xs-1 text-left">
											{{ Form::label('phone', '電話番号', ['class' => 'control-label']) }}
										</div>
										<div class="col-xs-2 mb-3">
											{{ Form::number('phone', $student_search['phone'] ?? null, ['placeholder' => '電話番号', 'class' => 'form-control form-name']) }}
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
												{{Form::checkbox('fatherless_flg', '1',$student_search['fatherless_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'fatherless_flg'])}}母子家庭
											</label>
										</div>
										<div class="col-xs-2 mb-1">
											<label>
												{{Form::checkbox('temporary_flg', '1', $student_search['temporary_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'temporary_flg'])}}仮入塾
											</label>
										</div>
										<div class="col-xs-2 mb-1">
											<label>
												{{Form::checkbox('rest_flg', '1', $student_search['rest_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'rest_flg'])}}休塾者
											</label>
										</div>
										<div class="col-xs-2 mb-1">
											<label>
												{{Form::checkbox('graduation_flg', '1', $student_search['graduation_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'graduation_flg'])}}卒塾者
											</label>
										</div>
										<div class="col-xs-2 mb-1">
											<label>
												{{Form::checkbox('withdrawal_flg', '1', $student_search['withdrawal_flg'] ?? NULL, ['class'=>'custom-control-input','id'=>'withdrawal_flg'])}}退塾者
											</label>
										</div>

									</div>
									<div class="form-group ">
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
									<div class="form-group ">
										<div class="col-xs-1 text-left">
											{{ Form::label('product_select', '商品名', ['class' => 'control-label']) }}
										</div>
										<div class="col-xs-5 mb-5">
											{{ Form::select('product_select',$products_select_list,$student_search['product_select'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
										</div>
										<div class="col-xs-1 text-left">
											{{ Form::label('discount_select', '割引', ['class' => 'control-label']) }}
										</div>
										<div class="col-xs-5 mb-5">
											{{ Form::select('discount_select',$discounts_select_list,$student_search['discount_select'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
										</div>
									</div>
									<div class="form-group ">
										<div class="col-xs-1 text-left">
											{{ Form::label('juku_start_date', '入塾日', ['class' => 'control-label']) }}
										</div>
										<div class="col-xs-2 mb-3">
											{{Form::date('juku_start_date',null, ['class' => 'form-control','id' => 'juku_start_date'])}}
										</div>
										<div class="col-xs-1 text-center">
											{{ Form::label('wave', '～', ['class' => 'control-label']) }}
										</div>
										<div class="col-xs-2 mb-3">
											{{Form::date('juku_end_date', null, ['class' => 'form-control','id' => 'juku_start_date'])}}
										</div>
										<div class="col-xs-1 text-left">
											{{ Form::label('juku_start_date', '卒塾日', ['class' => 'control-label']) }}
										</div>
										<div class="col-xs-2 mb-3">
											{{Form::date('juku_graduation_start_date', null, ['class' => 'form-control','id' => 'juku_start_date'])}}
										</div>
										<div class="col-xs-1 text-center">
											{{ Form::label('wave', '～', ['class' => 'control-label']) }}
										</div>
										<div class="col-xs-2 mb-3">
											{{Form::date('juku_graduation_end_date', null, ['class' => 'form-control','id' => 'juku_start_date'])}}
										</div>
									</div>
									<div class="form-group ">
										<div class="col-xs-1 text-left">
											{{ Form::label('juku_start_date', '復塾日', ['class' => 'control-label']) }}
										</div>
										<div class="col-xs-2 mb-3">
											{{Form::date('juku_return_start_date', null, ['class' => 'form-control','id' => 'juku_start_date'])}}
										</div>
										<div class="col-xs-1 text-center">
											{{ Form::label('wave', '～', ['class' => 'control-label']) }}
										</div>
										<div class="col-xs-2 mb-3">
											{{Form::date('juku_return_end_date', null, ['class' => 'form-control','id' => 'juku_start_date'])}}
										</div>
									</div>
									<div class="form-group ">
										<div class="col-xs-1 text-left">
											{{ Form::label('juku_withdrawal_date', '退塾日', ['class' => 'control-label']) }}
										</div>
										<div class="col-xs-2 mb-3">
											{{Form::date('juku_withdrawal_start_date',null, ['class' => 'form-control','id' => 'juku_start_date'])}}
										</div>
										<div class="col-xs-1 text-center">
											{{ Form::label('wave', '～', ['class' => 'control-label']) }}
										</div>
										<div class="col-xs-2 mb-3">
											{{Form::date('juku_withdrawal_end_date',null, ['class' => 'form-control','id' => 'juku_start_date'])}}
										</div>
										<div class="col-xs-1 text-left">
											{{ Form::label('juku_rest_date', '休塾日', ['class' => 'control-label']) }}
										</div>
										<div class="col-xs-2 mb-3">
											{{Form::date('juku_rest_start_date', null, ['class' => 'form-control','id' => 'juku_start_date'])}}
										</div>
										<div class="col-xs-1 text-center">
											{{ Form::label('wave', '～', ['class' => 'control-label']) }}
										</div>
										<div class="col-xs-2 mb-3">
											{{Form::date('juku_rest_end_date', null, ['class' => 'form-control','id' => 'juku_start_date'])}}
										</div>
									</div>

									<div class="form-group">
										<div class="">
											<div class="text-center">
												{{ Form::submit('検索', ['name' => 'search', 'class' => 'btn btn-primary']) }}
												{{ Form::reset('リセット', ['class' => 'btn btn-primary reset']) }}
												{{-- <button class="btn">クリア</button> --}}
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			{{ Form::close() }}

			<div>{{ $student->total() }} 件中 {{ $student->firstItem() }} - {{ $student->lastItem() }} 件を表示</div>
			<br>
			<br>
			<a class="btn btn-primary check_all">
				一括選択
			</a>
			<div class="table-responsive">
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th>選択</th>
							<th>管理No</th>
							<th>生徒No</th>
							<th>生徒名</th>
							<th>学年</th>
							<th>校舎名</th>
							<th>学校名</th>
							<th>商品名</th>
							<th>割引</th>
							<th>情報登録</th>
							<th>一括削除</th>
						</tr>
					</thead>
					<tbody>
						@foreach($student as $item)
						<tr>
							<td>
								<input type="checkbox" name="check[]" value="{{ $item->student_no }}">
							</td>
							<td>{{ $item->id}} </td>
							<td>{{ sprintf('%08d', $item->student_no)}} </td>
							<td>{{ $item->surname}} {{ $item->name}}</td>
							<td>{{ config('const.school_year')[$item->grade] ?? ""}}</td>
							<td>{{ $item->schoolbuilding->name ?? ""}} </td>
							<td>{{ $item->school->name ?? ""}} </td>
							<td>{{ $item->juko_info->product->name ?? ""}}</td>
							<td>{{ $item->discount->name ?? ""}} </td>
							<td>
								<a href="{{ url('/shinzemi/juko_info/' . sprintf('%08d', $item->student_no) . '/edit') }}" title="Edit" class="btn btn-primary btn-xs">受講情報登録</a>
							</td>
							<td>
								<form method="GET" action="{{route('juko_info.product_delete',$item->student_no)}}" class="form-horizontal" style="display:inline;">
									{{ csrf_field() }}
									{{ method_field("DELETE") }}
									<button type="submit" class="btn btn-danger btn-xs" title="Delete User" onclick="return confirm('登録している受講情報を全て削除します。\n本当によろしいですか？')">
										削除
									</button>
								</form>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				<div class="pagination-wrapper"> {{ $student->appends(request()->input())->links() }} </div>
			</div>
		</div>
	</div>

	<div style="margin-bottom: 64px;">
		<form method="post" action="{{ route('juko_info.bulk_store')}}" id="bulk_store" name="bulk_store">
			@csrf

			<div style="display: flex;margin-top: 16px;">

				<div>
					<button type="button" class="btn btn-primary" style="margin-left: 16px;margin-top:16px; " onclick="getChecked()">
						一括登録
					</button>
				</div>

				<div style="margin-left: 16px;">
					<div>
						商品：
					</div>
					<select name="selected_product" id="" class="form-control select_search_2">
						<option value="0">選択してください</option>
						@foreach ($products_select_list as $key => $value)
						<option value="{{ $key }}">{{ $value }}</option>
						@endforeach
					</select>
				</div>

			</div>

		</form>

		<form method="post" action="{{ route('juko_info.bulk_delete')}}" id="bulk_delete" name="bulk_delete">
			@csrf

			<div style="display: flex;margin-top: 16px;">

				<div>
					<button type="button" class="btn btn-danger" style="margin-left: 16px;margin-top:16px; " onclick="getChecked_for_delete()">
						一括削除
					</button>
				</div>

				<div style="margin-left: 16px;">
					<div>
						商品：
					</div>
					<select name="selected_product_for_delete" id="" class="form-control select_search_2">
						<option value="0">選択してください</option>
						@foreach ($products_select_list as $key => $value)
						<option value="{{ $key }}">{{ $value }}</option>
						@endforeach
					</select>
				</div>
			</div>
		</form>
	</div>
</div>

@endsection
