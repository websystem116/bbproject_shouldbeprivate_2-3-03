@extends("layouts.app")
@section("content")
@push('scripts')
<script>
	function do_accept(){
		if(confirm("承認しますか？")){
			$("#act").val("accept");
			$("#frm").submit();
		}
	}

	function do_cancel(){
		if(confirm("キャンセルしますか？")){
			$("#act").val("cancel");
			$("#frm").submit();
		}
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
				<div class="panel-heading">申込書詳細</div>
				<div class="panel-body">
					<a href="{{route('application.accept_index')}}" class="btn btn-warning btn-xs">戻る</a>
					<br />
					<br />
					<form method="POST" id="frm" action="{{route('application.accept_process')}}" class="form-horizontal">
						{{ csrf_field() }}
						<input type="hidden" name="edit_id" value="{{ $edit_id }}">
						<input type="hidden" name="act" id="act" value="">

						<h3>入会申込書</h3>
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
								{{ isset($detail_data->surname)?$detail_data->surname:'' }}
								{{ isset($detail_data->name)?$detail_data->name:'' }}

								（{{ isset($detail_data->surname_kana)?$detail_data->surname_kana:'' }}
								{{ isset($detail_data->name_kana)?$detail_data->name_kana:'' }}）
							</div>
							<div class="col-md-2">
								{{Form::label('birthdate','生年月日')}}
							</div>
							<div class="col-md-5">
								{{ isset($detail_data->birthdate)?$detail_data->birthdate:'' }}
							</div>
						</div>
						<!--性別-->
						<div class="form-group row">
							<div class="col-form-label col-md-2">
								{{Form::label('gender_label','性別')}}
							</div>
							<div class="col-md-10">
								@if (isset($detail_data->gender)&&$detail_data->gender=='2')
								女性
								@elseif (isset($detail_data->gender)&&$detail_data->gender=='1')
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
								{{ isset($detail_data->zip_code)?$detail_data->zip_code:'' }}
							</div>
							<!--/郵便番号-->
							<!--住所-->
							<div class="col-md-2">
								{{Form::label('address1','住所⓵')}}<br>
								{{Form::label('address2','住所⓶')}}<br>
								{{Form::label('address3','住所⓷')}}
							</div>
							<div class="col-md-5">
								{{ isset($detail_data->address1)?$detail_data->address1:'' }}<br>
								{{ isset($detail_data->address2)?$detail_data->address2:'' }}<br>
								{{ isset($detail_data->address3)?$detail_data->address3:'' }}
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
								{{ isset($detail_data->phone1)?$detail_data->phone1:'' }}
							</div>
							<div class="col-md-2">
								{{Form::label('phone2','携帯')}}
							</div>
							<div class="col-md-5">
								{{ isset($detail_data->phone2)?$detail_data->phone2:'' }}
							</div>

						</div>
						<!--/保護者連絡先-->

						<!--Eメール-->
						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('email','入退室連絡用Eメール')}}
							</div>
							<div class="col-md-5">
								{{ isset($detail_data->email)?$detail_data->email:'' }}
							</div>
						</div>
						<!--/Eメール-->

						<!--現在学年-->
						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('grade','現在学年')}}
							</div>
							<div class="col-md-3">
								{{ list_val(config('const.school_year'),isset($detail_data->grade)?$detail_data->grade:null) }}
							</div>
							<div class="col-md-2">
								{{Form::label('school','学校')}}
							</div>
							<div class="col-md-5">
								{{ list_val($schools_select_list,isset($detail_data->school_id)?$detail_data->school_id:null) }}
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
								@if (isset($detail_data->class_shogakubu)&&$detail_data->class_shogakubu=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control" style="margin-right:30px;">
									小学部 →
									</div>
									@if (isset($detail_data->class_shogakubu_3ka)&&$detail_data->class_shogakubu_3ka=='1')
									<div class="custom-control" style="margin-right:30px;">
										3科
									</div>
									@endif

									@if (isset($detail_data->class_shogakubu_2ka)&&$detail_data->class_shogakubu_2ka=='1')
									<div class="custom-control" style="margin-right:30px;">
										2科<?php if(isset($detail_data->class_shogakubu_2ka_label)&&$detail_data->class_shogakubu_2ka_label!=''){ ?>【{{ $detail_data->class_shogakubu_2ka_label }}】<?php } ?>
									</div>
									@endif

									@if (isset($detail_data->class_shogakubu_1ka)&&$detail_data->class_shogakubu_1ka=='1')
									<div class="custom-control">
										1科<?php if (isset($detail_data->class_shogakubu_1ka_label)&&$detail_data->class_shogakubu_1ka_label!=''){ ?>【{{ $detail_data->class_shogakubu_1ka_label }}】<?php } ?>
									</div>
									@endif
								</div>
								@endif										

								@if (isset($detail_data->class_chugaku1_2)&&$detail_data->class_chugaku1_2=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control" style="margin-right:30px;">
									中学1・2年 →
									</div>
									@if (isset($detail_data->class_chugaku1_2_p5ka)&&$detail_data->class_chugaku1_2_p5ka=='1')
									<div class="custom-control" style="margin-right:30px;">
										P 5科
									</div>
									@endif

									@if (isset($detail_data->class_chugaku1_2_kyoka)&&$detail_data->class_chugaku1_2_kyoka=='1')
									<div class="custom-control">
										教科選択
										<?php if (isset($detail_data->class_chugaku1_2_kyoka_ka)&&$detail_data->class_chugaku1_2_kyoka_ka!=''){ ?>
										【{{ $detail_data->class_chugaku1_2_kyoka_ka }}】科：<?php } ?><?php 
										if (isset($detail_data->class_chugaku1_2_kyoka_label)&&$detail_data->class_chugaku1_2_kyoka_label!=''){?>【{{ $detail_data->class_chugaku1_2_kyoka_label }}】<?php } ?>
									</div>
									@endif

								</div>
								@endif		
								
								@if (isset($detail_data->class_chugaku3)&&$detail_data->class_chugaku3=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control" style="margin-right:30px;">
									中学3年 →
									</div>

									@if (isset($detail_data->class_chugaku3_p5ka)&&$detail_data->class_chugaku3_p5ka=='1')
									<div class="custom-control" style="margin-right:30px;">
										P 5科
									</div>
									@endif
									@if (isset($detail_data->class_chugaku3_s5ka)&&$detail_data->class_chugaku3_s5ka=='1')
									<div class="custom-control" style="margin-right:30px;">
										S 5科
									</div>
									@endif
									@if (isset($detail_data->class_chugaku3_kyoka)&&$detail_data->class_chugaku3_kyoka=='1')
									<div class="custom-control">
										教科選択
										@if (isset($detail_data->class_chugaku3_kyoka_label)&&$detail_data->class_chugaku3_kyoka_label!='')
										【{{ $detail_data->class_chugaku3_kyoka_label }}】
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
								@if (isset($detail_data->kobetsu_kyoka)&&$detail_data->kobetsu_kyoka!='')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control">
									教科【{{ $detail_data->kobetsu_kyoka }}】
									</div>
								</div>
								@endif										

								@if (isset($detail_data->kobetsu_kobetsushidokosu)&&$detail_data->kobetsu_kobetsushidokosu=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control" style="margin-right:30px;">
									個別指導コース
									</div>
									@if (isset($detail_data->kobetsu_kobetsushidokosu_bun)&&$detail_data->kobetsu_kobetsushidokosu_bun=='40')
									40分
									@elseif (isset($detail_data->kobetsu_kobetsushidokosu_bun)&&$detail_data->kobetsu_kobetsushidokosu_bun=='80')
									80分
									@endif
								</div>
								@endif		
								
								@if (isset($detail_data->kobetsu_mantsumankosu)&&$detail_data->kobetsu_mantsumankosu=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control" style="margin-right:30px;">
									マンツーマンコース
									</div>
									@if (isset($detail_data->kobetsu_mantsumankosu_kaisu)&&$detail_data->kobetsu_mantsumankosu_kaisu!='')
									週【{{ $detail_data->kobetsu_mantsumankosu_kaisu }}】回
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
								@if (isset($detail_data->kokosei_jishushitsuriyo)&&$detail_data->kokosei_jishushitsuriyo=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control">
									自習室利用＋チュータリング計画学習
									</div>
								</div>
								@endif										

								@if (isset($detail_data->kokosei_shudanshido)&&$detail_data->kokosei_shudanshido=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control" style="margin-right:30px;">
									集団指導コース
									</div>
									@if (isset($detail_data->kokosei_shudanshido_label)&&$detail_data->kokosei_shudanshido_label!='')
										（{{$detail_data->kokosei_shudanshido_label }}）
									@endif
								</div>
								@endif		
								
								@if (isset($detail_data->kokosei_kobetsushidokosu)&&$detail_data->kokosei_kobetsushidokosu=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control" style="margin-right:30px;">
									個別指導コース
									</div>
									@if (isset($detail_data->kokosei_kobetsushidokosu_kaisu)&&$detail_data->kokosei_kobetsushidokosu_kaisu!='')
									<div class="custom-control" style="margin-right:30px">
									週【{{ $detail_data->kokosei_kobetsushidokosu_kaisu }}】回
									</div>
									@endif
									@if (isset($detail_data->kokosei_kobetsushidokosu_kyoka)&&$detail_data->kokosei_kobetsushidokosu_kyoka!='')
									教科【{{ $detail_data->kokosei_kobetsushidokosu_kyoka }}】
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
								@if (isset($detail_data->etc_ondemando)&&$detail_data->etc_ondemando=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control">
									オンデマンド
									</div>
								</div>
								@endif
								@if (isset($detail_data->etc_eikaiwa)&&$detail_data->etc_eikaiwa=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control">
									英会話
									</div>
								</div>
								@endif
								@if (isset($detail_data->etc_programing)&&$detail_data->etc_programing=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control">
									プログラミング
									</div>
								</div>
								@endif
								@if (isset($detail_data->etc_soroban)&&$detail_data->etc_soroban=='1')
								<div class="custom-flex" style="margin-bottom:10px">
									<div class="custom-control" style="margin-right:30px;">
									そろばん
									</div>
									@if (isset($detail_data->etc_soroban_kaisu)&&$detail_data->etc_soroban_kaisu!='')
									<div class="custom-flex">
										週（{{ $detail_data->etc_soroban_kaisu }}）回
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
									@if (isset($detail_data->tanmatsukiki_yes)&&$detail_data->tanmatsukiki_yes=='1')
									<div class="custom-control" style="margin-right:30px;">
										する（￥550/月）
									</div>
									@endif
									@if (isset($detail_data->tanmatsukiki_no)&&$detail_data->tanmatsukiki_no=='1')
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
									@if (isset($detail_data->shingakuzemina_howknow_1)&&$detail_data->shingakuzemina_howknow_1=='1')
									<div class="custom-control" style="margin-right:30px;">
										１.新聞のチラシ
									</div>
									@endif
									@if (isset($detail_data->shingakuzemina_howknow_2)&&$detail_data->shingakuzemina_howknow_2=='1')
									<div class="custom-control" style="margin-right:30px;">
										２.ポスト投函チラシ
									</div>
									@endif
									@if (isset($detail_data->shingakuzemina_howknow_3)&&$detail_data->shingakuzemina_howknow_3=='1')
									<div class="custom-control" style="margin-right:30px;">
										３.配布されたチラシ
									</div>
									@endif
									@if (isset($detail_data->shingakuzemina_howknow_4)&&$detail_data->shingakuzemina_howknow_4=='1')
									<div class="custom-control" style="margin-right:30px;">
										４.公式HP
									</div>
									@endif
									@if (isset($detail_data->shingakuzemina_howknow_5)&&$detail_data->shingakuzemina_howknow_5=='1')
									<div class="custom-control" style="margin-right:30px;">
										５.塾ナビ・塾シル
									</div>
									@endif
									@if (isset($detail_data->shingakuzemina_howknow_6)&&$detail_data->shingakuzemina_howknow_6=='1')
									<div class="custom-control" style="margin-right:30px;">
										６.以前通ってた
									</div>
									@endif
									@if (isset($detail_data->shingakuzemina_howknow_7)&&$detail_data->shingakuzemina_howknow_7=='1')
									<div class="custom-control" style="margin-right:30px;">
										７.昔に体験等受けた
									</div>
									@endif
									@if (isset($detail_data->shingakuzemina_howknow_8)&&$detail_data->shingakuzemina_howknow_8=='1')
									<div class="custom-control" style="margin-right:30px;">
										８.教室を見て
									</div>
									@endif
									@if (isset($detail_data->shingakuzemina_howknow_9)&&$detail_data->shingakuzemina_howknow_9=='1')
									<div class="custom-control custom-flex" style="margin-right:30px;">
										９.友人・知人の紹介
										@if (isset($detail_data->shingakuzemina_howknow_9_label)&&$detail_data->shingakuzemina_howknow_9_label!='')
										（{{ $detail_data->shingakuzemina_howknow_9_label }}様）
										@endif
									</div>
									@endif
									@if (isset($detail_data->shingakuzemina_howknow_10)&&$detail_data->shingakuzemina_howknow_10=='1')
									<div class="custom-control" style="margin-right:30px;">
										10.SNSで知った
									</div>
									@endif
									@if (isset($detail_data->shingakuzemina_howknow_11)&&$detail_data->shingakuzemina_howknow_11=='1')
									<div class="custom-control custom-flex">
										11.その他
										@if (isset($detail_data->shingakuzemina_howknow_11_label)&&$detail_data->shingakuzemina_howknow_11_label!='')
										（{{ $detail_data->shingakuzemina_howknow_11_label }}）
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
									@if (isset($detail_data->shingakuzemina_howknow_11_label)&&$detail_data->shingakuzemina_howknow_11_label!='')
									<?php echo nl2br($detail_data->shingakuzemina_reason) ?>
									@endif
								</div>

								<div class="custom-control">
									{{Form::label('shingakuzemina_require','求めること：',['class'=>'custom-control-label'])}}
									@if (isset($detail_data->shingakuzemina_require)&&$detail_data->shingakuzemina_require!='')
									<?php echo nl2br($detail_data->shingakuzemina_require) ?>
									@endif
								</div>
							</div>
						</div>

						<hr>
						<div class="form-group row">
							<div class="col-md-12">
								<h4>4. サイン</h4>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-md-11">
								@if ($sign_filepath)
								<img src="{{ url('/') }}/{{ $sign_filepath }}">
								@endif
							</div>
						</div>
						<hr>

						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('inputName','担当者')}}
							</div>
							<div class="col-md-3">
								{{Form::text('charged_by',$charged_by,['class' => 'form-control','placeholder' => '担当者名を入力'])}}
							</div>
						</div>
						

						<!--ボタンブロック-->
						<div class="form-group row">
							<div class="col-md-4">
								@if (auth()->user()->roles == 1)								
								<button class="btn btn-primary" type="button" onclick="do_accept();">承認</button>
								<button class="btn btn-danger" type="button" onclick="do_cancel();">キャンセル</button>
								@endif
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
