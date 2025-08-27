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
<script src="{{ asset('shinzemi/js/student.js') }}"></script>
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
<script>
	$(function() {
		// $('.select_search').select2({
		// 	language: "ja",
		// 	width: '300px'
		// });
		// $('.select_search_grade').select2({
		// 	language: "ja",
		// 	width: '100px'
		// });
		// $('.select_search_school').select2({
		// 	language: "ja",
		// 	width: '200px'
		// });
	});
	function back(){
		$("#frm").attr("action", "{{ route('application.admission_others_create') }}");
		$("#frm").submit();
	}
</script>
@endpush
@php
	function list_val($arr, $search){
		foreach($arr as $key => $val){
			if($key == $search)
				return $val;
		}
		return "";
	}
@endphp
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">登録内容確認</div>
				<div class="panel-body">
					<button class="btn btn-warning btn-xs" onclick="back();">戻る</button>
					<br />
					<br />
					@if ($errors->any())
					<ul class="alert alert-danger">
						@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
						@endforeach
					</ul>
					@endif
					<form method="POST" id="frm" action="{{route('application.admission_sign')}}" class="form-horizontal">
						{{ csrf_field() }}
						<input type="hidden" name="surname" value="{{ $old_data['surname'] }}">
						<input type="hidden" name="name" value="{{ $old_data['name'] }}">
						<input type="hidden" name="surname_kana" value="{{ $old_data['surname_kana'] }}">
						<input type="hidden" name="name_kana" value="{{ $old_data['name_kana'] }}">
						<input type="hidden" name="birthdate" value="{{ $old_data['birthdate'] }}">
						<input type="hidden" name="gender" value="{{ $old_data['gender'] }}">
						<input type="hidden" name="zip_code" value="{{ $old_data['zip_code'] }}">
						<input type="hidden" name="address1" value="{{ $old_data['address1'] }}">
						<input type="hidden" name="address2" value="{{ $old_data['address2'] }}">
						<input type="hidden" name="address3" value="{{ $old_data['address3'] }}">
						<input type="hidden" name="phone1" value="{{ $old_data['phone1'] }}">
						<input type="hidden" name="phone2" value="{{ $old_data['phone2'] }}">
						<input type="hidden" name="email" value="{{ $old_data['email'] }}">
						<input type="hidden" name="grade" value="{{ $old_data['grade'] }}">
						<input type="hidden" name="school_id" value="{{ $old_data['school_id'] }}">

						<input type="hidden" name="class_shogakubu" value="{{ isset($old_data['class_shogakubu'])&&$old_data['class_shogakubu']=='1'?'1':'0' }}">
						<input type="hidden" name="class_shogakubu_3ka" value="{{ isset($old_data['class_shogakubu_3ka'])&&$old_data['class_shogakubu_3ka']=='1'?'1':'0' }}">
						<input type="hidden" name="class_shogakubu_2ka" value="{{ isset($old_data['class_shogakubu_2ka'])&&$old_data['class_shogakubu_2ka']=='1'?'1':'0' }}">
						<input type="hidden" name="class_shogakubu_2ka_label" value="{{ $old_data['class_shogakubu_2ka_label'] }}">
						<input type="hidden" name="class_shogakubu_1ka" value="{{ isset($old_data['class_shogakubu_1ka'])&&$old_data['class_shogakubu_1ka']=='1'?'1':'0' }}">
						<input type="hidden" name="class_shogakubu_1ka_label" value="{{ $old_data['class_shogakubu_1ka_label'] }}">
						<input type="hidden" name="class_chugaku1_2" value="{{ isset($old_data['class_chugaku1_2'])&&$old_data['class_chugaku1_2']=='1'?'1':'0' }}">
						<input type="hidden" name="class_chugaku1_2_p5ka" value="{{ isset($old_data['class_chugaku1_2_p5ka'])&&$old_data['class_chugaku1_2_p5ka']=='1'?'1':'0' }}">
						<input type="hidden" name="class_chugaku1_2_kyoka" value="{{ isset($old_data['class_chugaku1_2_kyoka'])&&$old_data['class_chugaku1_2_kyoka']=='1'?'1':'0' }}">
						<input type="hidden" name="class_chugaku1_2_kyoka_ka" value="{{ $old_data['class_chugaku1_2_kyoka_ka'] }}">
						<input type="hidden" name="class_chugaku1_2_kyoka_label" value="{{ $old_data['class_chugaku1_2_kyoka_label'] }}">
						<input type="hidden" name="class_chugaku3" value="{{ isset($old_data['class_chugaku3'])&&$old_data['class_chugaku3']=='1'?'1':'0' }}">
						<input type="hidden" name="class_chugaku3_p5ka" value="{{ isset($old_data['class_chugaku3_p5ka'])&&$old_data['class_chugaku3_p5ka']=='1'?'1':'0' }}">
						<input type="hidden" name="class_chugaku3_s5ka" value="{{ isset($old_data['class_chugaku3_s5ka'])&&$old_data['class_chugaku3_s5ka']=='1'?'1':'0' }}">
						<input type="hidden" name="class_chugaku3_kyoka" value="{{ isset($old_data['class_chugaku3_kyoka'])&&$old_data['class_chugaku3_kyoka']=='1'?'1':'0' }}">
						<input type="hidden" name="class_chugaku3_kyoka_label" value="{{ $old_data['class_chugaku3_kyoka_label'] }}">
						<input type="hidden" name="kobetsu_kyoka" value="{{ $old_data['kobetsu_kyoka'] }}">
						<input type="hidden" name="kobetsu_kobetsushidokosu" value="{{ isset($old_data['kobetsu_kobetsushidokosu'])&&$old_data['kobetsu_kobetsushidokosu']=='1'?'1':'0' }}">
						<input type="hidden" name="kobetsu_kobetsushidokosu_bun" value="{{ isset($old_data['kobetsu_kobetsushidokosu_bun'])?$old_data['kobetsu_kobetsushidokosu_bun']:'' }}">
						<input type="hidden" name="kobetsu_mantsumankosu" value="{{ isset($old_data['kobetsu_mantsumankosu'])&&$old_data['kobetsu_mantsumankosu']=='1'?'1':'0' }}">
						<input type="hidden" name="kobetsu_mantsumankosu_kaisu" value="{{ $old_data['kobetsu_mantsumankosu_kaisu'] }}">
						<input type="hidden" name="kokosei_jishushitsuriyo" value="{{ isset($old_data['kokosei_jishushitsuriyo'])&&$old_data['kokosei_jishushitsuriyo']=='1'?'1':'0' }}">
						<input type="hidden" name="kokosei_shudanshido" value="{{ isset($old_data['kokosei_shudanshido'])&&$old_data['kokosei_shudanshido']=='1'?'1':'0' }}">
						<input type="hidden" name="kokosei_shudanshido_label" value="{{ $old_data['kokosei_shudanshido_label'] }}">
						<input type="hidden" name="kokosei_kobetsushidokosu" value="{{ isset($old_data['kokosei_kobetsushidokosu'])&&$old_data['kokosei_kobetsushidokosu']=='1'?'1':'0' }}">
						<input type="hidden" name="kokosei_kobetsushidokosu_kaisu" value="{{ $old_data['kokosei_kobetsushidokosu_kaisu'] }}">
						<input type="hidden" name="kokosei_kobetsushidokosu_kyoka" value="{{ $old_data['kokosei_kobetsushidokosu_kyoka'] }}">
						<input type="hidden" name="etc_ondemando" value="{{ isset($old_data['etc_ondemando'])&&$old_data['etc_ondemando']=='1'?'1':'0' }}">
						<input type="hidden" name="etc_eikaiwa" value="{{ isset($old_data['etc_eikaiwa'])&&$old_data['etc_eikaiwa']=='1'?'1':'0' }}">
						<input type="hidden" name="etc_programing" value="{{ isset($old_data['etc_programing'])&&$old_data['etc_programing']=='1'?'1':'0' }}">
						<input type="hidden" name="etc_soroban" value="{{ isset($old_data['etc_soroban'])&&$old_data['etc_soroban']=='1'?'1':'0' }}">
						<input type="hidden" name="etc_soroban_kaisu" value="{{ $old_data['etc_soroban_kaisu'] }}">

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

						
						<div class="form-group row">
							<div class="col-md-12">
								<h4>1. 入塾者内容</h4>
							</div>
						</div>

						<!--生徒氏名-->
						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('inputName','生徒氏名')}}
							</div>
							<div class="col-md-3">
								{{ isset($old_data['surname'])?$old_data['surname']:'' }}
								{{ isset($old_data['name'])?$old_data['name']:'' }}

								（{{ isset($old_data['surname_kana'])?$old_data['surname_kana']:'' }}
								{{ isset($old_data['name_kana'])?$old_data['name_kana']:'' }}）
							</div>
							<div class="col-md-2">
								{{Form::label('birthdate','生年月日')}}
							</div>
							<div class="col-md-5">
								{{ isset($old_data['birthdate'])?$old_data['birthdate']:'' }}
							</div>
						</div>
						<!--性別-->
						<div class="form-group row">
							<div class="col-form-label col-md-2">
								{{Form::label('gender_label','性別')}}
							</div>
							<div class="col-md-10">
								@if (isset($old_data['gender'])&&$old_data['gender']=='2')
								女性
								@elseif (isset($old_data['gender'])&&$old_data['gender']=='1')
								男性
								@endif
							</div>
						</div>
						<!--/性別-->

						<div class="form-group row">
							<!--郵便番号-->
							<div class="col-md-2">
								{{Form::label('zip_code','郵便番号')}}
							</div>
							<div class="col-md-3">
								{{ isset($old_data['zip_code'])?$old_data['zip_code']:'' }}
							</div>
							<!--/郵便番号-->
							<!--住所-->
							<div class="col-md-2">
								{{Form::label('address1','住所⓵')}}<br>
								{{Form::label('address2','住所⓶')}}<br>
								{{Form::label('address3','住所⓷')}}
							</div>
							<div class="col-md-5">
								{{ isset($old_data['address1'])?$old_data['address1']:'' }}<br>
								{{ isset($old_data['address2'])?$old_data['address2']:'' }}<br>
								{{ isset($old_data['address3'])?$old_data['address3']:'' }}
							</div>
							<!--/住所-->

						</div>


						<!--保護者連絡先-->
						<div class="row">
							<div class="col-md-2">
								{{Form::label('inputName','保護者連絡先')}}
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('phone1','自宅')}}
							</div>
							<div class="col-md-3">
								{{ isset($old_data['phone1'])?$old_data['phone1']:'' }}
							</div>
							<div class="col-md-2">
								{{Form::label('phone2','携帯')}}
							</div>
							<div class="col-md-5">
								{{ isset($old_data['phone2'])?$old_data['phone2']:'' }}
							</div>

						</div>
						<!--/保護者連絡先-->

						<!--Eメール-->
						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('email','入退室連絡用Eメール')}}
							</div>
							<div class="col-md-5">
								{{ isset($old_data['email'])?$old_data['email']:'' }}
							</div>
						</div>
						<!--/Eメール-->

						<!--現在学年-->
						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('grade','現在学年')}}
							</div>
							<div class="col-md-3">
								{{ list_val(config('const.school_year'),isset($old_data['grade'])?$old_data['grade']:null) }}
							</div>
							<div class="col-md-2">
								{{Form::label('school','学校')}}
							</div>
							<div class="col-md-5">
								{{ list_val($schools_select_list,isset($old_data['school_id'])?$old_data['school_id']:null) }}
							</div>
						</div>
						<!--/現在学年-->
						<hr>
						<div class="form-group row">
							<div class="col-md-12">
								<h4>2. 選択コース</h4>
							</div>
						</div>

						<!--クラス指導部-->
						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('class_shogakubu','クラス指導部',['class'=>'custom-control-label'])}}
							</div>
							<div class="col-md-10">
								@if (isset($old_data['class_shogakubu'])&&$old_data['class_shogakubu']=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control" style="margin-right:30px;">
									小学部 →
									</div>
									@if (isset($old_data['class_shogakubu_3ka'])&&$old_data['class_shogakubu_3ka']=='1')
									<div class="custom-control" style="margin-right:30px;">
										3科
									</div>
									@endif

									@if (isset($old_data['class_shogakubu_2ka'])&&$old_data['class_shogakubu_2ka']=='1')
									<div class="custom-control" style="margin-right:30px;">
										2科<?php if(isset($old_data['class_shogakubu_2ka_label'])&&$old_data['class_shogakubu_2ka_label']!=''){ ?>【{{ $old_data['class_shogakubu_2ka_label'] }}】<?php } ?>
									</div>
									@endif

									@if (isset($old_data['class_shogakubu_1ka'])&&$old_data['class_shogakubu_1ka']=='1')
									<div class="custom-control">
										1科<?php if (isset($old_data['class_shogakubu_1ka_label'])&&$old_data['class_shogakubu_1ka_label']!=''){ ?>【{{ $old_data['class_shogakubu_1ka_label'] }}】<?php } ?>
									</div>
									@endif
								</div>
								@endif										

								@if (isset($old_data['class_chugaku1_2'])&&$old_data['class_chugaku1_2']=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control" style="margin-right:30px;">
									中学1・2年 →
									</div>
									@if (isset($old_data['class_chugaku1_2_p5ka'])&&$old_data['class_chugaku1_2_p5ka']=='1')
									<div class="custom-control" style="margin-right:30px;">
										P 5科
									</div>
									@endif

									@if (isset($old_data['class_chugaku1_2_kyoka'])&&$old_data['class_chugaku1_2_kyoka']=='1')
									<div class="custom-control">
										教科選択
										<?php if (isset($old_data['class_chugaku1_2_kyoka_ka'])&&$old_data['class_chugaku1_2_kyoka_ka']!=''){ ?>
										【{{ $old_data['class_chugaku1_2_kyoka_ka'] }}】科：<?php } ?><?php 
										if (isset($old_data['class_chugaku1_2_kyoka_label'])&&$old_data['class_chugaku1_2_kyoka_label']!=''){?>【{{ $old_data['class_chugaku1_2_kyoka_label'] }}】<?php } ?>
									</div>
									@endif

								</div>
								@endif		
								
								@if (isset($old_data['class_chugaku3'])&&$old_data['class_chugaku3']=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control" style="margin-right:30px;">
									中学3年 →
									</div>

									@if (isset($old_data['class_chugaku3_p5ka'])&&$old_data['class_chugaku3_p5ka']=='1')
									<div class="custom-control" style="margin-right:30px;">
										P 5科
									</div>
									@endif
									@if (isset($old_data['class_chugaku3_s5ka'])&&$old_data['class_chugaku3_s5ka']=='1')
									<div class="custom-control" style="margin-right:30px;">
										S 5科
									</div>
									@endif
									@if (isset($old_data['class_chugaku3_kyoka'])&&$old_data['class_chugaku3_kyoka']=='1')
									<div class="custom-control">
										教科選択
										@if (isset($old_data['class_chugaku3_kyoka_label'])&&$old_data['class_chugaku3_kyoka_label']!='')
										【{{ $old_data['class_chugaku3_kyoka_label'] }}】
										@endif
									</div>
									@endif

								</div>
								@endif								

							</div>
						</div>
						<!--/クラス指導部-->

						<!--個別指導部-->
						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('class_shogakubu','個別指導部',['class'=>'custom-control-label'])}}
							</div>
							<div class="col-md-10">
								@if (isset($old_data['kobetsu_kyoka'])&&$old_data['kobetsu_kyoka']!='')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control">
									教科【{{ $old_data['kobetsu_kyoka'] }}】
									</div>
								</div>
								@endif										

								@if (isset($old_data['kobetsu_kobetsushidokosu'])&&$old_data['kobetsu_kobetsushidokosu']=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control" style="margin-right:30px;">
									個別指導コース
									</div>
									@if (isset($old_data['kobetsu_kobetsushidokosu_bun'])&&$old_data['kobetsu_kobetsushidokosu_bun']=='40')
									40分
									@elseif (isset($old_data['kobetsu_kobetsushidokosu_bun'])&&$old_data['kobetsu_kobetsushidokosu_bun']=='80')
									80分
									@endif
								</div>
								@endif		
								
								@if (isset($old_data['kobetsu_mantsumankosu'])&&$old_data['kobetsu_mantsumankosu']=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control" style="margin-right:30px;">
									マンツーマンコース
									</div>
									@if (isset($old_data['kobetsu_mantsumankosu_kaisu'])&&$old_data['kobetsu_mantsumankosu_kaisu']!='')
									週【{{ $old_data['kobetsu_mantsumankosu_kaisu'] }}】回
									@endif
								</div>
								@endif								

							</div>
						</div>
						<!--/個別指導部-->						

						<!--高校生コース-->
						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('class_shogakubu','高校生コース',['class'=>'custom-control-label'])}}
							</div>
							<div class="col-md-10">
								@if (isset($old_data['kokosei_jishushitsuriyo'])&&$old_data['kokosei_jishushitsuriyo']=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control">
									自習室利用＋チュータリング計画学習
									</div>
								</div>
								@endif										

								@if (isset($old_data['kokosei_shudanshido'])&&$old_data['kokosei_shudanshido']=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control" style="margin-right:30px;">
									集団指導コース
									</div>
									@if (isset($old_data['kokosei_shudanshido_label'])&&$old_data['kokosei_shudanshido_label']!='')
										（{{$old_data['kokosei_shudanshido_label'] }}）
									@endif
								</div>
								@endif		
								
								@if (isset($old_data['kokosei_kobetsushidokosu'])&&$old_data['kokosei_kobetsushidokosu']=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control" style="margin-right:30px;">
									個別指導コース
									</div>
									@if (isset($old_data['kokosei_kobetsushidokosu_kaisu'])&&$old_data['kokosei_kobetsushidokosu_kaisu']!='')
									<div class="custom-control" style="margin-right:30px">
									週【{{ $old_data['kokosei_kobetsushidokosu_kaisu'] }}】回
									</div>
									@endif
									@if (isset($old_data['kokosei_kobetsushidokosu_kyoka'])&&$old_data['kokosei_kobetsushidokosu_kyoka']!='')
									教科【{{ $old_data['kokosei_kobetsushidokosu_kyoka'] }}】
									@endif
								</div>
								@endif								

							</div>
						</div>
						<!--/高校生コース-->

						<!--その他コース-->
						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('class_shogakubu','その他コース',['class'=>'custom-control-label'])}}
							</div>
							<div class="col-md-10">
								@if (isset($old_data['etc_ondemando'])&&$old_data['etc_ondemando']=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control">
									オンデマンド
									</div>
								</div>
								@endif
								@if (isset($old_data['etc_eikaiwa'])&&$old_data['etc_eikaiwa']=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control">
									英会話
									</div>
								</div>
								@endif
								@if (isset($old_data['etc_programing'])&&$old_data['etc_programing']=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control">
									プログラミング
									</div>
								</div>
								@endif
								@if (isset($old_data['etc_soroban'])&&$old_data['etc_soroban']=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control" style="margin-right:30px;">
									そろばん
									</div>
									@if (isset($old_data['etc_soroban_kaisu'])&&$old_data['etc_soroban_kaisu']!='')
									<div class="custom-flex">
										週（{{ $old_data['etc_soroban_kaisu'] }}）回
									</div>
									@endif
								</div>
								@endif

							</div>
						</div>
						<!--/その他コース-->

						<hr>
						<div class="form-group row">
							<div class="col-md-12">
								<h4>3. その他</h4>
							</div>
						</div>

						<!--端末機器レンタル-->
						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('inputName','端末機器レンタル')}}
							</div>
							<div class="col-md-10">
								<div class="custom-flex">
									@if (isset($old_data['tanmatsukiki_yes'])&&$old_data['tanmatsukiki_yes']=='1')
									<div class="custom-control" style="margin-right:30px;">
										する（￥550/月）
									</div>
									@endif
									@if (isset($old_data['tanmatsukiki_no'])&&$old_data['tanmatsukiki_no']=='1')
									<div class="custom-control">
										しない
									</div>
									@endif

								</div>
							</div>
						</div>
						<!--/端末機器レンタル-->

						<!--進学ゼミナール-->
						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('inputName','進学ゼミナールを何でお知りになりましたか')}}
							</div>
							<div class="col-md-10">
								<div class="custom-flex">
									@if (isset($old_data['shingakuzemina_howknow_1'])&&$old_data['shingakuzemina_howknow_1']=='1')
									<div class="custom-control" style="margin-right:30px;">
										１.新聞のチラシ
									</div>
									@endif
									@if (isset($old_data['shingakuzemina_howknow_2'])&&$old_data['shingakuzemina_howknow_2']=='1')
									<div class="custom-control" style="margin-right:30px;">
										２.ポスト投函チラシ
									</div>
									@endif
									@if (isset($old_data['shingakuzemina_howknow_3'])&&$old_data['shingakuzemina_howknow_3']=='1')
									<div class="custom-control" style="margin-right:30px;">
										３.配布されたチラシ
									</div>
									@endif
									@if (isset($old_data['shingakuzemina_howknow_4'])&&$old_data['shingakuzemina_howknow_4']=='1')
									<div class="custom-control" style="margin-right:30px;">
										４.公式HP
									</div>
									@endif
									@if (isset($old_data['shingakuzemina_howknow_5'])&&$old_data['shingakuzemina_howknow_5']=='1')
									<div class="custom-control" style="margin-right:30px;">
										５.塾ナビ・塾シル
									</div>
									@endif
									@if (isset($old_data['shingakuzemina_howknow_6'])&&$old_data['shingakuzemina_howknow_6']=='1')
									<div class="custom-control" style="margin-right:30px;">
										６.以前通ってた
									</div>
									@endif
									@if (isset($old_data['shingakuzemina_howknow_7'])&&$old_data['shingakuzemina_howknow_7']=='1')
									<div class="custom-control" style="margin-right:30px;">
										７.昔に体験等受けた
									</div>
									@endif
									@if (isset($old_data['shingakuzemina_howknow_8'])&&$old_data['shingakuzemina_howknow_8']=='1')
									<div class="custom-control" style="margin-right:30px;">
										８.教室を見て
									</div>
									@endif
									@if (isset($old_data['shingakuzemina_howknow_9'])&&$old_data['shingakuzemina_howknow_9']=='1')
									<div class="custom-control custom-flex" style="margin-right:30px;">
										９.友人・知人の紹介
										@if (isset($old_data['shingakuzemina_howknow_9_label'])&&$old_data['shingakuzemina_howknow_9_label']!='')
										（{{ $old_data['shingakuzemina_howknow_9_label'] }}様）
										@endif
									</div>
									@endif
									@if (isset($old_data['shingakuzemina_howknow_10'])&&$old_data['shingakuzemina_howknow_10']=='1')
									<div class="custom-control" style="margin-right:30px;">
										10.SNSで知った
									</div>
									@endif
									@if (isset($old_data['shingakuzemina_howknow_11'])&&$old_data['shingakuzemina_howknow_11']=='1')
									<div class="custom-control custom-flex">
										11.その他
										@if (isset($old_data['shingakuzemina_howknow_11_label'])&&$old_data['shingakuzemina_howknow_11_label']!='')
										（{{ $old_data['shingakuzemina_howknow_11_label'] }}）
										@endif
									</div>
									@endif

								</div>
							</div>
						</div>
						<!--/進学ゼミナール-->

						<!--端末機器レンタル-->
						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('inputName','進学ゼミナールにお決め頂いた理由と求めること')}}
							</div>
							<div class="col-md-10">
								<div class="custom-control">
									{{Form::label('shingakuzemina_reason','理由:',['class'=>'custom-control-label'])}}
									@if (isset($old_data['shingakuzemina_howknow_11_label'])&&$old_data['shingakuzemina_howknow_11_label']!='')
									<?php echo nl2br($old_data['shingakuzemina_reason']) ?>
									@endif
								</div>

								<div class="custom-control">
									{{Form::label('shingakuzemina_require','求めること：',['class'=>'custom-control-label'])}}
									@if (isset($old_data['shingakuzemina_require'])&&$old_data['shingakuzemina_require']!='')
									<?php echo nl2br($old_data['shingakuzemina_require']) ?>
									@endif
								</div>
							</div>
						</div>
						
						

						<!--ボタンブロック-->
						<div class="form-group row">
							<div class="col-md-4">
								{{Form::submit('次へ',['class'=>'btn btn-primary'])}}
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<!--/ボタンブロック-->
@endsection
