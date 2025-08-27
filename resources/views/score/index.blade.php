@extends("layouts.app")
@section("content")
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
<script src="{{ asset('/js/score.js') }}"></script>
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
        window.location.href = "/shinzemi/score"; //URLリセットする
    });
});
</script>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">試験別成績一覧表出力</div>
				<div class="panel-body">
					{{ Form::model($student_search, ['route' => 'score.index', 'method' => 'GET', 'class' => 'form-horizontal']) }}
					<div class="panel-group" id="sampleAccordion">
						<h3 class="panel-title">
							出力条件
						</h3>
					</div>
					<div class="panel-body">
						<div class="form-group row">
							<div class="col-xs-1 text-left">
								{{ Form::label('resultcategory', '成績区分', ['class' => 'control-label']) }}
							</div>
							<div class="col-xs-2 mb-3">
								{{ Form::select('result_category_id',$resultcategorys_select_list,$student_search['result_category_id'] ?? null,['placeholder' => '選択してください','class' => 'resultcategory_select form-control select_search3']) }}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-xs-1 text-left">
								{{ Form::label('implementation', '実施回', ['class' => 'control-label']) }}
							</div>
							<div class="col-xs-2 mb-3">
								{{ Form::select('implementation_id',$implementations_select_list,$student_search['implementation_id'] ?? null,['placeholder' => '選択してください','class' => 'implementation_select form-control select_search3']) }}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-xs-1">
								{{ Form::label('school_building', '年度', ['class' => 'control-label']) }}
							</div>
							<div class="col-xs-2 mb-3">
								{{Form::text('year', null, ['style'=>'background-color:#f3d7dc','class' => 'form-control', 'id' => 'year','maxlength'=>4, 'placeholder' => $year])}}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-xs-1 text-left">
								{{Form::label('grade','学年')}}
							</div>
							<div class="col-md-3">
								{{ Form::select('grade', config('const.school_year'),$student_search['grade'] ?? null,['placeholder' => '選択してください', 'class' => 'form-control select_search_grade']) }}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-xs-1">
								{{Form::label('school','学校')}}
							</div>
							<div class="col-md-5">
								{{ Form::select('school_id', $schools_select_list,$student_search['school_id'],['placeholder' => '選択してください', 'class' => 'form-control select_search3']) }}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-xs-1 text-left">
								{{ Form::label('school_building', '校舎名', ['class' => 'control-label']) }}
							</div>
							<div class="col-xs-2 mb-3">
								{{ Form::select('school_building_id',$schooolbuildings_select_list,$student_search['school_building_id'] ?? null,['placeholder' => '選択してください','class' => 'form-control select_search3']) }}
							</div>
						</div>
					</div>
					<br>
					<div class="panel-group" id="sampleAccordion">
						<h3 class="panel-title">
							除外条件
						</h3>
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
						<div class="col-xs-1">
							{{ Form::submit('出力', ['name' => 'output','class' => 'btn btn-primary']) }}
						</div>
						<div class="col-xs-1">
							{{ Form::reset('リセット', ['class' => 'btn btn-primary reset']) }}
						</div>
					</div>
				</div>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>
@endsection
