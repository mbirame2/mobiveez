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
        $input['password'] = bcrypt($input['password']); 
        

          $co=new User();
          $co->prenom=$input['first_name'];
        $co->nom=$input['last_name'];
        $co->password=$input['password'];
        $co->telephoneportable=$input['phone'];
        $co->email=$input['email'];
        $co->localisation=$input['address'];
        $co->etatcompte=0;
        $co->compte=0;
        $dept=departement::where('lib_dept',$input['city'])->first();;  
        /*var_dump($dept->lib_dept);die();*/
        $co->departement()->associate($dept);
        $co->pays=$input['country'];
        if($request->accountType=="particulier")
        {
            $co->typecompte="particulier";
            $article = User::where('pays',$input['country'])->where('typecompte',"particulier")->get();  
            $article = count($article)+1;
            $co->sexe=$request->gender;
            $code=$request->country.strval(date("y"))."Pa".strval($article+1);
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

 

    public function login(){ 
        if (is_numeric(request('telephone_mail'))) {
            $field = 'telephoneportable' ;
        }else{
            $field = 'email' ;
        }
        if( Auth::attempt([$field => request('telephone_mail'), 'password' => request('password')]) ){ 
            $user = Auth::user(); 
            $token =  $user->createToken('MyApp')->accessToken; 
            return response()->json( [$token,$user]); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }

    public function me()
    {
        if(auth('api')->user()->profil=="particulier"){
            $article = particulier::with(['user'])->where('user_id',auth('api')->user()->id)->first();
            return response()->json($article);
        }else{
            $article = professionnel::with(['user'])->where('user_id',auth('api')->user()->id)->first();
            return response()->json($article);
        }
        
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
    public function sendEmailForgetPassword(Request $request){
        $email = $request->email;
        try {
            $user = $this->userService->findUserByEmail($email);  //code...
        } catch (\Throwable $th) {
            return response()->json([
                "status"=>403,
                "message"=> "le mail n'existe pas dans la base de donnée"
          ]);
        }

        if(!empty($user)){
            $mailSending = $this->sendEmailService->sendEmailPasswordForget($user);   
            echo "mail sending",$mailSending;
            if($mailSending){
                
                return response()->json([
                      "status"=>200,
                      "message"=> "mail sending succefful"
                ]);
            } else{
                return response()->json([
                    "status"=>500,
                    "message"=> "error sending mail"
              ]);
            }  
        }
    }

    public function checkCodeNewMdp(Request $request){
        $code = $request->code;
        $user = $this->userService->findUserByEmail($request->email);
        if($user->dateValiditeCode < Carbon::now()){
            return response()->json([
                "status" => 406,
                "email" => $request->email,
                "message" => "Ce code n'est plus valide"
            ]); 
        }
        if($user->codeMdpForget == $code){
            return response()->json([
                "status" => 200,
                "email" => $request->email,
                 "message" => "le code entré correspond bien au code envoyé dans votre mail"
            ]);
        }
        return response()->json([
            "status" => 403,
            "email" => $request->email,
             "message" => "le code entré ne correspond pas au code envoyé dans votre boite mail"
        ]);


    }
    public function changeMotDePasse(Request $request){
        $user = $this->userService->findUserByEmail($request->email);
        $user->password = $request->newPassword;
        return $user->save();
    }
}
