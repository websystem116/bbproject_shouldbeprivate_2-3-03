@extends("layouts.app")
@section("content")
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endpush
@push('scripts')
<script type="text/javascript" src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
<script src="{{ asset('/js/student.js') }}"></script>
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
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">入塾前生徒新規登録</div>
				<div class="panel-body">
					<a href="{{ url("before_student") }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
					<br />
					<br />
					@if ($errors->any())
					<ul class="alert alert-danger">
						@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
						@endforeach
					</ul>
					@endif
					<form method="POST" action="{{route('before_student.store')}}" class="form-horizontal">
						{{ csrf_field() }}
						<div class="form-group row">
							<div class="col-md-3 col-form-label">
								{{Form::label('person_information','本人情報')}}
							</div>
						</div>
						<!--生徒氏名-->
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('student_name','生徒氏名')}}
							</div>
							<div class="col-md-5">
								{{Form::text('surname', null, ['class' => 'form-control','id' => 'surname','placeholder' => '姓'])}}
							</div>
							<div class="col-md-5">
								{{Form::text('name', null, ['class' => 'form-control','id' => 'name','placeholder' => '名'])}}
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

						<!--生年月日-->
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('birthdate','生年月日')}}
							</div>
							<div class="col-md-5">
								{{Form::text('birthdate', null, ['class' => 'form-control','id' => 'birthdate','placeholder' => '例：2022-10-01'])}}
							</div>
						</div>
						<!--/生年月日-->

						<!--性別-->
						<div class="form-group row">
							<div class="col-form-label col-md-2 mb-3">
								{{Form::label('gender_label','性別')}}
							</div>
							<div class="col-md-10">
								<div class="custom-control custom-radio custom-control-inline">
									{{Form::radio('gender', '2', false, ['class'=>'custom-control-input','id'=>'gender1','required'=>'required'])}}
									{{Form::label('gender1','女性',['class'=>'custom-control-label'])}}
								</div>
								<div class="custom-control custom-radio custom-control-inline">
									{{Form::radio('gender', '1', false, ['class'=>'custom-control-input','id'=>'gender2','required'=>'required'])}}
									{{Form::label('gender2','男性',['class'=>'custom-control-label'])}}
								</div>
							</div>
						</div>
						<!--/性別-->

						<!--郵便番号-->
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('zip_code','郵便番号')}}
							</div>
							<div class="col-md-5">
								{{Form::text('zip_code', null, ['onKeyUp'=>"AjaxZip3.zip2addr(this,'','address1','address1')",'class' => 'form-control char_change','id' => 'zip_code','placeholder' => '***-****'])}}
							</div>
						</div>
						<!--/郵便番号-->

						<!--住所-->
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('address1','住所⓵')}}
							</div>
							<div class="col-md-10">
								{{Form::text('address1', null, ['class' => 'form-control','id' => 'address1','placeholder' => ''])}}
							</div>
							<div class="col-md-2 mb-3">
								{{Form::label('address2','住所⓶')}}
							</div>
							<div class="col-md-10">
								{{Form::text('address2', null, ['class' => 'form-control','id' => 'address2','placeholder' => ''])}}
							</div>
							<div class="col-md-2 mb-3">
								{{Form::label('address3','住所⓷')}}
							</div>
							<div class="col-md-10">
								{{Form::text('address3', null, ['class' => 'form-control','id' => 'address3','placeholder' => ''])}}
							</div>
						</div>
						<!--/住所-->

						<!--電話番号-->
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('phone1','電話番号⓵')}}
							</div>
							<div class="col-md-5">
								{{Form::text('phone1',null,['class' => 'form-control char_change','id' => 'phone1','placeholder' => '例：090-1234-5678'])}}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('phone2','電話番号⓶')}}
							</div>
							<div class="col-md-5">
								{{Form::text('phone2',null,['class' => 'form-control char_change','id' => 'phone2','placeholder' => '例：090-1234-5678'])}}
							</div>
						</div>
						<!--/電話番号-->

						<!--Eメール-->
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('email','Eメール')}}
							</div>
							<div class="col-md-5">
								{{Form::email('email', null, ['class' => 'form-control','id' => 'email','placeholder' => 'Eメール'])}}
							</div>
						</div>
						<!--/Eメール-->

						<!--FAX-->
						{{-- <div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('faxnumber','FAX番号')}}
							</div>
							<div class="col-md-5">
								{{Form::text('fax',null,['class' => 'form-control char_change','id' => 'fax','placeholder' => '**-****-****'])}}
							</div>
						</div> --}}
						<!--/FAX-->

						<!--現在学年-->
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('grade','現在学年')}}
							</div>
							<div class="col-md-5">
								{{ Form::select('grade', config('const.school_year'),null,['placeholder' => '選択してください', 'class' => 'form-control select_search_grade']) }}
							</div>
							<div class="col-md-5">
								{{ Form::select('school_id',$schools_select_list,null,['placeholder' => '選択してください', 'class' => 'form-control select_search']) }}
							</div>
						</div>
						<!--/現在学年-->

						<!--家族情報-->
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('family_info','家族情報')}}
							</div>
						</div>
						<!--/家族情報-->

						<!--保護者氏名-->
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('parentname','保護者氏名')}}
							</div>
							<div class="col-md-5">
								{{Form::text('parent_surname', null, ['class' => 'form-control','id' => 'parent_surname','placeholder' => '姓'])}}
							</div>
							<div class="col-md-5">
								{{Form::text('parent_name', null, ['class' => 'form-control','id' => 'parent_name','placeholder' => '名'])}}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-2 mb-3">
							</div>
							<div class="col-md-5">
								{{Form::text('parent_surname_kana', null, ['class' => 'form-control hira_change','id' => 'parent_surname_kana','placeholder' => 'セイ'])}}
							</div>
							<div class="col-md-5">
								{{Form::text('parent_name_kana', null, ['class' => 'form-control hira_change','id' => 'parent_name_kana','placeholder' => 'メイ'])}}
							</div>
						</div>
						<!--/保護者氏名-->

						<!--兄弟姉妹-->
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('inputbrothers','兄弟姉妹名')}}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-2 mb-3">
							</div>
							<div class="col-md-3">
								{{Form::text('brothers_name1', null, ['class' => 'form-control','id' => 'brothers_name1','placeholder' => ''])}}
							</div>
							<div class="col-md-2">
								{{ Form::select('brothers_gender1', config('const.gender'),null,['placeholder' => '選択してください', 'class' => 'form-control']) }}
							</div>
							<div class="col-md-2">
								{{ Form::select('brothers_grade1', config('const.school_year'),null,['placeholder' => '選択してください', 'class' => 'form-control select_search_grade']) }}
							</div>
							<div class="col-md-2">
								{{ Form::select('brothers_school_no1', $schools_select_list,null,['placeholder' => '選択してください', 'class' => 'form-control select_search_school']) }}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-2 mb-3">
							</div>
							<div class="col-md-3">
								{{Form::text('brothers_name2', null, ['class' => 'form-control','id' => 'brothers_name2','placeholder' => ''])}}
							</div>
							<div class="col-md-2">
								{{ Form::select('brothers_gender2', config('const.gender'),null,['placeholder' => '選択してください', 'class' => 'form-control']) }}
							</div>
							<div class="col-md-2">
								{{ Form::select('brothers_grade2', config('const.school_year'),null,['placeholder' => '選択してください', 'class' => 'form-control select_search_grade']) }}
							</div>
							<div class="col-md-2">
								{{ Form::select('brothers_school_no2', $schools_select_list,null,['placeholder' => '選択してください', 'class' => 'form-control select_search_school']) }}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-2 mb-3">
							</div>
							<div class="col-md-3">
								{{Form::text('brothers_name3', null, ['class' => 'form-control','id' => 'brothers_name3','placeholder' => ''])}}
							</div>
							<div class="col-md-2">
								{{ Form::select('brothers_gender3', config('const.gender'),null,['placeholder' => '選択してください', 'class' => 'form-control']) }}
							</div>
							<div class="col-md-2">
								{{ Form::select('brothers_grade3', config('const.school_year'),null,['placeholder' => '選択してください', 'class' => 'form-control select_search_grade']) }}
							</div>
							<div class="col-md-2">
								{{ Form::select('brothers_school_no3',$schools_select_list,null,['placeholder' => '選択してください', 'class' => 'form-control select_search_school']) }}
							</div>
						</div>

						<div class="form-group row">
							<div class="col-md-2 mb-3">
							</div>
							<div class="custom-control custom-checkbox custom-control-inline col-md-3">
								{{Form::hidden('brothers_flg', '0') }}
								{{Form::checkbox('brothers_flg', '1',false, ['class'=>'custom-control-input','id'=>'brothers_flg'])}}
								{{Form::label('brothers_flg','兄弟姉妹が在塾',['class'=>'custom-control-label'])}}
							</div>
							<div class="custom-control custom-checkbox custom-control-inline col-md-3">
								{{Form::hidden('fatherless_flg', '0') }}
								{{Form::checkbox('fatherless_flg', '1',false, ['class'=>'custom-control-input','id'=>'fatherless_flg'])}}
								{{Form::label('fatherless_flg','ひとり親家庭',['class'=>'custom-control-label'])}}
							</div>
						</div>
						<!--/兄弟姉妹-->

						<!--塾内情報-->
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('inputJuku_info','塾内情報')}}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-2 mb-2">
								{{Form::label('school_building_id','校舎')}}
							</div>
							<div class="col-md-4">
								{{ Form::select('school_building_id',$schooolbuildings_select_list,null,['placeholder' => '選択してください','class' => 'form-control select_search']) }}
							</div>
						</div>
						<!--塾内情報-->

						<!--問合わせ管理-->
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('contact_info','問合わせ管理')}}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-2 mb-2">
								{{Form::label('contact_tel_date','問合わせ(電話)')}}
							</div>
							<div class="col-md-3">
								{{Form::text('contact_tel_date', null, ['class' => 'form-control','id' => 'contact_tel_date','placeholder' => '例：'.$year.'-01-01'])}}
							</div>
							<div class="col-md-2">
								{{Form::label('description_juku_date','入塾説明')}}
							</div>
							<div class="col-md-3">
								{{Form::text('description_juku_date', null, ['class' => 'form-control','id' =>'description_juku_date','placeholder' => '例：'.$year.'-01-01'])}}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-2 mb-2">
								{{Form::label('coming_juku_date','問合せ(来塾)')}}
							</div>
							<div class="col-md-3">
								{{Form::text('coming_juku_date', null, ['class' => 'form-control','id' => 'coming_juku_date','placeholder' => '例：'.$year.'-01-01'])}}
							</div>
							<div class="col-md-2 mb-2">
								{{Form::label('juku_test_date','入塾テスト')}}
							</div>
							<div class="col-md-3">
								{{Form::text('juku_test_date', null, ['class' => 'form-control','id' => 'juku_test_date','placeholder' => '例：'.$year.'-01-01'])}}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-2 mb-2">
								{{Form::label('document_request_date','問合せ(資料請求)')}}
							</div>
							<div class="col-md-3">
								{{Form::text('document_request_date', null, ['class' => 'form-control','id' => 'document_request_date','placeholder' => '例：'.$year.'-01-01'])}}
							</div>
							<div class="col-md-2 mb-2">
								{{Form::label('special_experience_date','特別体験')}}
							</div>
							<div class="col-md-3">
								{{Form::text('special_experience_date', null, ['class' => 'form-control','id' => 'special_experience_date','placeholder' => '例：'.$year.'-01-01'])}}
							</div>
						</div>
						<!--問合わせ管理-->


						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('juko_info','講習受講情報')}}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('summer_year','夏期講習')}}
							</div>
							<div class="col-md-3">
								{{Form::text('summer_year', null, ['class' => 'form-control','id' => 'summer_year','placeholder' => $year])}}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('winter_year','冬期講習')}}
							</div>
							<div class="col-md-3">
								{{Form::text('winter_year', null, ['class' => 'form-control','id' => 'winter_year','placeholder' => $year])}}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('spring_year','春期講習')}}
							</div>
							<div class="col-md-3">
								{{Form::text('spring_year', null, ['class' => 'form-control','id' => 'spring_year','placeholder' => $year])}}
							</div>
						</div>

						<div class="form-group row">
							<div class="col-md-2 mb-3">
							</div>
							<div class="custom-control custom-checkbox custom-control-inline col-md-3">
								{{Form::hidden('sign_up_juku_flg', '0') }}
								{{Form::checkbox('sign_up_juku_flg', '1',false, ['class'=>'custom-control-input','id'=>'sign_up_juku_flg'])}}
								{{Form::label('sign_up_juku_flg','入塾フラグ',['class'=>'custom-control-label'])}}
							</div>
							{{-- <div class="custom-control custom-checkbox custom-control-inline col-md-3">
								{{Form::hidden('delete_flg', '0') }}
								{{Form::checkbox('delete_flg', '1',false, ['class'=>'custom-control-input','id'=>'delete_flg'])}}
								{{Form::label('delete_flg','無効フラグ',['class'=>'custom-control-label'])}}
							</div> --}}
						</div>
						<!--ボタンブロック-->
						<div class="form-group row">
							<div class="col-md-4">
								{{Form::submit('登録', ['class'=>'btn btn-primary confirm'])}}
							</div>
						</div>
				</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!--/ボタンブロック-->
@endsection
