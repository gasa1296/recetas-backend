<x-mail::message>
  <img src="{{ asset('Logo.png') }}" alt="logo" class="img">
  <h1 class="center h1">Receta médica electrónica</h1>
  <p class="center p">Estimado (a), {{$prescription->patient->first_name}} {{$prescription->patient->last_name1}}<br>Le compartimos su receta electrónica en formato pdf de su consulta</p>
  <sub>Este correo electrónico ha sido generado automáticamente por el Sistema de Emisión de Recetas electrónicas por lo que le
  solicitamos no responder a este mensaje, ya que las respuestas a este correo electrónico no serán leídas.
  Está recibiendo este correo electrónico debido a que ha proporcionado la dirección de correo electrónico
  ejemplo@gmail.com a Farmacias Especializadas para hacerle llegar su Receta Electrónica.</sub>

</x-mail::message>
<style>
  .h1 {
    font: normal normal normal 28px/33px Roboto;
  }

  .center {
    text-align: center;
  }

  .p {
    font: normal normal normal 14px/18px Roboto;
  }

  .btn {
    background: #181818;
    border: 1px solid #181818;
    border-radius: 8px;
    color: #fff;
    padding: 16px 32px;
    font: normal normal normal 16px/16px Roboto;
  }

  .logo,
  .footer p {
    display: none;
  }

  .img {
    margin-left: auto;
    margin-right: auto;
    width: 70%;
  }
</style>