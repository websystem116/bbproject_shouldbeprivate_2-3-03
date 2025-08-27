@extends("layouts.app")
@section("content")
@push('css')
<link href="{{ asset('css/bootstrap-datepicker3.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/bootstrap-datepicker.ja.min.js') }}"></script>
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
        window.location.href = "/shinzemi/before_juku_sales"; //URLリセットする
    });
});
$(function() {
			// monthPick
			var currentTime = new Date();
			var year = currentTime.getFullYear();
			var year2 = parseInt(year) + 10;

			$(".monthPick").datepicker({
			autoclose: true,
			language: 'ja',
			clearBtn: true,
			format: "yyyymm",
			minViewMode: 1,
			maxViewMode: 2
			});
});
</script>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">入塾前売上一覧</div>
				<div class="panel-body">
					{{ Form::model($student_search, ['route' => 'before_juku_sales.index', 'method' => 'GET', 'class' => 'form-horizontal']) }}
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
												{{ Form::number('id_start',$student_search['id_start'] ?? null, ['placeholder' => 'No', 'class' => 'form-control form-name']) }}
											</div>
											<div class="col-xs-1 text-center">
												{{ Form::label('wave', '～', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::number('id_end', $student_search['id_end'] ?? null, ['placeholder' => 'No', 'class' => 'form-control form-name']) }}
											</div>
										</div>
										<div class="form-group row">
											<div class="col-xs-1 text-left">
												{{ Form::label('number', '生徒No', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::number('student_no_start',$student_search['student_no_start'] ?? null, ['placeholder' => 'No', 'class' => 'form-control form-name']) }}
											</div>
											<div class="col-xs-1 text-center">
												{{ Form::label('wave', '～', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::number('student_no_end', $student_search['student_no_end'] ?? null, ['placeholder' => 'No', 'class' => 'form-control form-name']) }}
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
												{{ Form::label('school_name', '学校名', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::select('school_id',$schools_select_list,$student_search['school_id'] ?? null, ['placeholder' => '選択してください', 'class' => 'form-control form-name select_search2']) }}
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
										<div class="form-group row">
											<div class="col-xs-1 text-left">
												{{ Form::label('payment_date', '入金日', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{Form::date('payment_start_date',null, ['class' => 'form-control','id' => 'payment_start_date'])}}
											</div>
											<div class="col-xs-1 text-center">
												{{ Form::label('wave', '～', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{Form::date('payment_end_date', null, ['class' => 'form-control','id' => 'payment_end_date'])}}
											</div>
											<div class="col-xs-1 text-left">
												{{ Form::label('sales_date', '売上年月', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::text('sales_date',$student_search['sales_date'] ?? null, ['placeholder' => '年月', 'class' => 'form-control form-name monthPick','autocomplete=off']) }}
											</div>
										</div>
										<div class="form-group">
											<div class="text-center">
												{{ Form::submit('検索', ['name' => 'search', 'class' => 'btn btn-primary']) }}
												<span style="margin-left: 16px;"></span>
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
				{{ Form::close() }}



				<div class="panel-body">

					<div>{{ $before_student->total() }} 件中 {{ $before_student->firstItem() }} - {{ $before_student->lastItem() }} 件を表示</div>

					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th>管理No</th>
									<th>生徒No</th>
									<th>氏名</th>
									<th>学年</th>
									<th>校舎名</th>
									<th>学校名</th>
									<th>売上年月</th>
									<th>入金日</th>
									<th>商品名</th>
								</tr>
							</thead>
							<tbody>
								@foreach($before_student as $before_student_info)
								<tr>
									<td>{{ $before_student_info->id}} </td>
									<td>{{ $before_student_info->before_student_no ?? ""}} </td>
									<td>{{ $before_student_info->surname}} {{ $before_student_info->name}}</td>
									<td>{{ config('const.school_year')[$before_student_info->grade] ?? ""}}</td>
									<td>{{ $before_student_info->schoolbuilding->name ?? ""}}</td>
									<td>{{ $before_student_info->school->name ?? ""}}</td>
									<td>
										{{ $before_student_info->before_juku_sale->sales_date ?? ""}}
									</td>
									<td>
										@isset( $before_student_info->before_juku_sale->payment_date)
										{{ $before_student_info->before_juku_sale->payment_date->format('Y年m月d日')}}
										@endisset
									</td>
									<td>{{ $before_student_info->before_juku_sale->product->name ?? ""}}</td>
									<td>
										<a href="{{ url('/shinzemi/before_juku_sales/' . $before_student_info->before_student_no . '/edit') }}" title="Edit bank"><button class="btn btn-primary btn-xs">売上登録</button></a>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
						{{-- <div class="pagination-wrapper"> {!! $before_student->appends(["search" => Request::get("grade_start")])->render() !!} </div> --}}
						<div class="pagination-wrapper"> {{ $before_student->appends(request()->input())->links() }} </div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection
