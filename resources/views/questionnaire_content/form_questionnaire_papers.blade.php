@extends("layouts.app")
@section('content')
@push('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/i18n/ja.js"></script>
<script>
$(function() {
    $('.select_search').select2({
        language: "ja",
        width: '200px'
    });
});
</script>
@endpush
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">アンケート用紙PDFダウンロード</div>
                <div class="panel-body">
                    <br />
                    <a href="{{ url('/shinzemi/questionnaire_content') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif


                    <form method="GET" action="{{ route('questionnaire_content.export_questionnaire_papers') }}" class="form-horizontal" target="_blank">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="name" class="col-md-4 control-label">アンケート内容: </label>
                            <div class="col-md-6">
                                <select class="form-control" name="questionnaire_content_id" id="questionnaire_content_id">
                                    @foreach ($questionnaire_contents as $questionnaire_content)
                                    <option value="{{ $questionnaire_content->id }}">{{ $questionnaire_content->title }}</option>
                                    @endforeach
                                </select>

                                <div class="text-danger">集計・確定済のアンケート内容は表示されません。</div>

                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name_short" class="col-md-4 control-label">校舎: </label>
                            <div class="col-md-6">

                                <select class="form-control select_search" name="school_building_id" id="school_building_id">
                                    @foreach ($school_buildings as $school_building)
                                    <option value="{{ $school_building->id }}">{{ $school_building->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-md-4 control-label">枚数: </label>
                            <div class="col-md-6">
                                <input type="number" class="form-control" name="number_of_sheets" id="number_of_sheets" value="0" min="0">
                            </div>
                        </div>

                        <div class="form-group" style="padding-top: 16px;">
                            <div class="col-md-offset-4 col-md-4">
                                <input class="btn btn-primary" type="submit" value="ダウンロード">
                            </div>
                        </div>

                    </form>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection