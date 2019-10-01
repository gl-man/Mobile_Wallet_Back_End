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
                    		<td>User email</td>
                    		<td>Transactions Hash</td>
                    		<td>type</td>
                    		<td>amount</td>
                    		<td>Time</td>
                    	</tr>
                    	@foreach($data as $d)
                    		<tr>
	                    		<td>{{ $d->user->email }}</td>
	                    		<td>{{ $d->hash }}</td>
	                    		<td>{{ $d->type }}</td>
	                    		<td>{{ $d->amount }}</td>
	                    		<td>{{ $d->created_at }}</td>
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
