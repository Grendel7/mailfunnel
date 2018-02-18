@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header">View Message</div>

                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <td>From</td>
                                <td>{{ $message->from }}</td>
                            </tr>
                            <tr>
                                <td>Subject</td>
                                <td>{{ $message->subject }}</td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td>{{ $message->address->email }}</td>
                            </tr>
                            <tr>
                                <td>Received at</td>
                                <td>{{ $message->created_at }}</td>
                            </tr>
                            <tr>
                                <td>Rejected?</td>
                                <td>{{ $message->is_rejected ? 'Yes' : 'No' }}</td>
                            </tr>
                            <tr>
                                <td>Rejection Reason</td>
                                <td>{{ $message->is_rejected ? ucwords(str_replace('_', ' ', $message->reason)) : '-' }}</td>
                            </tr>
                            <tr>
                                <td>Spam Score</td>
                                <td>{{ $message->spam_score }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
