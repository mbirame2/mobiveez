<?php

namespace App\Http\Controllers;

use App\Services\SendEmailService;
use App\User;
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
        $article = User::latest()->first();
        $fileNameToStore="null";
        if($article==null){
            $one=0;
        }else{
            $one=$article->id;
        }
        
        $article=$one+1;
      //  var_dump("SN".strval(date("y")).strval($article));die();
        $validator = Validator::make($request->all(), [ 
            'prenom' => 'required', 
            'nom' => 'required', 
            'telephone' => 'required', 
            'password' => 'required', 
            'compte' => 'required', 
            'email' => 'required', 
            'ville' => 'required', 
            'adresse' => 'required', 
            'pays' => 'required', 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        
        if($request->hasFile('photo') ){
            $image_name = $request->file('photo')->getClientOriginalName();
        $filename = pathinfo($image_name,PATHINFO_FILENAME);
        $image_ext = $request->file('photo')->getClientOriginalExtension();
        $fileNameToStore = $filename.'-'.time().'.'.$image_ext;
        $path =  $request->file('photo')->storeAs('public/images/user',$fileNameToStore);
          }
          $co=new User();
          $co->prenom=$input['prenom'];
        $co->nom=$input['nom'];
        $co->password=$input['password'];
        $co->telephone=$input['telephone'];
        $co->email=$input['email'];
        $co->pays=$input['pays'];
        
        if($request->compte=="particulier")
        {
            $co->profil="particulier";
            

            $input['id_user']="SN".strval(date("y"))."Pa".strval($article);
            $com=new particulier();
            $com->numero_id=$input['id_user'];
            $com->genre=$input['genre'];
            $com->ville=$input['ville'];
            $com->adresse=$input['adresse'];
            if($fileNameToStore!="null"){
            $com->photo=$fileNameToStore;
            }
            
            $com->save();
            $co->particulier()->associate($com);
            $co->save();
        }else if($request->compte=="professionnel"){

            
            $co->profil="professionnel";
            
            $input['id_user']="SN".strval(date("y"))."Pr".strval($article);
            $com=new professionnel();
            $com->numero_id=$input['id_user'];
            $com->entreprise=$input['entreprise'];
            $com->ville=$input['ville'];
            $com->adresse=$input['adresse'];
            if($fileNameToStore){
                $com->photo=$fileNameToStore;
                }
            $com->telephone_fixe=$input['telephone_fixe'];
            
            $com->save();
            $co->professionnel()->associate($com);
            $co->save();

        }else{
            return response()->json("Erreur: choisir pour le compte entre particulier et professionnel"); 
        }

        $success['token'] =  $co->createToken('MyApp')->accessToken; 
        $success['user']=$co;
        return response()->json(['success'=>$success],200); 

    }

 

    public function login(){ 
        if(Auth::attempt(['telephone' => request('telephone_mail'), 'password' => request('password')]) || Auth::attempt(['email' => request('telephone/mail'), 'password' => request('password')]) ){ 
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
