<x-mail::message>
# Solicitura de registro  


@foreach ($inputs as $key => $input)
    <p>{{$key}}: {{$input}}</p>
@endforeach  


{{ config('app.name') }}
</x-mail::message>