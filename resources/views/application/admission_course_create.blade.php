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
		$("#frm").attr("action", "{{ route('application.admission_student_create') }}");
		$("#frm").submit();
	}
</script>
@endpush
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">選択コース</div>
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
					<p>※該当項目にチェックをつけてください</p>
					<form method="POST" id="frm" action="{{route('application.admission_others_create')}}" class="form-horizontal">
						<input type="hidden" name="surname" value="{{ isset($old_data['surname'])?$old_data['surname']:'' }}">
						<input type="hidden" name="name" value="{{ isset($old_data['name'])?$old_data['name']:'' }}">
						<input type="hidden" name="surname_kana" value="{{ isset($old_data['surname_kana'])?$old_data['surname_kana']:'' }}">
						<input type="hidden" name="name_kana" value="{{ isset($old_data['name_kana'])?$old_data['name_kana']:'' }}">
						<input type="hidden" name="birthdate" value="{{ isset($old_data['birthdate'])?$old_data['birthdate']:'' }}">
						<input type="hidden" name="gender" value="{{ isset($old_data['gender'])?$old_data['gender']:'' }}">
						<input type="hidden" name="zip_code" value="{{ isset($old_data['zip_code'])?$old_data['zip_code']:'' }}">
						<input type="hidden" name="address1" value="{{ isset($old_data['address1'])?$old_data['address1']:'' }}">
						<input type="hidden" name="address2" value="{{ isset($old_data['address2'])?$old_data['address2']:'' }}">
						<input type="hidden" name="address3" value="{{ isset($old_data['address3'])?$old_data['address3']:'' }}">
						<input type="hidden" name="phone1" value="{{ isset($old_data['phone1'])?$old_data['phone1']:'' }}">
						<input type="hidden" name="phone2" value="{{ isset($old_data['phone2'])?$old_data['phone2']:'' }}">
						<input type="hidden" name="email" value="{{ isset($old_data['email'])?$old_data['email']:'' }}">
						<input type="hidden" name="grade" value="{{ isset($old_data['grade'])?$old_data['grade']:'' }}">
						<input type="hidden" name="school_id" value="{{ isset($old_data['school_id'])?$old_data['school_id']:'' }}">

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

						{{ csrf_field() }}
						<!--クラス指導部-->
						<div class="panel-group" id="sampleAccordion">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">クラス指導部</h3>
								</div>
								<div id="sampleAccordionCollapse1" class="panel-collapse collapse in">
									<div class="panel-body">
										<div class="form-group row">
											<div class="col-md-2">
												{{Form::checkbox('class_shogakubu', '1', isset($old_data['class_shogakubu'])&&$old_data['class_shogakubu']=='1'?true:false, ['class'=>'custom-control-input','id'=>'class_shogakubu'])}}
												{{Form::label('class_shogakubu','小学部 →',['class'=>'custom-control-label'])}}
											</div>
											<div class="col-md-10">
												<div class="custom-flex">
													<div class="custom-control" style="margin-right:30px;">
														{{Form::checkbox('class_shogakubu_3ka', '1', isset($old_data['class_shogakubu_3ka'])&&$old_data['class_shogakubu_3ka']=='1'?true:false, ['class'=>'custom-control-input','id'=>'class_shogakubu_3ka'])}}
														{{Form::label('class_shogakubu_3ka','3科',['class'=>'custom-control-label'])}}
													</div>

													<div class="custom-control" style="margin-right:30px;">
														{{Form::checkbox('class_shogakubu_2ka', '1', isset($old_data['class_shogakubu_2ka'])&&$old_data['class_shogakubu_2ka']=='1'?true:false, ['class'=>'custom-control-input','id'=>'class_shogakubu_2ka'])}}
														{{Form::label('class_shogakubu_2ka','2科',['class'=>'custom-control-label'])}}
													</div>
													<div class="brackets">
														{{Form::text('class_shogakubu_2ka_label', isset($old_data['class_shogakubu_2ka_label'])?$old_data['class_shogakubu_2ka_label']:null, ['class' => 'form-control','style'=>'width:100px'])}}
													</div>

													<div class="custom-control">
														{{Form::checkbox('class_shogakubu_1ka', '1', isset($old_data['class_shogakubu_1ka'])&&$old_data['class_shogakubu_1ka']=='1'?true:false, ['class'=>'custom-control-input','id'=>'class_shogakubu_1ka'])}}
														{{Form::label('class_shogakubu_1ka','1科',['class'=>'custom-control-label'])}}
													</div>
													<div class="brackets">
														{{Form::text('class_shogakubu_1ka_label', isset($old_data['class_shogakubu_1ka_label'])?$old_data['class_shogakubu_1ka_label']:null, ['class' => 'form-control','style'=>'width:100px'])}}
													</div>
												</div>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-md-2">
												{{Form::checkbox('class_chugaku1_2', '1', isset($old_data['class_chugaku1_2'])&&$old_data['class_chugaku1_2']=='1'?true:false, ['class'=>'custom-control-input','id'=>'class_chugaku1_2'])}}
												{{Form::label('class_chugaku1_2','中学1・2年 →',['class'=>'custom-control-label'])}}
											</div>
											<div class="col-md-10">
												<div class="custom-flex">
													<div class="custom-control" style="margin-right:30px;">
														{{Form::checkbox('class_chugaku1_2_p5ka', '1', isset($old_data['class_chugaku1_2_p5ka'])&&$old_data['class_chugaku1_2_p5ka']=='1'?true:false, ['class'=>'custom-control-input','id'=>'class_chugaku1_2_p5ka'])}}
														{{Form::label('class_chugaku1_2_p5ka','P 5科',['class'=>'custom-control-label'])}}
													</div>

													<div class="custom-control">
														{{Form::checkbox('class_chugaku1_2_kyoka', '1', isset($old_data['class_chugaku1_2_kyoka'])&&$old_data['class_chugaku1_2_kyoka']=='1'?true:false, ['class'=>'custom-control-input','id'=>'class_chugaku1_2_kyoka'])}}
														{{Form::label('class_chugaku1_2_kyoka','教科選択',['class'=>'custom-control-label'])}}
													</div>
													<div class="brackets">
														{{Form::text('class_chugaku1_2_kyoka_ka', isset($old_data['class_chugaku1_2_kyoka_ka'])?$old_data['class_chugaku1_2_kyoka_ka']:null, ['class' => 'form-control','style'=>'width:100px', 'id'=>'class_chugaku1_2_kyoka_ka'])}}
													</div>
													{{Form::label('class_chugaku1_2_kyoka_ka','科：',['class'=>'custom-control-label'])}}
													<div class="brackets">
														{{Form::text('class_chugaku1_2_kyoka_label', isset($old_data['class_chugaku1_2_kyoka_label'])?$old_data['class_chugaku1_2_kyoka_label']:null, ['class' => 'form-control','style'=>'width:100px'])}}
													</div>
												</div>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-md-2">
												{{Form::checkbox('class_chugaku3', '1', isset($old_data['class_chugaku3'])&&$old_data['class_chugaku3']=='1'?true:false, ['class'=>'custom-control-input','id'=>'class_chugaku3'])}}
												{{Form::label('class_chugaku3','中学3年 →',['class'=>'custom-control-label'])}}
											</div>
											<div class="col-md-10">
												<div class="custom-flex">
													<div class="custom-control" style="margin-right:30px;">
														{{Form::checkbox('class_chugaku3_p5ka', '1', isset($old_data['class_chugaku3_p5ka'])&&$old_data['class_chugaku3_p5ka']=='1'?true:false, ['class'=>'custom-control-input','id'=>'class_chugaku3_p5ka'])}}
														{{Form::label('class_chugaku3_p5ka','P 5科',['class'=>'custom-control-label'])}}
													</div>

													<div class="custom-control" style="margin-right:30px;">
														{{Form::checkbox('class_chugaku3_s5ka', '1', isset($old_data['class_chugaku3_s5ka'])&&$old_data['class_chugaku3_s5ka']=='1'?true:false, ['class'=>'custom-control-input','id'=>'class_chugaku3_s5ka'])}}
														{{Form::label('class_chugaku3_s5ka','S 5科',['class'=>'custom-control-label'])}}
													</div>

													<div class="custom-control">
														{{Form::checkbox('class_chugaku3_kyoka', '1', isset($old_data['class_chugaku3_kyoka'])&&$old_data['class_chugaku3_kyoka']=='1'?true:false, ['class'=>'custom-control-input','id'=>'class_chugaku3_kyoka'])}}
														{{Form::label('class_chugaku3_kyoka','教科選択',['class'=>'custom-control-label'])}}
													</div>
													<div class="brackets">
														{{Form::text('class_chugaku3_kyoka_label', isset($old_data['class_chugaku3_kyoka_label'])?$old_data['class_chugaku3_kyoka_label']:null, ['class' => 'form-control','style'=>'width:100px'])}}
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
						<!--/クラス指導部-->

						<!--個別指導部-->
						<div class="panel-group" id="sampleAccordion">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">個別指導部</h3>
								</div>
								<div id="sampleAccordionCollapse1" class="panel-collapse collapse in">
									<div class="panel-body">
										<div class="form-group row">
											<div class="col-md-12">
												<div class="custom-flex">
													{{Form::label('kobetsu_kyoka','教科',['class'=>'custom-control-label'])}}
													<div class="brackets">
														{{Form::text('kobetsu_kyoka', isset($old_data['kobetsu_kyoka'])?$old_data['kobetsu_kyoka']:null, ['class' => 'form-control','style'=>'width:100px'])}}
													</div>
												</div>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-md-2">
												{{Form::checkbox('kobetsu_kobetsushidokosu', '1', isset($old_data['kobetsu_kobetsushidokosu'])&&$old_data['kobetsu_kobetsushidokosu']=='1'?true:false, ['class'=>'custom-control-input','id'=>'kobetsu_kobetsushidokosu'])}}
												{{Form::label('kobetsu_kobetsushidokosu','個別指導コース',['class'=>'custom-control-label'])}}
											</div>
											<div class="col-md-10">
												<div class="custom-flex">
													<div class="custom-control custom-radio" style="margin-right:30px;">
														{{Form::radio('kobetsu_kobetsushidokosu_bun', '40', isset($old_data['kobetsu_kobetsushidokosu_bun'])&&$old_data['kobetsu_kobetsushidokosu_bun']=='40'?true:false, ['class'=>'custom-control-input','id'=>'kobetsu_kobetsushidokosu_bun_40'])}}
														{{Form::label('kobetsu_kobetsushidokosu_bun_40','40分',['class'=>'custom-control-label'])}}
													</div>
													<div class="custom-control custom-radio">
														{{Form::radio('kobetsu_kobetsushidokosu_bun', '80', isset($old_data['kobetsu_kobetsushidokosu_bun'])&&$old_data['kobetsu_kobetsushidokosu_bun']=='80'?true:false, ['class'=>'custom-control-input','id'=>'kobetsu_kobetsushidokosu_bun_80'])}}
														{{Form::label('kobetsu_kobetsushidokosu_bun_80','80分',['class'=>'custom-control-label'])}}
													</div>
												</div>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-md-2">
												{{Form::checkbox('kobetsu_mantsumankosu', '1', isset($old_data['kobetsu_mantsumankosu'])&&$old_data['kobetsu_mantsumankosu']=='1'?true:false, ['class'=>'custom-control-input','id'=>'kobetsu_mantsumankosu'])}}
												{{Form::label('kobetsu_mantsumankosu','マンツーマンコース',['class'=>'custom-control-label'])}}
											</div>
											<div class="col-md-10">
												<div class="custom-flex">
													{{Form::label('kobetsu_mantsumankosu_week','週',['class'=>'custom-control-label'])}}
													<div class="brackets">
														{{Form::text('kobetsu_mantsumankosu_kaisu', isset($old_data['kobetsu_mantsumankosu_kaisu'])?$old_data['kobetsu_mantsumankosu_kaisu']:null, ['class' => 'form-control','style'=>'width:100px'])}}
													</div>
													{{Form::label('kobetsu_mantsumankosu_kaisu','回',['class'=>'custom-control-label'])}}
												</div>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
						<!--/個別指導部-->

						<!--高校生コース-->
						<div class="panel-group" id="sampleAccordion">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">高校生コース</h3>
								</div>
								<div id="sampleAccordionCollapse1" class="panel-collapse collapse in">
									<div class="panel-body">
										<div class="form-group row">
											<div class="col-md-12">
												{{Form::checkbox('kokosei_jishushitsuriyo', '1', isset($old_data['kokosei_jishushitsuriyo'])&&$old_data['kokosei_jishushitsuriyo']=='1'?true:false, ['class'=>'custom-control-input','id'=>'kokosei_jishushitsuriyo'])}}
												{{Form::label('kokosei_jishushitsuriyo','自習室利用＋チュータリング計画学習',['class'=>'custom-control-label'])}}
											</div>

										</div>
										<div class="form-group row">
											<div class="col-md-2">
												{{Form::checkbox('kokosei_shudanshido', '1', isset($old_data['kokosei_shudanshido'])&&$old_data['kokosei_shudanshido']=='1'?true:false, ['class'=>'custom-control-input','id'=>'kokosei_shudanshido'])}}
												{{Form::label('kokosei_shudanshido','集団指導コース',['class'=>'custom-control-label'])}}
											</div>
											<div class="col-md-10">
												<div class="custom-flex">
													<div class="round-brackets">
														{{Form::text('kokosei_shudanshido_label', isset($old_data['kokosei_shudanshido_label'])?$old_data['kokosei_shudanshido_label']:null, ['class' => 'form-control','style'=>'width:100px'])}}
													</div>
												</div>
											</div>
										</div>
										<div class="form-group row">
											<div class="col-md-2">
												{{Form::checkbox('kokosei_kobetsushidokosu', '1', isset($old_data['kokosei_kobetsushidokosu'])&&$old_data['kokosei_kobetsushidokosu']=='1'?true:false, ['class'=>'custom-control-input','id'=>'kokosei_kobetsushidokosu'])}}
												{{Form::label('kokosei_kobetsushidokosu','個別指導コース',['class'=>'custom-control-label'])}}
											</div>
											<div class="col-md-10">
												<div class="custom-flex">
													{{Form::label('kokosei_kobetsushidokosu_week','週',['class'=>'custom-control-label'])}}
													<div class="brackets">
														{{Form::text('kokosei_kobetsushidokosu_kaisu', isset($old_data['kokosei_kobetsushidokosu_kaisu'])?$old_data['kokosei_kobetsushidokosu_kaisu']:null, ['class' => 'form-control','style'=>'width:100px'])}}
													</div>
													{{Form::label('kokosei_kobetsushidokosu_kaisu','回',['class'=>'custom-control-label', 'style'=>'margin-right:30px'])}}

													<div class="custom-control">
														{{Form::label('kokosei_kobetsushidokosu_kyoka','教科',['class'=>'custom-control-label'])}}
													</div>
													<div class="brackets">
														{{Form::text('kokosei_kobetsushidokosu_kyoka', isset($old_data['kokosei_kobetsushidokosu_kyoka'])?$old_data['kokosei_kobetsushidokosu_kyoka']:null, ['class' => 'form-control','style'=>'width:100px'])}}
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--/高校生コース-->

						<!--その他コース-->
						<div class="panel-group" id="sampleAccordion">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">その他コース</h3>
								</div>
								<div id="sampleAccordionCollapse1" class="panel-collapse collapse in">
									<div class="panel-body">
										<div class="form-group row">
											<div class="col-md-12">
												{{Form::checkbox('etc_ondemando', '1', isset($old_data['etc_ondemando'])&&$old_data['etc_ondemando']=='1'?true:false, ['class'=>'custom-control-input','id'=>'etc_ondemando'])}}
												{{Form::label('etc_ondemando','オンデマンド',['class'=>'custom-control-label'])}}
											</div>
										</div>
										<div class="form-group row">
											<div class="col-md-12">
												{{Form::checkbox('etc_eikaiwa', '1', isset($old_data['etc_eikaiwa'])&&$old_data['etc_eikaiwa']=='1'?true:false, ['class'=>'custom-control-input','id'=>'etc_eikaiwa'])}}
												{{Form::label('etc_eikaiwa','英会話',['class'=>'custom-control-label'])}}
											</div>
										</div>
										<div class="form-group row">
											<div class="col-md-12">
												{{Form::checkbox('etc_programing', '1', isset($old_data['etc_programing'])&&$old_data['etc_programing']=='1'?true:false, ['class'=>'custom-control-input','id'=>'etc_programing'])}}
												{{Form::label('etc_programing','プログラミング',['class'=>'custom-control-label'])}}
											</div>
										</div>
										<div class="form-group row">
											<div class="col-md-2">
												{{Form::checkbox('etc_soroban', '1', isset($old_data['etc_soroban'])&&$old_data['etc_soroban']=='1'?true:false, ['class'=>'custom-control-input','id'=>'etc_soroban'])}}
												{{Form::label('etc_soroban','そろばん',['class'=>'custom-control-label'])}}
											</div>
											<div class="col-md-10">
												<div class="custom-flex">
													{{Form::label('etc_soroban_week','週',['class'=>'custom-control-label'])}}
													<div class="brackets">
														{{Form::text('etc_soroban_kaisu', isset($old_data['etc_soroban_kaisu'])?$old_data['etc_soroban_kaisu']:null, ['class' => 'form-control','style'=>'width:100px'])}}
													</div>
													{{Form::label('etc_soroban_kaisu','回',['class'=>'custom-control-label'])}}
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--/その他コース-->


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
