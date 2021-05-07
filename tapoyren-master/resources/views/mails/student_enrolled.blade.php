<!DOCTYPE html>
<html lang="en-US">

   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
   </head>

   <body>
      <h1>Hi, {{ $instructor->name }}!</h1>
      <h2>{{ $student->name }} is enrolled for your course "{{ $course->title }}"!</h2>
   </body>

</html>
