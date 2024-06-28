<x-mail::message>
  <div class="container">
    <div class="content">
      <p class="welcome">Hola <span class="welcome-2">{{$prescription->patient->first_name}} {{$prescription->patient->last_name}} {{$prescription->patient->last_name1}}</span></p>
      <p class="medic">
        tu médico <span class="medic-bold">{{$prescription->medic->first_name}} {{$prescription->medic->last_name}} {{$prescription->medic->last_name1}}</span> te ha
        enviado una Receta Médica Electrónica.
      </p>
    </div>
    <img
      src="{{ asset('email/background.jpg')}}"
      alt="background"
      class="img-background"
    />
    <img class="img-square" src="{{ asset('email/square.jpg')}}"/>
    <p class="folio-text">El folio de tu Receta Médicas Electrónica es:</p>
    <p class="folio">{{$prescription->code}}</p>
    <a href="{{$link}}"><img src="{{ asset('email/consulta.jpg')}}" class="img-consulta" alt="consulta" /></a>
    <img src="{{ asset('email/vineta.jpg')}}" class="img-vineta" alt="vineta" />
    <p class="follow">Síguenos en redes sociales</p>
    <div>
      <a href="https://www.facebook.com/" class="social">
        <img src="{{ asset('email/facebook.jpg')}}" alt="facebook" />
      </a>
      <a href="https://www.instagram.com/" class="social">
        <img src="{{ asset('email/instagram.jpg')}}" alt="instagram" />
      </a>
      <a href="https://www.linkeding.com/" class="social">
        <img src="{{ asset('email/linkedin.jpg')}}" alt="linkeding" />
      </a>
      <a href="https://www.x.com/" class="social">
        <img src="{{ asset('email/x.jpg')}}" alt="x" />
      </a>
    </div>
    <div class="container-download">
      <div class="border-download"></div>
      <div class="download">Descarga nuestra aplicacion móvil:</div>
      <div class="border-download"></div>
    </div>
    <div class="app">
      <a href="https://www.facebook.com/" class="social">
        <img src="{{ asset('email/google.jpg')}}" alt="google" />
      </a>
      <a href="https://www.instagram.com/" class="social">
        <img src="{{ asset('email/appstore.jpg')}}" alt="appstore" />
      </a>
    </div>
  </div>
</x-mail::message>
<style>
  .logo,
  .x_header a,
  .x_footer p,
  .footer p {
    display: none;
  }
</style>
<style>
  .container {
    width: 100%;
    max-width: 602px;
    margin: 0 auto;
    background-color: #ffffff;
    padding: 20px 0px;
    text-align: center;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }
  .header {
    text-align: center;
    padding: 10px 0;
    background-color: #4caf50;
    color: white;
    border-radius: 10px 10px 0 0;
  }
  .content {
    padding: 20px;
  }
  .welcome {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 10px;
  }
  .medic {
    max-width: 350px;
    line-height: 26px;
    width: 100%;
    font-size: 16px;
    font-weight: 300;
    margin: 0 auto;
  }
  .medic-bold {
    font-weight: bold;
  }
  .welcome-2 {
    color: #27348b;
  }
  .footer {
    text-align: center;
    padding: 10px 0;
    background-color: #4caf50;
    color: white;
    border-radius: 0 0 10px 10px;
    margin-top: 20px;
  }
  a {
    color: #4caf50;
    text-decoration: none;
  }
  .img-background {
    width: 100%;
    height: auto;
    display: block;
    margin: 0 auto;
  }
  .img-square {
    width: 95%;
    height: auto;
    display: block;
    margin: 0 auto;
    position: relative;
    top: -3px;
  }
  .folio-text {
    font-size: 16px;
    margin-top: 30px;
    font-weight: 200;
    color: #27348b;
  }
  .folio {
    border: 2px solid #27348b;
    margin: 0 auto;
    color: #27348b;
    font-weight: bold;
    display: inline-block;
    padding: 16px 32px;
    font-size: 28px;
    border-radius: 16px;
    margin-bottom: 30px;
  }
  .img-vineta {
    width: 100%;
    height: auto;
    display: block;
    margin: 0 auto;
  }
  .img-consulta {
    width: 80%;
    height: auto;
    display: block;
    margin: 0 auto;
  }
  .follow {
    font-weight: bold;
    font-size: 28px;
  }
  .social {
    margin: 0px 10px;
  }
  .app {
    margin-top: 10px;
  }
  .download {
    font-size: 14px;
    font-weight: 500px;
    color: #ff6700;
    margin: 0 10px;
  }
  .container-download {
    width: 70%;
    margin: 0 auto;
    display: flex;
    margin-top: 40px;
  }
  .border-download {
    border-top: 2px solid #ff6700;
    width: 20%;
    margin: 0 auto;
    margin-top: 7px;
  }
</style>