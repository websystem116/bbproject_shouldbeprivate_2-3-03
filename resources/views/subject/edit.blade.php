@extends("layouts.app")
@section("content")
<script src="{{ asset('/js/subject.js') }}"></script>
@push('css')
<link href="{{ asset('css/subject.css') }}" rel="stylesheet">
@endpush

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">成績教科登録</div>
				<div class="panel-body">
					<a href="{{ url('/shinzemi/result_category') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
					<br />
					<br />
					{{ Form::model($result_category, array('route' => array('subject.update', $result_category->id), 'method' => 'PUT', 'class' => 'form-horizontal')) }}
					<div class="form-group">
						<label for="name" class="col-md-2">成績カテゴリー名: </label>
						<div class="col-md-6">
							{{Form::label('result_category_name_label', $result_category->result_category_name, ['class' => 'form-control'])}}
						</div>

					</div>
					※「数学」で登録すると小学生では「算数」　中学生では「数学」の表記になります。<br>
					※「2科/3科平均」「3科/5科平均」で登録すると小学生では「2科」「3科」　中学生では「3科」「5科」の表記になります。<br>
					<table class="table table-striped table-hover table-bordered table-condensed table_sticky">
						<thead>
							<tr>
								<th class="text-center">No</th>
								<th>教科</th>
							</tr>
						</thead>
						<tbody id="subject_table_tbody">
							@if(count($subjects)=== 0)
							<tr>
								<td class="col-md-1 text-center ">
									{{Form::label('subject_No','1',['class' => 'subject_No'])}}
								</td>
								<td class="col-md-1">
									{{Form::text('subject_name[]',false, ['class' => 'form-control subject_name', 'placeholder' => ''])}}

								</td>
							</tr>
							@endif
							@foreach($subjects as $subject)
							<tr>
								<td class="col-md-1 text-center">
									{{Form::label('subject_No','1',['class' => 'subject_No'])}}
									{{Form::hidden('hidden_subject_id[]', $subject->id, ['class' => 'hidden_subject_id'])}}
								</td>
								<td class="col-md-1">
									{{Form::text('subject_name[]',$subject->subject_name ??"", ['class' => 'form-control subject_name', 'placeholder' => ''])}}
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					<div class="form-group">
						<div class="col-md-2">
							{{Form::button('追加', ['name' => 'add','class'=>'btn btn-success add-input-subject'])}}
						</div>
						<div class="col-md-6">
							{{Form::button('削除', ['class'=>'btn btn-danger subject-delete'])}}
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
