@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
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
                    		<td>Contact Name</td>
                    		<td>User email</td>
                    		<td>Address</td>
                    	</tr>
                    	@foreach($data as $d)
                    		<tr>
	                    		<td>{{ $d->name }}</td>
	                    		<td>{{ $d->user->email }}</td>
	                    		<td>{{ $d->address }}</td>
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
