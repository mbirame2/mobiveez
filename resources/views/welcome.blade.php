<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Activer/Desactiver son compte</title>

        </head>
    <body>
         
        <p>Bienvenue dans Iveez! </p>

        {% if $details['body'] %}
        <h3>{{$details['body']}}</h3>
        {% endif %}

        <p>Merci.</p>
        
    </body>