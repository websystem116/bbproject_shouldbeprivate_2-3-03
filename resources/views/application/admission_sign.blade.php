@extends("layouts.app")
@section("content")
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
	#canvas {
		border: 1px solid #000;
		cursor: crosshair;
		width: 90%;
		height: 30vh;
		max-width: 1000px;
		max-height: 300px;
		display:block;
		margin-left:auto;
		margin-right:auto;
		margin-bottom:30px;
	}

</style>
@endpush
@push('scripts')
<script type="text/javascript" src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
<script src="{{ asset('shinzemi/js/student.js') }}"></script>
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
<script>
	function back(){
		$("#frm").attr("action", "{{ route('application.admission_confirm') }}");
		$("#frm").submit();
	}
</script>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">承諾サイン</div>
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
					<form method="POST" id="frm" action="{{route('application.admission_store')}}" class="form-horizontal">
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

						<input type="hidden" name="sign_image" id="sign_image">						
						
						<canvas id="canvas"></canvas>

						<!--ボタンブロック-->
						<div class="form-group row">
							<div class="col-md-4">
								{{Form::button('クリア',['class'=>'btn btn-default', 'id'=>'clear'])}}
								{{Form::button('サイン',['class'=>'btn btn-primary', 'id'=>'sign'])}}
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<!--/ボタンブロック-->
<script>
        document.body.addEventListener('touchmove', function(event) {
            event.preventDefault();
        }, { passive: false });
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
		var rect = canvas.getBoundingClientRect();
		var canvasLeft = rect.left + window.scrollX;
		var canvasTop = rect.top + window.scrollY;

        let drawing = false;

        function resizeCanvas() {
            canvas.width = canvas.clientWidth;
            canvas.height = canvas.clientHeight;
			canvasLeft = rect.left + window.scrollX;
			canvasTop = rect.top + window.scrollY;

        }

        function startDrawing(e) {
            drawing = true;
            ctx.beginPath();

            ctx.moveTo(e.touches ? e.touches[0].clientX - canvasLeft : e.clientX - canvasLeft, 
                       e.touches ? e.touches[0].clientY - canvasTop : e.clientY - canvasTop);
        }

        function draw(e) {
            if (!drawing) return;
            ctx.lineTo(e.touches ? e.touches[0].clientX - canvasLeft : e.clientX - canvasLeft, 
                       e.touches ? e.touches[0].clientY - canvasTop : e.clientY - canvasTop);
            ctx.stroke();
        }

        function stopDrawing() {
            drawing = false;
            ctx.closePath();
        }

        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function saveCanvas() {
            let dataURL = canvas.toDataURL('image/png');
			document.getElementById('sign_image').value = dataURL;
			$("#frm").submit();
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        canvas.addEventListener('touchstart', startDrawing);
        canvas.addEventListener('touchmove', draw);
        canvas.addEventListener('touchend', stopDrawing);
        canvas.addEventListener('touchcancel', stopDrawing);

        document.getElementById('clear').addEventListener('click', clearCanvas);
        document.getElementById('sign').addEventListener('click', saveCanvas);
</script>
@endsection
