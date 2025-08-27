@extends('layouts.app')

@section('content')

@push('scripts')
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
@endpush

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">新規作成</div>
                <div class="panel-body">
                    <a href="{{ url('/shinzemi/school_building') }}" title="Back"><button class="btn btn-warning btn-xs">戻る</button></a>
                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif


                    <form method="POST" action="{{ route('school_building.store') }}" class="form-horizontal">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="name" class="col-md-4 control-label">
                                No:
                                <span class="text-danger">※</span>
                            </label>
                            <div class="col-md-6">
                                <input class="form-control" name="number" type="number" id="number" value="{{ old('number') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="name" class="col-md-4 control-label">
                                校舎名:
                                <span class="text-danger">※</span>
                            </label>
                            <div class="col-md-6">
                                <input class="form-control" name="name" type="text" id="name" value="{{ old('name') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name_short" class="col-md-4 control-label">校舎名（略称）: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="name_short" type="text" id="name_short" value="{{ old('name_short') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="area" class="col-md-4 control-label">地域: </label>
                            <div class="col-md-6">
                                <select class="form-control" name="area" id="area">
                                    <option value="">-</option>
                                    @foreach (config('const.area') as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for=" zipcode" class="col-md-4 control-label"> 郵便番号: </label>
                            <div class="col-md-6">
                                <input class="form-control" name=" zipcode" type="text" id="zipcode" value="{{ old(' zipcode') }}" onKeyUp="AjaxZip3.zip2addr(this,'','address1','address1');">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address1" class="col-md-4 control-label">住所１: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="address1" type="text" id="address1" value="{{ old('address1') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address2" class="col-md-4 control-label">住所２: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="address2" type="text" id="address2" value="{{ old('address2') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address3" class="col-md-4 control-label">住所３: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="address3" type="text" id="address3" value="{{ old('address3') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tel" class="col-md-4 control-label">電話番号: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="tel" type="text" id="tel" value="{{ old('tel') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fax" class="col-md-4 control-label">FAX番号: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="fax" type="text" id="fax" value="{{ old('fax') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-md-4 control-label">E-mailアドレス: </label>
                            <div class="col-md-6">
                                <input class="form-control" name="email" type="text" id="email" value="{{ old('email') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-offset-4 col-md-4">
                                <input class="btn btn-primary" type="submit" value="登録">
                            </div>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection