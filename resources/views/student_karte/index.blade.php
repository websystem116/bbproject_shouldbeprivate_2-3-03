@extends("layouts.app")
@section("content")
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
@endpush
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
        window.location.href = "/shinzemi/student_karte"; //URLリセットする
    });
});
</script>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">成績情報登録</div>
				<div class="panel-body">
					{{ Form::model($student_search, ['route' => 'student_karte.index', 'method' => 'GET', 'class' => 'form-horizontal']) }}
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
												{{ Form::number('student_no_start',$student_search['student_no_start'] ?? null, ['placeholder' => '生徒No', 'class' => 'form-control form-name']) }}
											</div>
											<div class="col-xs-1 text-center">
												{{ Form::label('wave', '～', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::number('student_no_end', $student_search['student_no_end'] ?? null, ['placeholder' => '生徒No', 'class' => 'form-control form-name']) }}
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
											<div class="col-xs-1 text-left">
												{{ Form::label('discount_select', '割引', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-5 mb-5">
												{{ Form::select('discount_select',$discounts_select_list,$student_search['discount_select'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
											</div>
										</div>
										<div class="form-group row">
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
										<div class="form-group row">
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
										<div class="form-group row">
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
											<div class="row">
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
				<div class="panel-body">

					<div>{{ $student->total() }} 件中 {{ $student->firstItem() }} - {{ $student->lastItem() }} 件を表示</div>

					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th>管理No</th>
									<th>生徒No</th>
									<th>生徒氏名</th>
									<th>学年</th>
									<th>学校名</th>
									<th>学校区分</th>
									<th>校舎</th>
									<th>登録</th>
								</tr>
							</thead>
							<tbody>
								@foreach($student as $item)
								<tr>
									<td>{{ $item->id}} </td>
									<td>{{ $item->student_no}} </td>
									<td>{{ $item->surname}} {{ $item->name}}</td>
									<td>{{ config('const.school_year')[$item->grade ?? ""] }}</td>
									<td>{{ $item->school->name ?? ""}} </td>
									@if(!empty($item->school->school_classification))
									<td>{{config('const.school_classification')[$item->school->school_classification]}} </td>
									@else
									<td> </td>
									@endif
									<td>{{ $item->schoolbuilding->name ?? ""}} </td>
									<td>
										<a href="{{ url('/shinzemi/student_karte/' . $item->student_no . '/edit') }}" title="Edit"><button class="btn btn-primary btn-xs">成績情報登録</button></a>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
						<div class="pagination-wrapper">{{ $student->appends(request()->input())->links() }} </div>
						{{-- <div class="pagination-wrapper"> {!! $student->appends(["search" => Request::get("grade_start")])->render() !!} </div> --}}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
