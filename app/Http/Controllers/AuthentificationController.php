<?php

namespace App\Http\Controllers;

use App\Services\SendEmailService;
use App\User;
use App\departement;
use App\particulier;
use App\professionnel;
use App\Services\SendEmailServiceImpl;
use App\Services\UserService;
use App\Services\UserServiceImpl;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;


class AuthentificationController extends Controller
{
    public   $userService;
    public   $sendEmailService;
    
 
       /** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
      //  var_dump("SN".strval(date("y")).strval($article));die();
        $validator = Validator::make($request->all(), [ 
            'first_name' => 'required', 
            'last_name' => 'required', 
            'phone' => 'required', 
            'password' => 'required', 
            'accountType' => 'required', 
            'email' => 'required', 
            'city' => 'required', 
            'address' => 'required', 
            'country' => 'required', 
            
         

        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all(); 
        $input['password'] = sha1($input['password']); 
        

          $co=new User();
          $co->prenom=$input['first_name'];
        $co->nom=$input['last_name'];
        $co->password=$input['password'];
        $co->telephoneportable=$input['phone'];
        $co->email=$input['email'];
        $co->num_whatsapp=$input['num_whatsapp'];
        $co->localisation=$input['address'];
        $co->etatcompte=1;
        $co->compte=0;
        $dept=departement::where('lib_dept',$input['city'])->first(); 
        /*var_dump($dept->lib_dept);die();*/
        $co->departement()->associate($dept);
        $co->pays=$input['country'];
        if($request->accountType=="particulier")
        {
            $co->typecompte="particulier";
            $article = User::where('pays',$input['country'])->where('typecompte',"particulier")->get();  
            $article = count($article)+1;
            $co->sexe=$request->gender;
            $code=$request->countryCode.strval(date("y"))."Pa".strval($article+1);
            $co->codemembre=$code;
            $co->DateInscription=date("Y-m-d h:i:sa");
        
            $co->save();
        }else if($request->accountType=="professionnel"){

            $co->typecompte="professionnel";
            $article = User::where('pays',$input['country'])->where('typecompte',"professionnel")->get();  
            $article = count($article)+1;

            $code=$request->country.strval(date("y"))."Pr".strval($article);
            $co->DateInscription=date("Y-m-d h:i:sa");
            $co->codemembre=$code;
            $co->societe=$request->company;
            $co->telephonefixe=$request->landline;
            $co->save();

        }else{
            return response()->json("Erreur: choisir pour le compte entre particulier et professionnel"); 
        }

        $success['token'] =  $co->createToken('MyApp')->accessToken; 
        $success['user']=$co;
        return response()->json(['success'=>$success],200); 

    }

 

    public function loggin(){ 
        if (is_numeric(request('telephone_mail'))) {
            $field = 'telephoneportable' ;
        }else{
            $field = 'email' ;
        }
        if( Auth::attempt([$field => request('telephone_mail'), 'password' => sha1(request('password'))]) ){ 
            $user = Auth::user(); 
            $token =  $user->createToken('MyApp')->accessToken; 
            return response()->json( [$token,$user]); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }

    public function login(){
        if (is_numeric(request('telephone_mail'))) {
            $field = 'telephoneportable' ;
        }else{
            $field = 'email' ;
        }
        $user = User::where([
            $field => request('telephone_mail'), 
            'password' => sha1(request('password'))
        ])->first();
        
        if($user)
        {
            Auth::login($user);
            $user = Auth::user(); 
            $token =  $user->createToken('MyApp')->accessToken; 
            return response()->json( [$token,$user]); 
        }else{
            return response()->json(["error: Mot de passe incorrecte",401]); 
        }
    }

    public function me()
    {
       
            return response()->json(auth('api')->user() );
        
            
    }
    public function sendmail($mail)
    {
        $number=rand(1000,9999);
        $details=[
             
            'body'=>'Bienvenue dans Iveez. Votre code d activation est :',
            'code'=>$number
        ];
        
      
            $user = User::where("email", $mail)->first();
            if (!$user) {
                return response()->json([
                    "status"=>403,
                    "message"=> "le mail n'existe pas dans la base de donnée"
              ]);
            }
            Mail::to($mail)->send(new TestMail($details));
            return response( [$number]);
            //$user = $this->userService->findUserByEmail($mail);  //code...
     

         
        
            
    }
    public function updateuser(Request $request)
    {
        if(!auth('api')->user()){
            return response()->json(
                ['code'=>403 ,
                'error'=>'Token not found'
                ]);
        }
        $input = $request->all(); 
        $co = User::find(auth('api')->user()->idmembre);
       
        $co->prenom=$input['prenom'];
        $co->nom=$input['nom']; 
        $co->telephoneportable=$input['telephoneportable'];
        $co->email=$input['email'];
        if($input['newpassword']){
        $co->password= sha1($input['newpassword']); 
        }
        $co->num_whatsapp=$input['num_whatsapp'];
        $co->localisation=$input['localisation'];
       
        $dept=departement::find($input['departement_id']); 
        /*var_dump($dept->lib_dept);die();*/
        $co->departement()->associate($dept);
        
        $co->save();
         
        return response()->json([
            "status"=>200,
            "message"=> "l'utilisateur a été mis à jour"
      ]);
        
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {

        auth()->logout();
        return response()->json([
            'message' => 'Successfully logged out',
            'status' => 200
        ]);
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'user' => response()->json(auth()->user())->original,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expire_in' => auth('api')->factory()->getTTL(),
        ]);
    }
    public function changepassword(Request $request){
        $email = $request->email;
      
            $user = User::where("email", $email)->first(); 
            if (!$user) {
                return response()->json([
                    "status"=>403,
                    "message"=> "le mail n'existe pas dans la base de donnée"
              ]);
            }
            $user->password=sha1($request->password); 
            $user->save();
            return response()->json([
                "status"=>200,
                "message"=> "success"
          ]);
      

       
    }


    
}
