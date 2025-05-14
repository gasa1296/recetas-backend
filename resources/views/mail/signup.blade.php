<x-mail::message>
# Solicitud de acceso de médico para RME


@foreach ($inputs as $key => $input)
    <p>{{$key}}: {{$input}}</p>
@endforeach  


{{ config('app.name') }}
</x-mail::message>