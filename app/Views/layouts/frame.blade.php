<html>
<head>
    <title>@yield('title')</title>
</head>

<body>
    @include('header')

    @section('content')
    @show

    @include('footer')
</body>
</html>