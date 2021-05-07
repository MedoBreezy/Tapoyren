<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <h1>Hi, {{ $student->name }}</h1>
    <h2>
        Your access to package "{{ $package->name }}" has been expired!<br />
        You can renew your access to package from <a href="{{ url('/package/'.$package->id) }}">this link</a>!

        <br /><br />
        Yours Sincerely,<br />
        CRM team
    </h2>

</body>

</html>