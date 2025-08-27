@extends("layouts.app")
@section("content")
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
<script src="{{ asset('/js/student.js') }}"></script>
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
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">生徒情報編集 名前：{{ $student->surname }}{{ $student->name }}</div>
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

					{{ Form::model($student, array('route' => array('student.update', $student->id), 'method' => 'PUT', 'class' => 'form-horizontal')) }}

					<div class="form-group row">
						<div class="col-md-3 col-form-label">
							{{Form::label('inputName','本人情報')}}
						</div>
					</div>
					<!--生徒氏名-->
					<div class="form-group row">
						<div class="col-md-2 mb-3">
							{{Form::label('inputName','生徒氏名')}}
						</div>
						<div class="col-md-5">
							{{Form::text('surname', null, ['class' => 'form-control','id' => 'surname','style'=>'background-color:#f3d7dc','placeholder' => '姓',$disabled])}}
						</div>
						<div class="col-md-5">
							{{Form::text('name', null, ['class' => 'form-control','id' => 'name','style'=>'background-color:#f3d7dc','placeholder' => '名',$disabled])}}
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-2 mb-3">
						</div>
						<div class="col-md-5">
							{{Form::text('surname_kana', null, ['class' => 'form-control hira_change','id' => 'surname_kana','style'=>'background-color:#f3d7dc','placeholder' => 'セイ',$disabled])}}
						</div>
						<div class="col-md-5">
							{{Form::text('name_kana', null, ['class' => 'form-control hira_change','id' => 'name_kana','style'=>'background-color:#f3d7dc','placeholder' => 'メイ',$disabled])}}
						</div>
					</div>

					<!--生年月日-->
					<div class="form-group row">
						<div class="col-md-2 mb-3">
							{{Form::label('birthdate','生年月日')}}
						</div>
						<div class="col-md-5">
							{{Form::text('birthdate', null, ['class' => 'form-control char_change','id' => 'birthdate','placeholder' => '例：2022-10-01',$disabled])}}
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
								{{Form::radio('gender', '2', false, ['class'=>'custom-control-input','id'=>'gender1',$disabled])}}
								{{Form::label('gender1','女性',['class'=>'custom-control-label'])}}
							</div>
							<div class="custom-control custom-radio custom-control-inline">
								{{Form::radio('gender', '1', false, ['class'=>'custom-control-input','id'=>'gender2',$disabled])}}
								{{Form::label('gender2','男性',['class'=>'custom-control-label'])}}
							</div>
						</div>
					</div>

					{{-- 生徒パスワード ※表示のみ --}}
					<div class="form-group row">
						<div class="col-md-2 mb-3">
							<label for="initial_password">マイページ用初期パスワード</label>
						</div>
						<div class="col-md-5">
							<input type="text" class="form-control" id="initial_password" value="{{ $student->initial_password ?? '未設定' }}" disabled>
						</div>
					</div>

					<!--/性別-->

					<!--郵便番号-->
					<div class="form-group row">
						<div class="col-md-2 mb-3">
							{{Form::label('zip_code','郵便番号')}}
						</div>
						<div class="col-md-5">
							{{Form::text('zip_code', null, ['onKeyUp'=>"AjaxZip3.zip2addr(this,'','address1','address1')",'class' => 'form-control char_change','id' => 'zip_code','style'=>'background-color:#f3d7dc','placeholder' => '***-****',$disabled])}}
						</div>
					</div>
					<!--/郵便番号-->

					<!--住所-->
					<div class="form-group row">
						<div class="col-md-2 mb-3">
							{{Form::label('address1','住所⓵')}}
						</div>
						<div class="col-md-10">
							{{Form::text('address1', null, ['class' => 'form-control','id' => 'address1','style'=>'background-color:#f3d7dc','placeholder' => '',$disabled])}}
						</div>
						<div class="col-md-2 mb-3">
							{{Form::label('address2','住所⓶')}}
						</div>
						<div class="col-md-10">
							{{Form::text('address2', null, ['class' => 'form-control','id' => 'address2','placeholder' => '',$disabled])}}
						</div>
						<div class="col-md-2 mb-3">
							{{Form::label('address3','住所⓷')}}
						</div>
						<div class="col-md-10">
							{{Form::text('address3', null, ['class' => 'form-control','id' => 'address3','placeholder' => '',$disabled])}}
						</div>
					</div>
					<!--/住所-->

					<!--電話番号-->
					<div class="form-group row">
						<div class="col-md-2 mb-3">
							{{Form::label('phone1','電話番号⓵')}}
						</div>
						<div class="col-md-5">
							{{Form::text('phone1',null,['class' => 'form-control char_change','id' => 'phone1','style'=>'background-color:#f3d7dc','placeholder' => '例：090-1234-5678',$disabled])}}
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-2 mb-3">
							{{Form::label('phone2','電話番号⓶')}}
						</div>
						<div class="col-md-5">
							{{Form::text('phone2',null,['class' => 'form-control char_change','id' => 'phone2','placeholder' => '例：090-1234-5678',$disabled])}}
						</div>
					</div>
					<!--/電話番号-->

					<!--Eメール-->
					<div class="form-group row">
						<div class="col-md-2 mb-3">
							{{Form::label('email','Eメール')}}
						</div>
						<div class="col-md-5">
							{{Form::email('email', null, ['autocomplete'=>'off','class' => 'form-control','id' => 'email','placeholder' => 'Eメール',$disabled])}}
						</div>
					</div>
					<!--/Eメール-->

					{{-- 生徒パスワード ※表示のみ --}}
					<div class="form-group row">
						<div class="col-md-2 mb-3">
							<label for="initial_password">マイページ用Eメール</label>
						</div>
						<div class="col-md-5">
							<input type="text" class="form-control" id="initial_password" value="{{ $student->parent->email ?? '未設定' }}" disabled>
						</div>
					</div>

					<!--FAX-->
					{{-- <div class="form-group row">
						<div class="col-md-2 mb-3">
							{{Form::label('fax','FAX番号')}}
						</div>
						<div class="col-md-5">
							{{Form::text('fax',null,['class' => 'form-control char_change','id' => 'fax','placeholder' => ''])}}
						</div>
						<div class="custom-control custom-checkbox custom-control-inline col-md-5">
							{{Form::hidden('fax_flg', '0') }}
							{{Form::checkbox('fax_flg', '1', $student->fax_flg, ['class'=>'custom-control-input','id'=>'fax_flg'])}}
							{{Form::label('fax_flg','FAX送信希望',['class'=>'custom-control-label'])}}
						</div>
					</div> --}}
					<!--/FAX-->

					<!--現在学年-->
					<div class="form-group row">
						<div class="col-md-2 mb-3">
							{{Form::label('grade','現在学年')}}
						</div>
						<div class="col-md-3">
							@if(auth()->user()->roles == 1)
							{{ Form::select('grade', config('const.school_year'),null,['placeholder' => '選択してください', 'class' => 'form-control select_search_grade']) }}
							@else
							{{ Form::select('grade', config('const.school_year'),null,['placeholder' => '選択してください', 'class' => 'form-control',$disabled]) }}
							@endif
						</div>
						<div class="col-md-2 mb-3">
							{{Form::label('school','学校')}}
						</div>
						<div class="col-md-5">
							{{ Form::select('school_id', $schools_select_list,null,['placeholder' => '選択してください', 'class' => 'form-control select_search']) }}
						</div>
					</div>
					<!--/現在学年-->

					<!--家族情報-->
					<div class="form-group row">
						<div class="col-md-2 mb-3">
							{{Form::label('inputfamily','家族情報')}}
						</div>
					</div>
					<!--/家族情報-->

					<!--保護者氏名-->
					<div class="form-group row">
						<div class="col-md-2 mb-3">
							{{Form::label('inputName','保護者氏名')}}
						</div>
						<div class="col-md-5">
							{{Form::text('parent_surname', null, ['class' => 'form-control','id' => 'parent_surname','style'=>'background-color:#f3d7dc','placeholder' => '姓'])}}
						</div>
						<div class="col-md-5">
							{{Form::text('parent_name', null, ['class' => 'form-control','id' => 'parent_name','style'=>'background-color:#f3d7dc','placeholder' => '名'])}}
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
							{{Form::hidden('brothers_flg', '0',[$disabled]) }}
							{{Form::checkbox('brothers_flg', '1',$student->brothers_flg, ['class'=>'custom-control-input','id'=>'brothers_flg',$disabled])}}
							{{Form::label('brothers_flg','兄弟姉妹が在塾',['class'=>'custom-control-label'])}}
						</div>
						<div class="custom-control custom-checkbox custom-control-inline col-md-3">
							{{Form::hidden('fatherless_flg', '0',[$disabled]) }}
							{{Form::checkbox('fatherless_flg', '1',$student->fatherless_flg, ['class'=>'custom-control-input','id'=>'fatherless_flg',$disabled])}}
							{{Form::label('fatherless_flg','ひとり親家庭',['class'=>'custom-control-label'])}}
						</div>
					</div>
					<!--/兄弟姉妹-->

					<!--請求情報-->
					<div class="form-group row">
						<div class="col-md-2 mb-3">
							{{Form::label('inputbilling','請求情報')}}
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-2 mb-3">
							{{Form::label('inputBank','銀行選択')}}
						</div>
						<div class="col-md-3">
							{{ Form::select('bank_id', $banks_select_list,null,['id' => 'bank_id','class' => 'form-control select_search_school banks_select','placeholder' => '選択してください',$disabled]) }}
						</div>
						<div class="col-md-2 mb-3">
							{{Form::label('payment_methods','引き落とし方法')}}
						</div>
						<div class="col-md-5">
							{{ Form::select('payment_methods', config('const.payment_methods'),null,['id' => 'payment_methods','placeholder' => '選択してください', 'class' => 'form-control','style'=>'background-color:#f3d7dc',$disabled]) }}
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-2 mb-3">
							{{Form::label('inputBank','支店選択')}}
						</div>
						<div class="col-md-2">
							{{ Form::select('branch_code', $branch_banks_select_list,null,['class' => 'form-control select_search branch_banks','placeholder' => '選択してください',$disabled]) }}
						</div>
						<div class="col-md-2 mb-3">

						</div>
					</div>

					<div class="form-group row">
						@if(auth()->user()->roles == 1)
						<div class="col-md-2 mb-3">
							{{Form::label('inputBankid','口座番号')}}
						</div>
						<div class="col-md-3">
							{{Form::number('bank_number', null, ['class' => 'form-control char_change no-spin','id' => 'bank_number','placeholder' => '','maxlength'=>'7'])}}
						</div>
						<div class="col-md-1 mb-3">
							{{Form::label('bank_holder','口座名義')}}
						</div>
						<div class="col-md-3">
							{{Form::text('bank_holder', null, ['class' => 'form-control hira_change hankaku_kana_change','id' => 'bank_holder','placeholder' => ''])}}
						</div>
						<div class="col-md-1 mb-5">
							{{Form::label('inputBankid','口座種別')}}
						</div>
						<div class="col-md-2">
							{{ Form::select('bank_type', config('const.account_type'),null,['placeholder' => '選択してください', 'class' => 'form-control']) }}
						</div>
						@else
						<div class="col-md-2 mb-3">
							{{Form::label('inputBankid','口座種別')}}
						</div>
						<div class="col-md-3">
							{{ Form::select('bank_type', config('const.account_type'),null,['placeholder' => '選択してください', 'class' => 'form-control']) }}
						</div>
						@endif
					</div>

					<div class="form-group row">
						<div class="col-md-2 mb-3">
							{{Form::label('discount_id','割引')}}
						</div>
						<div class="col-md-8">
							{{ Form::select('discount_id',$discounts_select_list,null,['placeholder' => '選択してください','class' => 'form-control select_search',$disabled]) }}
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-2 mb-3">
							{{Form::label('billing_start_date','請求開始日')}}
						</div>
						<div class="col-md-5">
							{{Form::date('billing_start_date', null, ['class' => 'form-control','id' => 'billing_start_date','style'=>'background-color:#f3d7dc',$disabled])}}
						</div>
						<div class="custom-control custom-checkbox custom-control-inline col-md-2">
							{{Form::hidden('debit_stop_flg', '0',[$disabled]) }}
							{{Form::checkbox('debit_stop_flg', '1', $student->debit_stop_flg, ['class'=>'custom-control-input','id'=>'debit_stop_flg',$disabled])}}
							{{Form::label('debit_stop_flg','引落停止',['class'=>'custom-control-label'])}}
						</div>
						<div class="col-md-2 mb-3">
							{{Form::label('debit_stop_start','引落停止日')}}
						</div>
						<div class="col-md-2">
							{{Form::date('debit_stop_start_date', null, ['class' => 'form-control','id' => 'debit_stop_start_date',$disabled])}}
						</div>
					</div>
					<!--/請求情報-->

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
							{{ Form::select('school_building_id',$schooolbuildings_select_list,null,['placeholder' => '選択してください','class' => 'form-control select_search',$disabled]) }}
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-2 mb-2">
							{{Form::label('inpuJuku_start_date','入塾日')}}
						</div>
						<div class="col-md-3">
							{{Form::date('juku_start_date', null, ['class' => 'form-control','id' => 'juku_start_date','style'=>'background-color:#f3d7dc',$disabled])}}
						</div>
						<div class="col-md-2">
							{{Form::label('juku_class','塾クラス')}}
						</div>
						<div class="col-md-3">
							{{Form::text('juku_class', null, ['class' => 'form-control','id' =>'juku_class','placeholder' => ''])}}
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-2 mb-2">
							{{Form::label('juku_rest_date','休塾日')}}
						</div>
						<div class="col-md-3">
							{{Form::date('juku_rest_date', null, ['class' => 'form-control','id' => 'juku_rest_date',$disabled])}}
						</div>
						<div class="col-md-2 mb-2">
							{{Form::label('juku_return_date','復塾開始日')}}
						</div>
						<div class="col-md-3">
							{{Form::date('juku_return_date', null, ['class' => 'form-control','id' => 'juku_return_date',$disabled])}}
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-2 mb-2">
							{{Form::label('juku_graduation_date','卒塾日')}}
						</div>
						<div class="col-md-3">
							{{Form::date('juku_graduation_date', null, ['class' => 'form-control','id' => 'juku_graduation_date',$disabled])}}
						</div>
						<div class="col-md-2 mb-2">
							{{Form::label('juku_withdrawal_date','退塾日')}}
						</div>
						<div class="col-md-3">
							{{Form::date('juku_withdrawal_date', null, ['class' => 'form-control','id' => 'juku_withdrawal_date',$disabled])}}
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-2">
							{{Form::label('high_school_exam_year','受験年度')}}
						</div>
						<div class="col-md-3">
							{{Form::number('high_school_exam_year', null, ['class' => 'form-control','id' => 'high_school_exam_year','placeholder' => $year,$disabled])}}
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-2">
							{{Form::label('choice_private_school','進学先')}}
						</div>
						<div class="col-md-3">
							{{ Form::select('school_classification1', config('const.school_classification'),null,['placeholder' => '選択してください','class' => 'form-control school_classification']) }}
						</div>
						<div class="col-md-3">
							{{ Form::text('choice_private_school_name1',null,['placeholder' => '','class' => 'form-control choice_school']) }}
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-2">
						</div>
						<div class="col-md-3">
							{{ Form::select('school_classification2', config('const.school_classification'),null,['placeholder' => '選択してください','class' => 'form-control school_classification']) }}
						</div>
						<div class="col-md-3">
							{{ Form::text('choice_private_school_name2',null,['placeholder' => '','class' => 'form-control choice_school']) }}
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-2">
						</div>
						<div class="col-md-3">
							{{ Form::select('school_classification3', config('const.school_classification'),null,['placeholder' => '選択してください','class' => 'form-control school_classification']) }}
						</div>
						<div class="col-md-3">
							{{ Form::text('choice_private_school_name3',null,['placeholder' => '','class' => 'form-control choice_school']) }}
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-2">
						</div>
						<div class="col-md-3">
							{{ Form::select('school_classification4', config('const.school_classification'),null,['placeholder' => '選択してください','class' => 'form-control school_classification']) }}
						</div>
						<div class="col-md-3">
							{{ Form::text('choice_private_school_name4',null,['placeholder' => '','class' => 'form-control choice_school']) }}
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-2">
						</div>
						<div class="col-md-3">
							{{ Form::select('school_classification5', config('const.school_classification'),null,['placeholder' => '選択してください','class' => 'form-control school_classification']) }}
						</div>
						<div class="col-md-3">
							{{ Form::text('choice_private_school_name5',null,['placeholder' => '','class' => 'form-control choice_school']) }}
						</div>
					</div>


					<div class="form-group row">
						<div class="col-md-2">
							{{Form::label('lessons_year','講習受講歴')}}
						</div>
						<div class="col-md-3">
							{{Form::text('lessons_year', null, ['class' => 'form-control','id' =>'lessons_year','placeholder' => $year.'年'])}}
						</div>
						<div class="col-md-4">
							{{ Form::select('lessons_id', $products_select_list,null,['placeholder' => '選択してください','class' => 'form-control select_search']) }}
						</div>
						<div class="col-md-2">
							{{Form::label('inputChoice_school','に参加')}}
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-2">
							{{Form::label('inputCourse_history','広告媒体')}}
						</div>
						<div class="col-md-10">
							{{Form::textarea('ad_media_note', null, ['class' => 'form-control', 'id' => 'textarea_ad_media_note', 'placeholder' => '', 'rows' => '3'])}}
						</div>
						{{-- <div class="col-md-1">
							{{Form::text('ad_media', null, ['class' => 'form-control','id' => 'inputCourse_history','placeholder' => ''])}}
						</div>
						<div class="col-md-3">
							{{Form::text('ad_media_note', null, ['class' => 'form-control','id' => 'inputCourse_history','placeholder' => ''])}}
						</div> --}}
					</div>

					<div class="form-group row">
						<div class="col-md-2">
							{{Form::label('inputCourse_history','通塾歴')}}
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-2">
							{{Form::label('juku_history_name','塾名')}}
						</div>
						<div class="col-md-4">
							{{Form::text('juku_history_name', null, ['class' => 'form-control','id' => 'juku_history_name','placeholder' => '**塾'])}}
						</div>
						<div class="col-md-2">
							{{Form::label('juku_history_date','期間')}}
						</div>
						<div class="col-md-4">
							{{Form::text('juku_history_date', null, ['class' => 'form-control','id' => 'juku_history_date','placeholder' => '〇〇か月'])}}
						</div>
					</div>
					<!--/塾内情報-->

					<div class="form-group row">
						<div class="col-md-2">
							{{Form::label('interview_record_label','面談懇談記録')}}
						</div>
						<div class="col-md-10">
							{{Form::textarea('interview_record', null, ['class' => 'form-control', 'id' => 'textarea_interview_record', 'placeholder' => '', 'rows' => '3'])}}
						</div>
					</div>

					<!--備考欄-->
					<div class="form-group row">
						<div class="col-md-2">
							{{Form::label('comment','特記事項')}}
						</div>
						<div class="col-md-10">
							{{Form::textarea('comment', null, ['class' => 'form-control', 'id' => 'textarea_comment', 'placeholder' => '', 'rows' => '3'])}}
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-4">
							{{Form::hidden('temporary_flg', '0',[$disabled]) }}
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
