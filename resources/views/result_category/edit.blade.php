@extends("layouts.app")
@section("content")
<script src="{{ asset('/js/result_category_edit.js') }}"></script>
@push('css')
<link href="{{ asset('css/result_category.css') }}" rel="stylesheet">
@endpush

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">成績カテゴリー編集</div>
				<div class="panel-body">
					<a href="{{ url('/shinzemi/result_category') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
					<br />
					<br />
					{{ Form::model($result_category, array('route' => array('result_category.update', $result_category->id), 'method' => 'PUT', 'class' => 'form-horizontal')) }}
					<div class="form-group">
						<label for="name" class="col-md-2">成績カテゴリー名: </label>
						<div class="col-md-6">
							{{Form::text('result_category_name', $result_category->result_category_name, ['class' => 'form-control','id' => 'result_category_name','placeholder' => '成績カテゴリーネーム'])}}
						</div>
						<div class="custom-control custom-switch">
							{{Form::hidden('average_point_flg', '0') }}
							{{Form::checkbox('average_point_flg', '1',$result_category->average_point_flg ?? "", ['class'=>'custom-control-input','id'=>'average_point_flg'])}}
							{{Form::label('average_point_flg','学校平均表示')}}
						</div>
						<div class="custom-control custom-switch">
							{{Form::hidden('elementary_school_student_display_flg', '0') }}
							{{Form::checkbox('elementary_school_student_display_flg', '1',$result_category->elementary_school_student_display_flg ?? "", ['class'=>'custom-control-input','id'=>'elementary_school_student_display_flg'])}}
							{{Form::label('elementary_school_student_display_flg','小学生表示')}}

							{{Form::hidden('junior_high_school_student_display_flg', '0') }}
							{{Form::checkbox('junior_high_school_student_display_flg', '1',$result_category->junior_high_school_student_display_flg ?? "", ['class'=>'custom-control-input','id'=>'junior_high_school_student_display_flg'])}}
							{{Form::label('junior_high_school_student_display_flg','中学生表示')}}
						</div>
					</div>

					<table class="table table-striped table-hover table-bordered table-condensed table_sticky">
						<thead>
							<tr>
								<th class="text-center">No</th>
								<th>実施回</th>
							</tr>
						</thead>
						<tbody id="implementation_table_tbody">
							@if(count($implementations)=== 0)
							<tr>
								<td class="col-md-1 text-center ">
									{{Form::label('implementation_No','1',['class' => 'implementation_No'])}}
								</td>
								<td class="col-md-1">
									{{Form::text('implementation_name[]',false, ['class' => 'form-control implementation_name', 'placeholder' => ''])}}
								</td>
							</tr>
							@endif
							@foreach($implementations as $implementation)
							<tr>
								<td class="col-md-1 text-center">
									{{Form::label('implementation_No','1',['class' => 'implementation_No'])}}
									{{Form::hidden('hidden_implementation_id[]', $implementation->id, ['class' => 'hidden_implementation_id'])}}
								</td>
								<td class="col-md-1">
									{{Form::text('implementation_name[]',$implementation->implementation_name ??"", ['class' => 'form-control implementation_name', 'placeholder' => ''])}}
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					<div class="form-group">
						<div class="col-md-2">
							{{Form::button('追加', ['name' => 'add','class'=>'btn btn-success add-input-implementation'])}}
						</div>
						<div class="col-md-6">
							{{Form::button('削除', ['class'=>'btn btn-danger implementation-delete'])}}
						</div>
					</div>
					<br>
					<div class="form-group">
						<div class="col-md-4">
							{{Form::submit('更新', ['name' => 'registration','class'=>'btn btn-primary btn-lg'])}}
						</div>
					</div>
					{{ Form::close() }}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
