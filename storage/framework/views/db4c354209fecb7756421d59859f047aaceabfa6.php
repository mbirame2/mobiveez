<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Confirmation de compte</title>

        </head>
    <body>
         
        <p><?php echo e($details['title']); ?> </p>
        <p>  <?php echo e($details['subject']); ?></p>
        <h3><?php echo e($details['code']); ?></h3>
     
        <p> <?php echo e($details['advice']); ?> </p>
        
    </body><?php /**PATH /var/www/html/mobiveez/resources/views/TestMail.blade.php ENDPATH**/ ?>