@extends("layouts.app")
@section("content")
<script src="{{ asset('/js/average_point.js') }}"></script>
@push('css')
<link href="{{ asset('css/average_point.css') }}" rel="stylesheet">
@endpush

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">成績情報入力フォーム</div>
				<div class="panel-body">
					<a href="{{ url()->previous() }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
					<br />
					<br />
					<div class="container">
						<div class="row">
							<div class="col-md-2">
								<label>生徒No：{{ $target_student->student_no}}</label>
								{{Form::hidden('student_no', $target_student->student_no,['id' => 'student_no'])}}
							</div>
							<div class="col-md-2">
								<label>生徒氏名：{{ $target_student->surname}} {{ $target_student->name}}</label>
							</div>
							<div class="col-md-2">
								<label>学年：{{config('const.school_year')[$target_student->grade]}}</label>
							</div>
							<div class="col-md-2">
								<label>校舎名：{{ $target_student->schoolbuilding->name ?? ""}}</label>
							</div>
							<div class="col-md-2">
								<label>学校名：{{ $target_student->school->name ?? ""}}</label>
							</div>
						</div>
					</div>
					{{ Form::model($search, ['url' => 'average_point/'.$target_student->student_no.'/edit', 'method' => 'GET', 'class' => 'form-horizontal']) }}
					<div class="col-md-2 mb-3">
						{{Form::label('junior_high_school_lavel','学年')}}
					</div>
					<div class="col-md-3">
						{{ Form::select('school_year', config('const.school_year'),$search['school_year'] ?? $target_student->grade,['placeholder' => '選択してください', 'class' => 'form-control school_year']) }}
					</div>
					<div class="col-md-2 mb-3">
						{{Form::label('year_lavel','年度')}}
					</div>
					<div class="col-md-3">
						{{ Form::text('year', $select_year,['readonly','placeholder' => '', 'class' => 'form-control form-name']) }}

					</div>
					<div class="col-md-2 mb-3">
						{{ Form::submit('表示', ['name' => 'search', 'class' => 'btn btn-primary']) }}
					</div>
				</div>
				{{ Form::close() }}
				<form method="POST" action="{{route('average_point.store',['student_id' => $target_student->id ,
					'student_no' => $target_student->student_no,
					'school_id'=> $target_student->school->id,
					'grade'=>$target_student->grade,
					'school_year'=>$search['school_year'] ?? $target_student->grade,
					'year'=>$select_year,
					])}}" class="form-horizontal" autocomplete="off">
					{{ csrf_field() }}
					<tbody id="sales_table_tbody">
						<table class="table table-striped table-hover table-bordered table-condensed table_sticky">
						@foreach($target_resultcategory as $resultkey => $value_resultcategory)
							<tr>
								<th>
									<a href="/shinzemi/result_category/{{$value_resultcategory->id}}/edit" title="resultcategory">
										{{$value_resultcategory->result_category_name}}
									<a>
								</th>
								@foreach($value_resultcategory->subjects as $subject)
									@if($subject->subject_name=="2科/3科平均")
										@if($select_grade>=10)
											<th>3科</th>
										@elseif($select_grade<=9) 
											<th>2科</th>
										@else
										<th>{{$subject->subject_name}}</th>
										@endif
									@elseif($subject->subject_name=="3科/5科平均")
										@if($select_grade>=10)
											<th>5科</th>
										@elseif($select_grade<=9)
											<th>3科</th>
										@else
											<th>{{$subject->subject_name}}</th>
										@endif
									@elseif($subject->subject_name=="数学")
										@if($select_grade>=10)
											<th>数学</th>
										@elseif($select_grade<=9)
											<th>算数</th>
										@else
											<th>{{$subject->subject_name}}</th>
										@endif
									@else
										<th>{{$subject->subject_name}}</th>
									@endif
								@endforeach
							</tr>
							@foreach($value_resultcategory->implementations as $implementationkey =>$implementation)
								<tr>
									@if($select_grade<=9)
										@if($implementation->implementation_name=="1学期評定")
											<td>1学期</td>
										@elseif($implementation->implementation_name=="2学期評定")
											<td>2学期</td>
										@elseif($implementation->implementation_name=="3学期評定")
											<td>3学期</td>
										@elseif($implementation->implementation_name=="年間平均評定")
											{{-- 小学生で年間平均評定は表示しない --}}
										@else
											<td>{{$implementation->implementation_name}}</td>
										@endif
									@else
										<td>{{$implementation->implementation_name}}</td>
									@endif
									@foreach($value_resultcategory->subjects as $subjectkey => $subject)
										@if($value_resultcategory->result_category_name=="塾内模試"||$value_resultcategory->result_category_name=="藤井模試"||$value_resultcategory->result_category_name=="五ツ木模試"||$value_resultcategory->result_category_name=="Vもし")
											<td>
												{{Form::text('point['.$value_resultcategory->id.']['.$implementation->implementation_no.']['.$subject->subject_no.'][]',	
												$target_student_point[$value_resultcategory->id][$implementation->implementation_no][$subject->subject_no] ?? "", 	
												['class' =>'textbox','id' => 'point','placeholder' => '　'])}}	
											</td>
										@else
											@if($select_grade<=9)
												@if($implementation->implementation_name=="年間平均評定")
													{{-- 小学生で年間平均評定は表示しない --}}
												@else
												<td>
													{{Form::text('point['.$value_resultcategory->id.']['.$implementation->implementation_no.']['.$subject->subject_no.'][]',
													$target_student_point[$value_resultcategory->id][$implementation->implementation_no][$subject->subject_no] ?? "",
													['class' =>'textbox','id' => 'point','placeholder' => '　点'])}}
												</td>
												@endif
											@else
												<td>
													{{Form::text('point['.$value_resultcategory->id.']['.$implementation->implementation_no.']['.$subject->subject_no.'][]',
													$target_student_point[$value_resultcategory->id][$implementation->implementation_no][$subject->subject_no] ?? "",
													['class' =>'textbox','id' => 'point','placeholder' => '　点'])}}
												</td>
											@endif
										@endif
									@endforeach
								</tr>
									@if($value_resultcategory->average_point_flg == 1)
										<tr>
											<td>{{"平均点"}}</td>
											@foreach($value_resultcategory->subjects as $subjectkey => $subject)
												<td>{{Form::text('average_point['.$value_resultcategory->id.']['.$implementation->implementation_no.']['.$subject->subject_no.'][]', $target_average_point[$value_resultcategory->id][$implementation->implementation_no][$subject->subject_no] ?? "", ['class' =>'textbox','id' => 'average_point','placeholder' => '　点'])}}</td>
											@endforeach
										</tr>
									@endif
								@endforeach
							@endforeach
						</table>
					</tbody>
				</div>
			</form>
			<br>
			<br>
		</div>
	</div>
</div>
@endsection
