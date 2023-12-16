
<x-mail::message>
  ![A cute cat](https://upload.wikimedia.org/wikipedia/commons/thumb/3/3a/Cat03.jpg/1200px-Cat03.jpg)
  <h1 class="center h1">{{$title}}</h1>
  <p class="center p">{{$message}}</p>
  <p class="center"><a href="{{$url}}"><button class="btn">{{$button}}</button></a></p>

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
  .logo, .footer p {
    display: none;
  }
</style>