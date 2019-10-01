Your Transaction Type : {{ $name['type'] }} <br>
Your Transaction Hash : {{ $name['hash'] }} <br>
Your Transaction Amount : {{ $name['amount'] }} <br>
Your New Balance : {{ $name['balance'] }} <br>
@if($name['type'] == 'send')
Your Lock Balance : {{ $name['lock'] }} <br>
To Address : {{ $name['address'] }} <br>
@endif