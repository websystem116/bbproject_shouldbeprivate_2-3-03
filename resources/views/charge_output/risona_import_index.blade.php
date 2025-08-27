@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">口座振替データインポート（りそな銀行）</div>
                <div class="panel-body">
                    <!-- <a href="{{ url('/shinzemi/questionnaire_results_detail') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a> -->
                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif



                    <form method="POST" action="{{ route('charge_output.import_risona') }}" class="form-horizontal" enctype="multipart/form-data">
                        <!-- {{ csrf_field() }} -->
                        @csrf

                        <!-- file upload -->
                        <div class="form-group">
                            <label for="file" class="col-md-4 control-label">インポートデータ: </label>
                            <div class="col-md-6">
                                <input type="file" name="risona_file" id="file-input">
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-offset-4 col-md-4">
                                <button id="button" class="btn btn-primary" type="submit">登録</button>
                                <!-- <input class="btn btn-primary button" type="submit" value="登録"> -->
                            </div>
                        </div>


                    </form>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection