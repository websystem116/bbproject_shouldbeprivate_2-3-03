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
        window.location.href = "/shinzemi/average_point"; //URLリセットする
    });
});
</script>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">成績情報登録</div>
				<div class="panel-body">
					{{ Form::model($student_search, ['route' => 'average_point.index', 'method' => 'GET', 'class' => 'form-horizontal']) }}
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
												{{ Form::label('school_building', '校舎名', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-5 mb-3">
												{{ Form::select('school_building_id',$schooolbuildings_select_list,$student_search['school_building_id'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
											</div>
											<div class="col-xs-1 text-center">
												{{ Form::label('school_name', '学校名', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-5 mb-3">
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

										<br>
										<br>

										<div class="form-group row">
											<div class="col-xs-3 text-left">
												{{ Form::label('not_input_students', '未入力者検索', ['class' => 'control-label']) }}
											</div>
										</div>

										<div class="form-group row">
											<div class="col-xs-1 text-left">
												{{ Form::label('year', '年度', ['class' => 'control-label']) }}
											</div>

											<div class="col-md-3">
												{{ Form::text('year', session('selected_year', $now_year), ['placeholder' => '', 'class' => 'form-control form-name']) }}
											</div>
										</div>

										<div class="form-group row">
											<div class="col-xs-3 text-left">
												{{ Form::label('result_category_list', '成績カテゴリー', ['class' => 'control-label']) }}
											</div>
										</div>

										{{-- <div class="form-group row">
											<div class="col-xs-1 text-left">
												{{ Form::label('not_input_grade', '学年', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::select('not_input_grade_start', config('const.school_year'),$student_search['not_input']['grade_start'] ?? null,['placeholder' => '選択してください', 'class' => 'form-control select_search2']) }}
											</div>
											<div class="col-xs-1 text-center">
												{{ Form::label('wave', '～', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::select('not_input_grade_end', config('const.school_year'), $student_search['not_input']['grade_end'] ?? null,['placeholder' => '選択してください', 'class' => 'form-control select_search2']) }}
											</div>
										</div>

										<div class="form-group row">
											<div class="col-xs-1 text-left">
												{{ Form::label('not_input_school_building', '校舎名', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-3">
												{{ Form::select('not_input_school_building_id',$schooolbuildings_select_list,$student_search['not_input']['school_building_id'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
											</div>
										</div> --}}

										<div class="form-group row">
											<div class="col-xs-1 text left">
												{{ Form::label('regular_exam', '学校成績', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-1">
												<label>
													{{Form::checkbox('implementation_id_1[]', '1', $student_search['implementation_id_1']['1_1'] ?? NULL, ['class'=>'custom-control-input','id'=>'implementation_id_1_1'])}}1学期中間
												</label>
											</div>
											<div class="col-xs-2 mb-1">
												<label>
													{{Form::checkbox('implementation_id_1[]', '2', $student_search['implementation_id_1']['1_2'] ?? NULL, ['class'=>'custom-control-input','id'=>'implementation_id_1_2'])}}1学期期末
												</label>
											</div>
											<div class="col-xs-2 mb-1">
												<label>
													{{Form::checkbox('implementation_id_1[]', '3', $student_search['implementation_id_1']['1_3'] ?? NULL, ['class'=>'custom-control-input','id'=>'implementation_id_1_3'])}}2学期中間
												</label>
											</div>
											<div class="col-xs-2 mb-1">
												<label>
													{{Form::checkbox('implementation_id_1[]', '4', $student_search['implementation_id_1']['1_4'] ?? NULL, ['class'=>'custom-control-input','id'=>'implementation_id_1_4'])}}2学期期末
												</label>
											</div>
											<div class="col-xs-2 mb-1">
												<label>
													{{Form::checkbox('implementation_id_1[]', '5', $student_search['implementation_id_1']['1_5'] ?? NULL, ['class'=>'custom-control-input','id'=>'implementation_id_1_5'])}}学年末
												</label>
											</div>
										</div>

										<div class="form-group row">
											<div class="col-xs-1 text left">
												{{ Form::label('mock_exam', '塾内模試', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-1">
												<label>
													{{Form::checkbox('implementation_id_3[]', '1', $student_search['implementation_id_3']['3_1'] ?? NULL, ['class'=>'custom-control-input','id'=>'implementation_id_3_1'])}}1（4月）
												</label>
											</div>
											<div class="col-xs-2 mb-1">
												<label>
													{{Form::checkbox('implementation_id_3[]', '2', $student_search['implementation_id_3']['3_2'] ?? NULL, ['class'=>'custom-control-input','id'=>'implementation_id_3_2'])}}2（6月）
												</label>
											</div>
											<div class="col-xs-2 mb-1">
												<label>
													{{Form::checkbox('implementation_id_3[]', '3', $student_search['implementation_id_3']['3_3'] ?? NULL, ['class'=>'custom-control-input','id'=>'implementation_id_3_3'])}}3（8月）
												</label>
											</div>
											<div class="col-xs-2 mb-1">
												<label>
													{{Form::checkbox('implementation_id_3[]', '4', $student_search['implementation_id_3']['3_4'] ?? NULL, ['class'=>'custom-control-input','id'=>'implementation_id_3_4'])}}4（11月）
												</label>
											</div>
											<div class="col-xs-2 mb-1">
												<label>
													{{Form::checkbox('implementation_id_3[]', '5', $student_search['implementation_id_3']['3_5'] ?? NULL, ['class'=>'custom-control-input','id'=>'implementation_id_3_5'])}}5（1月）
												</label>
											</div>
										</div>

										<div class="form-group row">
											<div class="col-xs-1 text left">
												{{ Form::label('report_card', '通知表', ['class' => 'control-label']) }}
											</div>
											<div class="col-xs-2 mb-1">
												<label>
													{{Form::checkbox('implementation_id_2[]', '1', $student_search['implementation_id_2']['2_1'] ?? NULL, ['class'=>'custom-control-input','id'=>'implementation_id_2_1'])}}1学期評定
												</label>
											</div>
											<div class="col-xs-2 mb-1">
												<label>
													{{Form::checkbox('implementation_id_2[]', '2', $student_search['implementation_id_2']['2_2'] ?? NULL, ['class'=>'custom-control-input','id'=>'implementation_id_2_2'])}}2学期評定
												</label>
											</div>
											<div class="col-xs-2 mb-1">
												<label>
													{{Form::checkbox('implementation_id_2[]', '3', $student_search['implementation_id_2']['2_3'] ?? NULL, ['class'=>'custom-control-input','id'=>'implementation_id_2_3'])}}3学期評定
												</label>
											</div>
											<div class="col-xs-2 mb-1">
												<label>
													{{Form::checkbox('implementation_id_2[]', '4', $student_search['implementation_id_2']['2_4'] ?? NULL, ['class'=>'custom-control-input','id'=>'implementation_id_2_4'])}}年間平均評定
												</label>
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
									<th>成績カルテ出力</th>
								</tr>
							</thead>
							<tbody>
								@foreach($student as $item)
								<tr>
									<td>{{ $item->id}} </td>
									<td>{{ $item->student_no}} </td>
									<td>{{ $item->surname}} {{ $item->name}}</td>
									<td>{{ config('const.school_year')[$item->grade] ?? null }}</td>
									<td>{{ $item->school->name ?? ""}} </td>
									@if(!empty($item->school->school_classification))
									<td>{{config('const.school_classification')[$item->school->school_classification]}} </td>
									@else
									<td> </td>
									@endif
									<td>{{ $item->schoolbuilding->name ?? ""}} </td>
									@if(empty($student_search['withdrawal_flg']))
									<td>
										<a href="{{ url('/shinzemi/average_point/' . $item->student_no . '/edit') }}" title="Edit"><button class="btn btn-primary btn-xs">成績情報登録</button></a>
									</td>
									@else
									<td>
										<p>退塾者です</p>
									</td>
									@endif
									@if($item->grade<=9) <td>
										<a href="{{ route('average_point.output_elementary_school_student_result', ['id'=>$item->student_no]) }}">
											{{Form::button('小学生出力', ['id' => 'output_elementary_school_student_output','class' => 'btn btn-success', 'onfocus' => 'this.blur();'])}}
										</a>
										</td>
										@endif
										@if($item->grade>=10)
										<td>
											<a href="{{ route('average_point.output_junior_high_school_student_result', ['id'=>$item->student_no]) }}">
												{{Form::button('中学生出力', ['id' => 'output_junior_high_school_student_result','class' => 'btn btn-primary', 'onfocus' => 'this.blur();'])}}
											</a>
										</td>
										@endif
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
