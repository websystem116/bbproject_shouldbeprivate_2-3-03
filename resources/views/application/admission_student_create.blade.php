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
	});
</script>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">入塾者内容登録</div>
				<div class="panel-body">
					<a href="{{ route('application.accept_index') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
					<br />
					<br />
					@if ($errors->any())
					<ul class="alert alert-danger">
						@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
						@endforeach
					</ul>
					@endif
					<form method="POST" action="{{route('application.admission_course_create')}}" class="form-horizontal">
						{{ csrf_field() }}
						<input type="hidden" name="class_shogakubu" value="{{ isset($old_data['class_shogakubu'])&&$old_data['class_shogakubu']=='1'?'1':'0' }}">
						<input type="hidden" name="class_shogakubu_3ka" value="{{ isset($old_data['class_shogakubu_3ka'])&&$old_data['class_shogakubu_3ka']=='1'?'1':'0' }}">
						<input type="hidden" name="class_shogakubu_2ka" value="{{ isset($old_data['class_shogakubu_2ka'])&&$old_data['class_shogakubu_2ka']=='1'?'1':'0' }}">
						<input type="hidden" name="class_shogakubu_2ka_label" value="{{ isset($old_data['class_shogakubu_2ka_label'])?$old_data['class_shogakubu_2ka_label']:'' }}">
						<input type="hidden" name="class_shogakubu_1ka" value="{{ isset($old_data['class_shogakubu_1ka'])&&$old_data['class_shogakubu_1ka']=='1'?'1':'0' }}">
						<input type="hidden" name="class_shogakubu_1ka_label" value="{{ isset($old_data['class_shogakubu_1ka_label'])?$old_data['class_shogakubu_1ka_label']:'' }}">
						<input type="hidden" name="class_chugaku1_2" value="{{ isset($old_data['class_chugaku1_2'])&&$old_data['class_chugaku1_2']=='1'?'1':'0' }}">
						<input type="hidden" name="class_chugaku1_2_p5ka" value="{{ isset($old_data['class_chugaku1_2_p5ka'])&&$old_data['class_chugaku1_2_p5ka']=='1'?'1':'0' }}">
						<input type="hidden" name="class_chugaku1_2_kyoka" value="{{ isset($old_data['class_chugaku1_2_kyoka'])&&$old_data['class_chugaku1_2_kyoka']=='1'?'1':'0' }}">
						<input type="hidden" name="class_chugaku1_2_kyoka_ka" value="{{ isset($old_data['class_chugaku1_2_kyoka_ka'])?$old_data['class_chugaku1_2_kyoka_ka']:'' }}">
						<input type="hidden" name="class_chugaku1_2_kyoka_label" value="{{ isset($old_data['class_chugaku1_2_kyoka_label'])?$old_data['class_chugaku1_2_kyoka_label']:'' }}">
						<input type="hidden" name="class_chugaku3" value="{{ isset($old_data['class_chugaku3'])&&$old_data['class_chugaku3']=='1'?'1':'0' }}">
						<input type="hidden" name="class_chugaku3_p5ka" value="{{ isset($old_data['class_chugaku3_p5ka'])&&$old_data['class_chugaku3_p5ka']=='1'?'1':'0' }}">
						<input type="hidden" name="class_chugaku3_s5ka" value="{{ isset($old_data['class_chugaku3_s5ka'])&&$old_data['class_chugaku3_s5ka']=='1'?'1':'0' }}">
						<input type="hidden" name="class_chugaku3_kyoka" value="{{ isset($old_data['class_chugaku3_kyoka'])&&$old_data['class_chugaku3_kyoka']=='1'?'1':'0' }}">
						<input type="hidden" name="class_chugaku3_kyoka_label" value="{{ isset($old_data['class_chugaku3_kyoka_label'])?$old_data['class_chugaku3_kyoka_label']:'' }}">
						<input type="hidden" name="kobetsu_kyoka" value="{{ isset($old_data['kobetsu_kyoka'])?$old_data['kobetsu_kyoka']:'' }}">
						<input type="hidden" name="kobetsu_kobetsushidokosu" value="{{ isset($old_data['kobetsu_kobetsushidokosu'])&&$old_data['kobetsu_kobetsushidokosu']=='1'?'1':'0' }}">
						<input type="hidden" name="kobetsu_kobetsushidokosu_bun" value="{{ isset($old_data['kobetsu_kobetsushidokosu_bun'])?$old_data['kobetsu_kobetsushidokosu_bun']:'' }}">
						<input type="hidden" name="kobetsu_mantsumankosu" value="{{ isset($old_data['kobetsu_mantsumankosu'])&&$old_data['kobetsu_mantsumankosu']=='1'?'1':'0' }}">
						<input type="hidden" name="kobetsu_mantsumankosu_kaisu" value="{{ isset($old_data['kobetsu_mantsumankosu_kaisu'])?$old_data['kobetsu_mantsumankosu_kaisu']:'' }}">
						<input type="hidden" name="kokosei_jishushitsuriyo" value="{{ isset($old_data['kokosei_jishushitsuriyo'])&&$old_data['kokosei_jishushitsuriyo']=='1'?'1':'0' }}">
						<input type="hidden" name="kokosei_shudanshido" value="{{ isset($old_data['kokosei_shudanshido'])&&$old_data['kokosei_shudanshido']=='1'?'1':'0' }}">
						<input type="hidden" name="kokosei_shudanshido_label" value="{{ isset($old_data['kokosei_shudanshido_label'])?$old_data['kokosei_shudanshido_label']:'' }}">
						<input type="hidden" name="kokosei_kobetsushidokosu" value="{{ isset($old_data['kokosei_kobetsushidokosu'])&&$old_data['kokosei_kobetsushidokosu']=='1'?'1':'0' }}">
						<input type="hidden" name="kokosei_kobetsushidokosu_kaisu" value="{{ isset($old_data['kokosei_kobetsushidokosu_kaisu'])?$old_data['kokosei_kobetsushidokosu_kaisu']:'' }}">
						<input type="hidden" name="kokosei_kobetsushidokosu_kyoka" value="{{ isset($old_data['kokosei_kobetsushidokosu_kyoka'])?$old_data['kokosei_kobetsushidokosu_kyoka']:'' }}">
						<input type="hidden" name="etc_ondemando" value="{{ isset($old_data['etc_ondemando'])&&$old_data['etc_ondemando']=='1'?'1':'0' }}">
						<input type="hidden" name="etc_eikaiwa" value="{{ isset($old_data['etc_eikaiwa'])&&$old_data['etc_eikaiwa']=='1'?'1':'0' }}">
						<input type="hidden" name="etc_programing" value="{{ isset($old_data['etc_programing'])&&$old_data['etc_programing']=='1'?'1':'0' }}">
						<input type="hidden" name="etc_soroban" value="{{ isset($old_data['etc_soroban'])&&$old_data['etc_soroban']=='1'?'1':'0' }}">
						<input type="hidden" name="etc_soroban_kaisu" value="{{ isset($old_data['etc_soroban_kaisu'])?$old_data['etc_soroban_kaisu']:'' }}">

						<input type="hidden" name="tanmatsukiki_yes" value="{{ isset($old_data['tanmatsukiki_yes'])&&$old_data['tanmatsukiki_yes']=='1'?'1':'0' }}">
						<input type="hidden" name="tanmatsukiki_no" value="{{ isset($old_data['tanmatsukiki_no'])&&$old_data['tanmatsukiki_no']=='1'?'1':'0' }}">
						<input type="hidden" name="shingakuzemina_howknow_1" value="{{ isset($old_data['shingakuzemina_howknow_1'])&&$old_data['shingakuzemina_howknow_1']=='1'?'1':'0' }}">
						<input type="hidden" name="shingakuzemina_howknow_2" value="{{ isset($old_data['shingakuzemina_howknow_2'])&&$old_data['shingakuzemina_howknow_2']=='1'?'1':'0' }}">
						<input type="hidden" name="shingakuzemina_howknow_3" value="{{ isset($old_data['shingakuzemina_howknow_3'])&&$old_data['shingakuzemina_howknow_3']=='1'?'1':'0' }}">
						<input type="hidden" name="shingakuzemina_howknow_4" value="{{ isset($old_data['shingakuzemina_howknow_4'])&&$old_data['shingakuzemina_howknow_4']=='1'?'1':'0' }}">
						<input type="hidden" name="shingakuzemina_howknow_5" value="{{ isset($old_data['shingakuzemina_howknow_5'])&&$old_data['shingakuzemina_howknow_5']=='1'?'1':'0' }}">
						<input type="hidden" name="shingakuzemina_howknow_6" value="{{ isset($old_data['shingakuzemina_howknow_6'])&&$old_data['shingakuzemina_howknow_6']=='1'?'1':'0' }}">
						<input type="hidden" name="shingakuzemina_howknow_7" value="{{ isset($old_data['shingakuzemina_howknow_7'])&&$old_data['shingakuzemina_howknow_7']=='1'?'1':'0' }}">
						<input type="hidden" name="shingakuzemina_howknow_8" value="{{ isset($old_data['shingakuzemina_howknow_8'])&&$old_data['shingakuzemina_howknow_8']=='1'?'1':'0' }}">
						<input type="hidden" name="shingakuzemina_howknow_9" value="{{ isset($old_data['shingakuzemina_howknow_9'])&&$old_data['shingakuzemina_howknow_9']=='1'?'1':'0' }}">
						<input type="hidden" name="shingakuzemina_howknow_9_label" value="{{ isset($old_data['shingakuzemina_howknow_9_label'])?$old_data['shingakuzemina_howknow_9_label']:'' }}">
						<input type="hidden" name="shingakuzemina_howknow_10" value="{{ isset($old_data['shingakuzemina_howknow_10'])&&$old_data['shingakuzemina_howknow_10']=='1'?'1':'0' }}">
						<input type="hidden" name="shingakuzemina_howknow_11" value="{{ isset($old_data['shingakuzemina_howknow_11'])&&$old_data['shingakuzemina_howknow_11']=='1'?'1':'0' }}">
						<input type="hidden" name="shingakuzemina_howknow_11_label" value="{{ isset($old_data['shingakuzemina_howknow_11_label'])?$old_data['shingakuzemina_howknow_11_label']:'' }}">
						<input type="hidden" name="shingakuzemina_reason" value="{{ isset($old_data['shingakuzemina_reason'])?$old_data['shingakuzemina_reason']:'' }}">
						<input type="hidden" name="shingakuzemina_require" value="{{ isset($old_data['shingakuzemina_require'])?$old_data['shingakuzemina_require']:'' }}">

						<!--生徒氏名-->
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('inputName','生徒氏名')}}
							</div>
							<div class="col-md-5">
								{{Form::text('surname', isset($old_data['surname'])?$old_data['surname']:null, ['class' => 'form-control','id' => 'surname','style'=>isset($old_data['surname'])?'':'background-color:#f3d7dc','placeholder' => '姓'])}}
							</div>
							<div class="col-md-5">
								{{Form::text('name', isset($old_data['name'])?$old_data['name']:null, ['class' => 'form-control','id' => 'name','style'=>isset($old_data['name'])?'':'background-color:#f3d7dc','placeholder' => '名'])}}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-2 mb-3">
							</div>
							<div class="col-md-5">
								{{Form::text('surname_kana', isset($old_data['surname_kana'])?$old_data['surname']:null, ['class' => 'form-control hira_change','id' => 'surname_kana','style'=>isset($old_data['surname_kana'])?'':'background-color:#f3d7dc','placeholder' => 'セイ'])}}
							</div>
							<div class="col-md-5">
								{{Form::text('name_kana', isset($old_data['name_kana'])?$old_data['surname']:null, ['class' => 'form-control hira_change','id' => 'name_kana','style'=>isset($old_data['name_kana'])?'':'background-color:#f3d7dc','placeholder' => 'メイ'])}}
							</div>
						</div>

						<!--生年月日-->
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('birthdate','生年月日')}}
							</div>
							<div class="col-md-5">
								{{Form::text('birthdate', isset($old_data['birthdate'])?$old_data['birthdate']:null, ['class' => 'form-control char_change','id' => 'birthdate','placeholder' => '例：2022-10-01'])}}
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
									{{Form::radio('gender', '2', isset($old_data['gender'])&&$old_data['gender']=='2'?true:false, ['class'=>'custom-control-input','id'=>'gender1'])}}
									{{Form::label('gender1','女性',['class'=>'custom-control-label'])}}
								</div>
								<div class="custom-control custom-radio custom-control-inline">
									{{Form::radio('gender', '1', isset($old_data['gender'])&&$old_data['gender']=='1'?true:false, ['class'=>'custom-control-input','id'=>'gender2'])}}
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
								{{Form::text('zip_code', isset($old_data['zip_code'])?$old_data['zip_code']:null, ['autocomplete'=>'off','onKeyUp'=>"AjaxZip3.zip2addr(this,'','address1','address1')",'class' => 'form-control char_change','id' => 'zip_code','style'=>isset($old_data['zip_code'])?'':'background-color:#f3d7dc','placeholder' => '***-****'])}}
							</div>
						</div>
						<!--/郵便番号-->

						<!--住所-->
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('address1','住所⓵')}}
							</div>
							<div class="col-md-10">
								{{Form::text('address1', isset($old_data['address1'])?$old_data['address1']:null, ['class' => 'form-control','id' => 'address1','style'=>isset($old_data['address1'])?'':'background-color:#f3d7dc','placeholder' => ''])}}
							</div>
							<div class="col-md-2 mb-3">
								{{Form::label('address2','住所⓶')}}
							</div>
							<div class="col-md-10">
								{{Form::text('address2', isset($old_data['address2'])?$old_data['address2']:null, ['class' => 'form-control','id' => 'address2','placeholder' => ''])}}
							</div>
							<div class="col-md-2 mb-3">
								{{Form::label('address3','住所⓷')}}
							</div>
							<div class="col-md-10">
								{{Form::text('address3', isset($old_data['address3'])?$old_data['address3']:null, ['class' => 'form-control','id' => 'address3','placeholder' => ''])}}
							</div>
						</div>
						<!--/住所-->

						<!--保護者連絡先-->
						<div class="row">
							<div class="col-md-2 mb-3">
								{{Form::label('inputName','保護者連絡先')}}
							</div>
						</div>
						<div class="row">
							<div class="col-md-2 mb-3">
								{{Form::label('phone1','自宅')}}
							</div>
							<div class="col-md-5">
								{{Form::text('phone1',isset($old_data['phone1'])?$old_data['phone1']:null,['class' => 'form-control char_change','id' => 'phone1','style'=>isset($old_data['phone1'])?'':'background-color:#f3d7dc','placeholder' => '例：090-1234-5678'])}}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('phone2','携帯')}}
							</div>
							<div class="col-md-5">
								{{Form::text('phone2',isset($old_data['phone2'])?$old_data['phone2']:null,['class' => 'form-control char_change','id' => 'phone2','placeholder' => '例：090-1234-5678'])}}
							</div>
						</div>
						<!--/保護者連絡先-->

						<!--Eメール-->
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('email','入退室連絡用Eメール')}}
							</div>
							<div class="col-md-5">
								{{Form::email('email', isset($old_data['email'])?$old_data['email']:null, ['autocomplete'=>'off','class' => 'form-control','id' => 'email','placeholder' => 'Eメール', 'style'=>isset($old_data['email'])?'':'background-color:#f3d7dc'])}}
							</div>
						</div>
						<!--/Eメール-->

						<!--現在学年-->
						<div class="form-group row">
							<div class="col-md-2 mb-3">
								{{Form::label('grade','現在学年')}}
							</div>
							<div class="col-md-3">
								{{ Form::select('grade', config('const.school_year'),isset($old_data['grade'])?$old_data['grade']:null,['placeholder' => '選択してください', 'class' => 'form-control select_search_grade']) }}
							</div>
							<div class="col-md-2 mb-3">
								{{Form::label('school','学校')}}
							</div>
							<div class="col-md-5">
								{{ Form::select('school_id', $schools_select_list,isset($old_data['school_id'])?$old_data['school_id']:null,['placeholder' => '選択してください', 'class' => 'form-control select_search']) }}
							</div>
						</div>
						<!--/現在学年-->



						<!--ボタンブロック-->
						<div class="form-group row">
							<div class="col-md-4">
								{{Form::submit('次へ',['class'=>'btn btn-primary'])}}
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
