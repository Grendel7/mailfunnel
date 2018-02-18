@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header">
                        Domains

                        @can('create', \App\Models\Domain::class)
                            <a href="{{ route('domains.create') }}" class="btn btn-success btn-sm float-right">
                                Add Domain
                            </a>
                        @endcan
                    </div>

                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th>Domain</th>
                                <th>Addresses</th>
                            </tr>
                            @foreach ($domains as $domain)
                                <tr>
                                    <td>{{ $domain->domain }}</td>
                                    <td>
                                        <a href="{{ route('domains.show', ['domain' => $domain]) }}">
                                            {{ $domain->addresses()->count() }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            @if($domains->count() == 0)
                                <tr>
                                    <td colspan="4">No domains yet!</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
