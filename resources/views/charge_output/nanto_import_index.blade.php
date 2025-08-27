@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">口座振替データインポート（南都銀行）</div>
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

                    <form method="POST" action="{{ route('charge_output.import_nanto') }}" class="form-horizontal" enctype="multipart/form-data">
                        <!-- {{ csrf_field() }} -->
                        @csrf

                        <!-- file upload -->
                        <div class="form-group">
                            <label for="file" class="col-md-4 control-label">インポートデータ: </label>
                            <div class="col-md-6">
                                <input type="file" name="nanto_file" id="file-input">
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