<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Confirmation de compte</title>

        </head>
    <body>
         
        <p>Bienvenue dans Iveez! </p>
        <p> Votre code de vérification est :</p>
        <h3>{{$details['code']}}</h3>
     
        <p>Si vous n'êtes pas à l'origine de cette action, vous pouvez ignorer ce message.</p>
        
    </body>