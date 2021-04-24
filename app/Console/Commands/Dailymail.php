<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\User;
use App\Mail\StatutUser;

class Dailymail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statut:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change statut';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       
        User::where('DateDesactivation','=',date("Y-m-d"))->update(['etatcompte' => 0]);
        
      
        $details=[
            'body'=> 'Votre Compte IVEEZ a été suspendu',
            'code'=> ''
        ];
        $users=User::where('DateDesactivation','=',date("Y-m-d"))->get();
        foreach($users as $user){
          
            Mail::to($user->email)->send(new StatutUser($details));
        }
      
       // date("Y-m-d h:i:sa");
        
      #  return $user;
    }
}
