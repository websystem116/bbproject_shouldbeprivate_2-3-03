
        @extends("layouts.app")
        @section("content")
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">product {{ $product->id }}</div>
                            <div class="panel-body">

                                <a href="{{ url("product") }}" title="Back"><button class="btn btn-warning btn-xs">Back</button></a>
                                <a href="{{ url("product") ."/". $product->id . "/edit" }}" title="Edit product"><button class="btn btn-primary btn-xs">Edit</button></a>
                                <form method="POST" action="/product/{{ $product->id }}" class="form-horizontal" style="display:inline;">
                                        {{ csrf_field() }}
                                        {{ method_field("delete") }}
                                        <button type="submit" class="btn btn-danger btn-xs" title="Delete User" onclick="return confirm('Confirm delete')">
                                        Delete
                                        </button>    
                            </form>
                            <br/>
                            <br/>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
										<tr><th>id</th><td>{{$product->id}} </td></tr>
										<tr><th>name</th><td>{{$product->name}} </td></tr>
										<tr><th>name_short</th><td>{{$product->name_short}} </td></tr>
										<tr><th>description</th><td>{{$product->description}} </td></tr>
										<tr><th>price</th><td>{{$product->price}} </td></tr>
										<tr><th>tax_category</th><td>{{$product->tax_category}} </td></tr>
										<tr><th>division_code</th><td>{{$product->division_code}} </td></tr>
										<tr><th>item_no</th><td>{{$product->item_no}} </td></tr>
										<tr><th>tabulation</th><td>{{$product->tabulation}} </td></tr>

                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
    