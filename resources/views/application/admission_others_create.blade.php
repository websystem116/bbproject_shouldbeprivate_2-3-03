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
		$("#frm").attr("action", "{{ route('application.admission_course_create') }}");
		$("#frm").submit();
	}
</script>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">その他</div>
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
					<form method="POST" id="frm" action="{{route('application.admission_confirm')}}" class="form-horizontal">
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


						<!--端末機器レンタル-->
						<div class="form-group row">
							<div class="col-md-2">
								{{Form::label('inputName','端末機器レンタル')}}
							</div>
							<div class="col-md-10">
								<div class="custom-flex">
									<div class="custom-control" style="margin-right:30px;">
										{{Form::checkbox('tanmatsukiki_yes', '1', isset($old_data['tanmatsukiki_yes'])&&$old_data['tanmatsukiki_yes']=='1'?true:false, ['class'=>'custom-control-input','id'=>'tanmatsukiki_yes'])}}
										{{Form::label('tanmatsukiki_yes','する（￥550/月）',['class'=>'custom-control-label'])}}
									</div>

									<div class="custom-control">
										{{Form::checkbox('tanmatsukiki_no', '1', isset($old_data['tanmatsukiki_no'])&&$old_data['tanmatsukiki_no']=='1'?true:false, ['class'=>'custom-control-input','id'=>'tanmatsukiki_no'])}}
										{{Form::label('tanmatsukiki_no','しない',['class'=>'custom-control-label'])}}
									</div>

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
								<p>※該当項目にチェックをつけてください【複数可】</p>
								<div class="custom-flex">
									<div class="custom-control" style="margin-right:30px;">
										{{Form::checkbox('shingakuzemina_howknow_1', '1', isset($old_data['shingakuzemina_howknow_1'])&&$old_data['shingakuzemina_howknow_1']=='1'?true:false, ['class'=>'custom-control-input','id'=>'shingakuzemina_howknow_1'])}}
										{{Form::label('shingakuzemina_howknow_1','１.新聞のチラシ',['class'=>'custom-control-label'])}}
									</div>
									<div class="custom-control" style="margin-right:30px;">
										{{Form::checkbox('shingakuzemina_howknow_2', '1', isset($old_data['shingakuzemina_howknow_2'])&&$old_data['shingakuzemina_howknow_2']=='1'?true:false, ['class'=>'custom-control-input','id'=>'shingakuzemina_howknow_2'])}}
										{{Form::label('shingakuzemina_howknow_2','２.ポスト投函チラシ',['class'=>'custom-control-label'])}}
									</div>
									<div class="custom-control" style="margin-right:30px;">
										{{Form::checkbox('shingakuzemina_howknow_3', '1', isset($old_data['shingakuzemina_howknow_3'])&&$old_data['shingakuzemina_howknow_3']=='1'?true:false, ['class'=>'custom-control-input','id'=>'shingakuzemina_howknow_3'])}}
										{{Form::label('shingakuzemina_howknow_3','３.配布されたチラシ',['class'=>'custom-control-label'])}}
									</div>
									<div class="custom-control" style="margin-right:30px;">
										{{Form::checkbox('shingakuzemina_howknow_4', '1', isset($old_data['shingakuzemina_howknow_4'])&&$old_data['shingakuzemina_howknow_4']=='1'?true:false, ['class'=>'custom-control-input','id'=>'shingakuzemina_howknow_4'])}}
										{{Form::label('shingakuzemina_howknow_4','４.公式HP',['class'=>'custom-control-label'])}}
									</div>
									<div class="custom-control" style="margin-right:30px;">
										{{Form::checkbox('shingakuzemina_howknow_5', '1', isset($old_data['shingakuzemina_howknow_5'])&&$old_data['shingakuzemina_howknow_5']=='1'?true:false, ['class'=>'custom-control-input','id'=>'shingakuzemina_howknow_5'])}}
										{{Form::label('shingakuzemina_howknow_5','５.塾ナビ・塾シル',['class'=>'custom-control-label'])}}
									</div>
									<div class="custom-control" style="margin-right:30px;">
										{{Form::checkbox('shingakuzemina_howknow_6', '1', isset($old_data['shingakuzemina_howknow_6'])&&$old_data['shingakuzemina_howknow_6']=='1'?true:false, ['class'=>'custom-control-input','id'=>'shingakuzemina_howknow_6'])}}
										{{Form::label('shingakuzemina_howknow_6','６.以前通ってた',['class'=>'custom-control-label'])}}
									</div>
									<div class="custom-control" style="margin-right:30px;">
										{{Form::checkbox('shingakuzemina_howknow_7', '1', isset($old_data['shingakuzemina_howknow_7'])&&$old_data['shingakuzemina_howknow_7']=='1'?true:false, ['class'=>'custom-control-input','id'=>'shingakuzemina_howknow_7'])}}
										{{Form::label('shingakuzemina_howknow_7','７.昔に体験等受けた',['class'=>'custom-control-label'])}}
									</div>
									<div class="custom-control" style="margin-right:30px;">
										{{Form::checkbox('shingakuzemina_howknow_8', '1', isset($old_data['shingakuzemina_howknow_8'])&&$old_data['shingakuzemina_howknow_8']=='1'?true:false, ['class'=>'custom-control-input','id'=>'shingakuzemina_howknow_8'])}}
										{{Form::label('shingakuzemina_howknow_8','８.教室を見て',['class'=>'custom-control-label'])}}
									</div>
									<div class="custom-control custom-flex" style="margin-right:30px;">
										{{Form::checkbox('shingakuzemina_howknow_9', '1', isset($old_data['shingakuzemina_howknow_9'])&&$old_data['shingakuzemina_howknow_9']=='1'?true:false, ['class'=>'custom-control-input','id'=>'shingakuzemina_howknow_9', 'style'=>'margin-top:0'])}}
										{{Form::label('shingakuzemina_howknow_9','９.友人・知人の紹介',['class'=>'custom-control-label', 'style'=>'margin-bottom:0'])}}
										<div class="sama-brackets">
											{{Form::text('shingakuzemina_howknow_9_label', isset($old_data['shingakuzemina_howknow_9_label'])?$old_data['shingakuzemina_howknow_9_label']:null, ['class' => 'form-control','style'=>'width:100px'])}}
										</div>
									</div>
									<div class="custom-control" style="margin-right:30px;">
										{{Form::checkbox('shingakuzemina_howknow_10', '1', isset($old_data['shingakuzemina_howknow_10'])&&$old_data['shingakuzemina_howknow_10']=='1'?true:false, ['class'=>'custom-control-input','id'=>'shingakuzemina_howknow_10'])}}
										{{Form::label('shingakuzemina_howknow_10','10.SNSで知った',['class'=>'custom-control-label'])}}
									</div>
									<div class="custom-control custom-flex">
										{{Form::checkbox('shingakuzemina_howknow_11', '1', isset($old_data['shingakuzemina_howknow_11'])&&$old_data['shingakuzemina_howknow_11']=='1'?true:false, ['class'=>'custom-control-input','id'=>'shingakuzemina_howknow_11', 'style'=>'margin-top:0'])}}
										{{Form::label('shingakuzemina_howknow_11','11.その他',['class'=>'custom-control-label', 'style'=>'margin-bottom:0'])}}
										<div class="round-brackets">
											{{Form::text('shingakuzemina_howknow_11_label', isset($old_data['shingakuzemina_howknow_11_label'])?$old_data['shingakuzemina_howknow_11_label']:null, ['class' => 'form-control','style'=>'width:100px'])}}
										</div>
									</div>

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
									{{Form::textarea('shingakuzemina_reason', isset($old_data['shingakuzemina_reason'])?$old_data['shingakuzemina_reason']:null, ['class' => 'form-control','id'=>'shingakuzemina_reason', 'rows' => 2])}}
								</div>

								<div class="custom-control">
									{{Form::label('shingakuzemina_require','求めること：',['class'=>'custom-control-label'])}}
									{{Form::textarea('shingakuzemina_require', isset($old_data['shingakuzemina_require'])?$old_data['shingakuzemina_require']:null, ['class' => 'form-control','id'=>'shingakuzemina_require', 'rows' => 2])}}
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
