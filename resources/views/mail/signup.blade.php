<x-mail::message>
# Solicitud de acceso de m�dico para RME


@foreach ($inputs as $key => $input)
    {{$key}}: {{$input}}

    
@endforeach  


{{ config('app.name') }}
</x-mail::message>