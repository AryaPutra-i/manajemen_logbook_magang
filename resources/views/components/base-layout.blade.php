<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Web Logbook Dokter Magang' }}</title>
    @vite('resources/css/app.css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.datatables.net/v/bs5/dt-3.0.3/date-2.0.0/datatables.min.css" rel="stylesheet"
        integrity="sha384-MndP6nihLREZbkWNJiEzw90+6XgDlD0YC9pC6E32MVI9/gDHoQ4OXbkP4q2sCzEt" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
</head>

<body class="min-vh-100 min-vw-100 d-lg-flex" style="background-color: #FEFAE0">
    {{ $slot }}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
    <script src="https://cdn.datatables.net/v/bs5/dt-3.0.3/date-2.0.0/datatables.min.js"
        integrity="sha384-yhyWoVFUNkX3Z8ld2jvescqPnc1H+IIjtgv5uyx6fqCL7sql/tBjVEmfL9pbtQve" crossorigin="anonymous">
    </script>
    </script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    @stack('scripts')
</body>

</html>
