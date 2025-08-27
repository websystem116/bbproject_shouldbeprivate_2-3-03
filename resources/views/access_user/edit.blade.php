@extends("layouts.app")
@section("content")
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
{{-- <script src="{{ asset('/js/access_user.js') }}"></script> --}}
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
<script>
	$(function() {
		$('.select_search').select2({
			language: "ja",
			width: '300px'
		});
		$('.select_search_grade').select2({
			language: "ja",
			width: '100px'
		});
		$('.select_search_school').select2({
			language: "ja",
			width: '200px'
		});
	});
</script>
<style>
	.select2-container--default .select2-selection--single {
		background-color: #f3d7dc; /* 背景色を変更 */
	}
</style>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">入退室生徒情報編集 名前：{{ $student->surname }}{{ $student->name }}</div>
				<div class="panel-body">
					{{-- <a href="{{ url('student') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a> --}}
					<a href="{{ url()->previous() }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
					<br />
					<br />

					@if ($errors->any())
					<ul class="alert alert-danger">
						@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
						@endforeach
					</ul>
					@endif

					{{ Form::model($student, array('route' => array('access_user.update', $student->id), 'method' => 'PUT', 'class' => 'form-horizontal')) }}

					<div class="form-group row">
						<div class="col-md-3 col-form-label">
							{{Form::label('inputName','本人情報')}}
						</div>
					</div>
					<!--塾内情報-->
					<div class="form-group row">
						<div class="col-md-2 mb-2">
							{{Form::label('school_building_id','校舎')}}
						</div>
						<div class="col-md-4">
							{{ Form::select('school_building_id',$schooolbuildings_select_list,null,['placeholder' => '選択してください','class' => 'form-control select_search','style'=>'background-color:#f3d7dc']) }}
						</div>
					</div>
					<!--塾内情報-->
					
					<!--生徒氏名-->
					<div class="form-group row">
						<div class="col-md-2 mb-3">
							{{Form::label('inputName','生徒氏名')}}
						</div>
						<div class="col-md-5">
							{{Form::text('surname', null, ['class' => 'form-control','id' => 'surname','style'=>'background-color:#f3d7dc','placeholder' => '姓'])}}
						</div>
						<div class="col-md-5">
							{{Form::text('name', null, ['class' => 'form-control','id' => 'name','style'=>'background-color:#f3d7dc','placeholder' => '名'])}}
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-2 mb-3">
						</div>
						<div class="col-md-5">
							{{Form::text('surname_kana', null, ['class' => 'form-control hira_change','id' => 'surname_kana','placeholder' => 'セイ'])}}
						</div>
						<div class="col-md-5">
							{{Form::text('name_kana', null, ['class' => 'form-control hira_change','id' => 'name_kana','placeholder' => 'メイ'])}}
						</div>
					</div>


					<div class="form-group row">
						<div class="col-md-2">
							{{Form::label('email_access','メールアドレス1')}}
						</div>
						<div class="col-md-10">
						{{Form::email('email_access', null, ['autocomplete'=>'off','class' => 'form-control','id' => 'email','placeholder' => 'メールアドレス（自動送信用）','style'=>'background-color:#f3d7dc'])}}
					</div>
					</div>
					<div class="form-group row">
						<div class="col-md-2">
							{{Form::label('email_access2','メールアドレス2')}}
						</div>
						<div class="col-md-10">
							{{Form::email('email_access2', null, ['autocomplete'=>'off','class' => 'form-control','id' => 'email','placeholder' => 'メールアドレス（自動送信用）','style'=>'background-color:#f3d7dc'])}}
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-2">
							QRコード
						</div>
						<div class="col-md-10">
							{!! $qrCode !!}
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-4">
							{{ Form::submit('更新', array('class' => 'btn btn-primary confirm')) }}
						</div>
					</div>

					{{ Form::close() }}

				</div>
			</div>
		</div>
	</div>
</div>
@endsection
