@section('content')
<h1 class="mt-3 mb-5">{{ $title }}</h1>
<form class="form-horizontal" role="form" method="POST" action="{{ route('registerPhone') }}">
    {{ csrf_field() }}
    <div class="input-group mb-3">
        <label for="phone">{{ __('Phone') }}</label>
        <div class="input-group-addon">
            <select name="country_code" style="width: 150px;">
                <option value="">{{ __('Country...') }}</option>
                @foreach($country_list as $c)
                <option value="+{{$c[0]}}" @if ("+".$c[0] == $country_code) selected @endif>(+{{$c[0]}}) {{$c[1]}} ({{$c[2]}})</option>
                @endforeach
            </select>
        </div>
        <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone', $phone) }}" required>
    </div>
    <button type="submit" class="btn btn-primary">{{ __('Send SMS') }}</button>
    @if ($verified_phone == 1)
    <label class="text-danger">{{ __('Phone Number is Authenticated.') }}</label>
    @endif
</form>
@endsection