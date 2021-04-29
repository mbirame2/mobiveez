<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

     

        </head>
    <body>
         
        <p>{{$details['subject']}}</p>
        <p>{{$details['body']}}</p>
     
        <p>Veillez me contacter sur mon adresse mail {{$details['from']}}</p>
        <p>L'équipe IVEEZ.</p>
        
    </body>