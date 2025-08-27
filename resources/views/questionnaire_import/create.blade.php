@extends("layouts.app")
@section("content")

@push('css')
<link href="{{ asset('css/questionnaire_import.css') }}" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
<script>
$(function() {
    $('.select_search').select2({
        language: "ja",
        width: '540px'
    });
});
</script>
@endpush


<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">アンケートインポート</div>
                <div class="panel-body">
                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif

                    <form method="POST" action="{{route('questionnaire_import.store_import')}}" class="form-horizontal" enctype="multipart/form-data">
                        @csrf


                        <div class="form-group">
                            <label for="questionnaire_content_id" class="col-md-4 control-label">アンケート内容: </label>
                            <div class="col-md-6">
                                <select class="form-control" name="questionnaire_content_id" id="questionnaire_content_id">
                                    @foreach ($questionnaire_contents as $questionnaire_content)
                                    <option value="{{$questionnaire_content->id}}">{{ $questionnaire_content->title }}</option>
                                    @endforeach
                                </select>

                                <div class="text-danger">集計・確定済のアンケート内容は表示されません。</div>

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="school_building_id" class="col-md-4 control-label">校舎: </label>
                            <div class="col-md-6">
                                <select class="form-control select_search" name="school_building_id" id="school_building_id">
                                    @foreach ($school_buildings as $school_building)
                                    <option value="{{$school_building->id}}">{{ $school_building->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- file upload -->
                        <div class="form-group">
                            <label for="file" class="col-md-4 control-label">スキャンデータ: </label>
                            <div class="col-md-6">
                                <div id="drop-zone" style="border: 1px solid; padding: 30px;">
                                    <p>ファイルをドラッグ＆ドロップもしくは</p>
                                    <input type="file" name="file" id="file-input">
                                </div>
                                <div class="text-danger">
                                    ※システム上から印刷したアンケートのPDFファイルのみ対応しています。
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-offset-4 col-md-4">
                                <button id="button" class="btn btn-primary" type="submit">登録</button>
                            </div>
                        </div>

                    </form>


                </div>
            </div>
        </div>
    </div>
</div>


<script>
var button = $('#button'),
    spinner = '<span class="spinner"></span>';

button.click(function() {
    if (!button.hasClass('loading')) {
        button.toggleClass('loading').html(spinner);
    } else {
        button.toggleClass('loading').html("Load");
    }
})



















var dropZone = document.getElementById('drop-zone');
var preview = document.getElementById('preview');
var fileInput = document.getElementById('file-input');

dropZone.addEventListener('dragover', function(e) {
    e.stopPropagation();
    e.preventDefault();
    this.style.background = '#e1e7f0';
}, false);

dropZone.addEventListener('dragleave', function(e) {
    e.stopPropagation();
    e.preventDefault();
    this.style.background = '#ffffff';
}, false);

fileInput.addEventListener('change', function() {
    previewFile(this.files[0]);
});

dropZone.addEventListener('drop', function(e) {
    e.stopPropagation();
    e.preventDefault();
    this.style.background = '#ffffff'; //背景色を白に戻す
    var files = e.dataTransfer.files; //ドロップしたファイルを取得
    if (files.length > 1) return alert('アップロードできるファイルは1つだけです。');
    fileInput.files = files; //inputのvalueをドラッグしたファイルに置き換える。
    previewFile(files[0]);
}, false);

function previewFile(file) {
    /* FileReaderで読み込み、プレビュー画像を表示。 */
    var fr = new FileReader();
    fr.readAsDataURL(file);
    fr.onload = function() {
        var img = document.createElement('img');
        img.setAttribute('src', fr.result);
        preview.innerHTML = '';
        preview.appendChild(img);
    };
}
</script>

@endsection