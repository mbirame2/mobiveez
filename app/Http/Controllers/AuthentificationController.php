<?php

namespace App\Http\Controllers;

use App\Mail\ContactUser;

use App\Services\SendEmailService;
use App\User;
use App\OauthAccessToken;

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
use App\region;
use App\pays;




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
        $co->localisation=$input['adresse'];
        $co->etatcompte=1;
        $co->compte=0;
       // $dept=departement::where('lib_dept',$input['city'])->first(); 
        /*var_dump($dept->lib_dept);die();*/
        //$co->departement()->associate($dept);
        $co->pays=$input['country'];
        if($request->accountType=="particulier")
        {
            $co->typecompte="particulier";
            $article = User::where('pays',$input['country'])->where('typecompte',"particulier")->get();  
            $article = count($article)+1;
            $co->sexe=$request->gender;
           // return ucfirst($request->countryCode);

            $code=ucfirst($request->countryCode).strval(date("y"))."Pa".strval($article+1);
            $co->codemembre=$code;
            $co->DateInscription=date("Y/m/d-h:i");
        
            $co->save();
        }else if($request->accountType=="professionnel"){

            $co->typecompte="professionnel";
            $article = User::where('pays',$input['country'])->where('typecompte',"professionnel")->get();  
            $article = count($article)+1;

            $code=$request->countryCode.strval(date("y"))."Pr".strval($article);
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
            'password' => sha1(request('password')),
            'etatcompte' => 1
        ])->first();
        
        if($user)
        {
            Auth::login($user);
            $user = Auth::user(); 
           // $dept=departement::with('region')->where('id_dept',$user->departement_id)->first(); 
            //$region=region::where('id_reg',$dept->region->id_reg)->first(); 
            //return $dept;
            //$pays=pays::where('id_pays',$region->id_pays)->first(); 
            //$user['departement']=$dept->lib_dept;
            //$user['pays']=$pays->lib_pays;
            $token =  $user->createToken('MyApp')->accessToken; 
            return response()->json( [$token,$user]); 
        }else{
            return response()->json(["error: Mot de passe incorrecte ou compte desactive",401]); 
        }
    }

    public function me()
    {
            return response()->json(auth('api')->user() );

    }
    
    public function getuser($id)
    {
        $user = User::select( 'idmembre','prenom','departement_id','profil','nom','telephoneportable','localisation','telephonefixe','email','codemembre')->where(
            'idmembre', $id)->first();
            $dep=departement::where('id_dept',$user['departement_id'])->first(); 
            $user['departement']=$dep['lib_dept'];
            return response()->json($user);
        
    }
    public function sendmail($mail,$lang)
    {
        $number=rand(1000,9999);
        if($lang=='fr'){
            $details=[
             
                'subject'=>'Votre code de vérification est :',
                'code'=>$number,
                'title'=>'Bienvenue dans Iveez! ',
                'advice'=>"Si vous n'êtes pas à l'origine de cette action, vous pouvez ignorer ce message.",
            ];
        } else if ($lang=='en'){
            $details=[
             
                'subject'=>'Your verification code is :',
                'code'=>$number,
                'title'=>'Welcome to iveez!',
                'advice'=>"If you did not initiate this action, you can ignore this message ",
            ];
        }
        
        
      
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

    public function contact(Request $req )
    {
        
        $details=[];
        $details['body']=$req->message;
        $details['from']=$req->emailExp;
        
        $details['subject']=$req->messageObject;

      
         Mail::to($req->emailDest)->send(new ContactUser($details));

        return response()->json([
            "status"=>200,
            "message"=> "success"
        ]);
            //$user = $this->userService->findUserByEmail($mail);  //code...
     
    }

    public function checkuser(Request $request){
        if (is_numeric(request('telephone_mail'))) {
            $field = 'telephoneportable' ;
        }else{
            $field = 'email' ;
        }
        $user = User::where($field, $request->telephone_mail)->first();
        if (!$user) {
            return response()->json([
                "status"=>200,
                "exist"=> false
          ]);
        }
       
        return response()->json([
            "status"=>200,
            "exist"=> true,
            "typecompte"=> $user->typecompte
      ]);
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
        $co->telephonefixe=$input['telephonefixe'];
        $co->societe=$input['societe'];
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


        
    Auth::logout();
    
    OauthAccessToken::where("user_id", auth('api')->user()->idmembre)->delete(); 
     
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
        $phonenumber = $request->phonenumber;
      
            $user = User::where("email", $email)->orwhere("telephoneportable", $phonenumber)->first(); 
            if (!$user) {
                return response()->json([
                    "status"=>403,
                    "message"=> "le mail ou le numero telephone n'existe pas dans la base de donnée"
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
