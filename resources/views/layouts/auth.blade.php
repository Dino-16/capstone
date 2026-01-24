<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
  <title>Login - Jetlouge Travels</title>

  <!-- Login Page Styles -->
  @vite(['resources/css/auth.css'])
</head>
<body>
  
  {{ $slot }}


  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
