<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Confirmation de compte</title>

        </head>
    <body>
         
        <p>{{$details['title']}} </p>
        <p>  {{$details['subject']}}</p>
        <h3>{{$details['code']}}</h3>
     
        <p> {{$details['advice']}} </p>
        
    </body>