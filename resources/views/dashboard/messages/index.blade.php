@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">Messages</div>

                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th>From</th>
                                <th>To</th>
                                <th>Subject</th>
                                <th>Received at</th>
                            </tr>
                            @foreach ($messages as $message)
                                <tr>
                                    <td>{{ $message->from }}</td>
                                    <td>
                                        <a href="{{ route('addresses.edit', ['address' => $message->address]) }}">
                                            {{ $message->address->email }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('messages.show', ['message' => $message]) }}">
                                            {{ $message->subject }}
                                        </a>
                                    </td>
                                    <td nowrap="nowrap">{{ $message->created_at }}</td>
                                </tr>
                            @endforeach
                            @if($messages->count() == 0)
                                <tr>
                                    <td colspan="4">No messages yet!</td>
                                </tr>
                            @endif
                        </table>
                    </div>

                    <div class="card-footer">
                        {{ $messages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
