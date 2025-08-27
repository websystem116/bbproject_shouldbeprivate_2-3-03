@section('content')
<h1>{{ $title }}</h1>
<form class="form-horizontal" role="form" method="POST" action="{{ route('verifyPhone') }}">
    {{ csrf_field() }}
    <div class="form-group">
        <label name="phone">{{ $postdata['country_code']." ".$postdata['phone'] }}</label>
    </div>
    <div class="form-group">
        <input id="verified_code" type="text" class="form-control" name="verified_code" value="" required>
    </div>
    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
</form>
@endsection