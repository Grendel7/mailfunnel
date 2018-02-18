@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">Addresses</div>

                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th>Email</th>
                                <th>Forwarded Messages</th>
                                <th>Rejected Messages</th>
                                <th>Blocked?</th>
                                <th>Created at</th>
                                <th>Updated at</th>
                                <td>&nbsp;</td>
                            </tr>
                            @foreach ($addresses as $address)
                                <tr>
                                    <td>
                                        <a href="{{ route('addresses.edit', $address) }}">
                                            {{ $address->email }}
                                        </a>
                                    </td>
                                    <td>{{ $address->messages()->where('is_rejected', false)->count() }}</td>
                                    <td>{{ $address->messages()->where('is_rejected', true)->count() }}</td>
                                    <td>{{ $address->is_blocked ? 'Yes' : 'No'}}</td>
                                    <td nowrap="nowrap">{{ $address->created_at }}</td>
                                    <td nowrap="nowrap">{{ $address->updated_at }}</td>
                                    <td nowrap="nowrap">
                                        <a href="{{ route('addresses.show', $address) }}" class="btn btn-info btn-sm">Messages</a>
                                    </td>
                                </tr>
                            @endforeach
                            @if($addresses->count() == 0)
                                <tr>
                                    <td colspan="4">No addresses yet!</td>
                                </tr>
                            @endif
                        </table>
                    </div>

                    <div class="card-footer">
                        {{ $addresses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
