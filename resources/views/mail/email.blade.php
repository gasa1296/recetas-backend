
<x-mail::message>
  ![A cute cat](https://upload.wikimedia.org/wikipedia/commons/thumb/3/3a/Cat03.jpg/1200px-Cat03.jpg)
  <h1>{{$title}}</h1>
  <p>{{$message}}</p>
  <p><a href="{{$url}}"><button>{{$button}}</button></a></p>

</x-mail::message>
<style>
  .logo, .footer p {
    display: none;
  }
</style>