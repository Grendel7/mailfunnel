@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header">Edit {{ $address->email }}</div>

                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <td>Email</td>
                                <td>{{ $address->email }}</td>
                            </tr>
                            <tr>
                                <td>Created at</td>
                                <td>{{ $address->created_at }}</td>
                            </tr>
                            <tr>
                                <td>Updated at</td>
                                <td>{{ $address->created_at }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="card-footer">
                        <form method="post" action="{{ route('addresses.update', ['address' => $address]) }}">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}

                            @if($address->is_blocked)
                                <button class="btn btn-primary" name="is_blocked" value="0">Unblock</button>
                            @else
                                <button class="btn btn-primary" name="is_blocked" value="1">Block</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
