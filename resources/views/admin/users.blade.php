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
                    <table>
                    	<tr>
                    		<td>User Email</td>
                    		<td>Wallet Address</td>
                    		<td>Wallet Balance</td>
                    		<td>2fa Status</td>
                    		<td>Action</td>
                    	</tr>
                    	@foreach($data as $d)
                    		<tr>
	                    		<td>{{ $d->email }}</td>
	                    		<td>{{ $d->address }}</td>
	                    		<td>{{ $d->balance }}</td>
	                    		<td>@if($d->google2fa_secret == '0') Disable @else enable @endif</td>
	                    		<td><a href="{{ route('user',$d->id)}}">Details</a></td>
	                    	</tr>	
                    	@endforeach
                    </table>
                    {!! $data->render() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
