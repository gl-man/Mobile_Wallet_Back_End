@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
					<div>
                    	<h1>Basic Information</h1>
                    	Email : {{ $data->email }} <br>
                    	2Fa Status :@if($data->google2fa_secret == '0') Current Disable @else Current Enable <a href='{{ route('disable2fa',$data->id) }}'>Click here for Disable</a>  @endif<br>
                    	Address : {{ $data->address }} <a href='{{ route('disableadd',$data->id) }}'>Disable</a><br>
                    	Balance : {{ $data->balance }} <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
