<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

     

        </head>
    <body>
         
        <h2>{{$details['subject']}}</h2>
        <p>{{$details['body']}}</p>
     
        <p>Merci de me contacter sur cette adresse mail {{$details['from']}}.</p>
        <p>L'équipe IVEEZ.</p>
        
    </body>