@extends('app')
@section('title', 'My Dashboard')
@section('content')
<div id="dashboard" class="main">
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">Dashboard</div>

            <div class="panel-body">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                You are logged in!
            </div>
        </div>
    </div>
</div>
@endsection
