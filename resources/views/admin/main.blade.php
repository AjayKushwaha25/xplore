<!DOCTYPE html>
<html>
<head>
	@include('admin/includes/head')
	@yield('style')
</head>
<body data-sidebar="dark">
	@include('admin/includes/nav')
	@include('admin/includes/sidenav')
	@yield('content')
	@include('admin/includes/footer')
	@yield('script')
</body>
</html>