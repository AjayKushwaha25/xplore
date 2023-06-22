<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes/header')
    @yield('style')
    {{-- @include('components/message') --}}
</head>
<body>
    @yield('content')
    @include('includes/footer')
    @yield('script')
</body>
</html>
