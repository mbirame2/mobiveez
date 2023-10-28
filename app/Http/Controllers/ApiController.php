<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use File;
use App\plat;
use App\User;
use Validator;
use App\region;
use App\annonce;
use App\chambre;
use App\service;
use App\vehicule;
use App\evenement;
use App\automobile;
use App\immobilier;
use App\departement;
use App\habillement;
use App\imageannonce;
use App\restauration;
use App\commande_plat;
use App\professionnel;
use App\conditionsdutilisation;
use App\souscategorie;
use Illuminate\Http\Request;
use App\tarificationlivraison;
use Illuminate\Support\Facades\Storage;


class ApiController extends Controller
{


   

    public function chambre(Request $req){
  
      
      $annonce= new chambre;
      if($req->hasFile('photo1') ){
        $image_name = $req->file('photo1')->getClientOriginalName();
        $filename = pathinfo($image_name,PATHINFO_FILENAME);
        $image_ext = $req->file('photo1')->getClientOriginalExtension();
        $fileNameToStore = $filename.'-'.time().'.'.$image_ext;
        $path =  $req->file('photo1')->storeAs('public/images/chambre',$fileNameToStore);
        $annonce->photo1= $fileNameToStore;
      }
      if($req->hasFile('photo2') ){
        $image_name = $req->file('photo2')->getClientOriginalName();
        $filename = pathinfo($image_name,PATHINFO_FILENAME);
        $image_ext = $req->file('photo2')->getClientOriginalExtension();
        $fileNameToStore = $filename.'-'.time().'.'.$image_ext;
        $path =  $req->file('photo2')->storeAs('public/images/chambre',$fileNameToStore);
        $annonce->photo2= $fileNameToStore;
      }
      if($req->hasFile('photo3') ){
        $image_name = $req->file('photo3')->getClientOriginalName();
        $filename = pathinfo($image_name,PATHINFO_FILENAME);
        $image_ext = $req->file('photo3')->getClientOriginalExtension();
        $fileNameToStore = $filename.'-'.time().'.'.$image_ext;
        $path =  $req->file('photo3')->storeAs('public/images/chambre',$fileNameToStore);
        $annonce->photo3= $fileNameToStore;
      }
      if($req->hasFile('photo4') ){
        $image_name = $req->file('photo4')->getClientOriginalName();
        $filename = pathinfo($image_name,PATHINFO_FILENAME);
        $image_ext = $req->file('photo4')->getClientOriginalExtension();
        $fileNameToStore = $filename.'-'.time().'.'.$image_ext;
        $path =  $req->file('photo4')->storeAs('public/images/chambre',$fileNameToStore);
        $annonce->photo4= $fileNameToStore;
      }
      $annonce->titre=$req->input('titre');
      $annonce->prix_nuitee=$req->input('prix_nuitee');
      $annonce->service_chambre=$req->input('service_chambre');
      $annonce->type_lit=$req->input('type_lit');
      $annonce->capacite=$req->input('capacite');
      $annonce->description=$req->input('description');
      $annonce->statut=$req->input('statut');
      $annonce->user()->associate(auth('api')->user());
    
      $annonce->save();
      return response()->json(['succés'=>"Enregistrement du chambre avec succés"], 200);            

    }

public function getconditions($acro,$lang){
  $conditionsdutilisation=conditionsdutilisation::where([['acro' , $acro],['langue' ,$lang]])->get();
  return response()->json($conditionsdutilisation); 
} 

    public function vehicule(Request $req){
      $validator = Validator::make($req->all(), [ 
        'marque' => 'required', 
        'modele' => 'required', 
        'prix' => 'required', 
        'type_vehicule'=> 'required',
        'couleur' => 'required', 
        'type_vitesse' => 'required', 
        'statut' => 'required', 
        'carburant' => 'required', 
        'kilometre' => 'required', 
        'capacite' => 'required', 
        'climatisation' => 'required', 
        'autre_specification' => 'required', 
        'description' => 'required',
        'statut' => 'required',
    ]); 
      
          //var_dump(auth('api')->user()->id_professionnel);die();
    if ($validator->fails()) { 
        return response()->json(['error'=>$validator->errors()], 401);            
    }else{
      
      $annonce= new vehicule;
      if($req->hasFile('photo1') ){
        $image_name = $req->file('photo1')->getClientOriginalName();
        $filename = pathinfo($image_name,PATHINFO_FILENAME);
        $image_ext = $req->file('photo1')->getClientOriginalExtension();
        $fileNameToStore = $filename.'-'.time().'.'.$image_ext;
        $path =  $req->file('photo1')->storeAs('public/vehicule/photo',$fileNameToStore);
        $annonce->photo1= $fileNameToStore;
      }
      if($req->hasFile('photo2') ){
        $image_name = $req->file('photo2')->getClientOriginalName();
        $filename = pathinfo($image_name,PATHINFO_FILENAME);
        $image_ext = $req->file('photo2')->getClientOriginalExtension();
        $fileNameToStore = $filename.'-'.time().'.'.$image_ext;
        $path =  $req->file('photo2')->storeAs('public/images/vehicule',$fileNameToStore);
        $annonce->photo2= $fileNameToStore;
      }
      if($req->hasFile('photo3') ){
        $image_name = $req->file('photo3')->getClientOriginalName();
        $filename = pathinfo($image_name,PATHINFO_FILENAME);
        $image_ext = $req->file('photo3')->getClientOriginalExtension();
        $fileNameToStore = $filename.'-'.time().'.'.$image_ext;
        $path =  $req->file('photo3')->storeAs('public/images/vehicule',$fileNameToStore);
        $annonce->photo3= $fileNameToStore;
      }
      if($req->hasFile('photo4') ){
        $image_name = $req->file('photo4')->getClientOriginalName();
        $filename = pathinfo($image_name,PATHINFO_FILENAME);
        $image_ext = $req->file('photo4')->getClientOriginalExtension();
        $fileNameToStore = $filename.'-'.time().'.'.$image_ext;
        $path =  $req->file('photo4')->storeAs('public/images/vehicule',$fileNameToStore);
        $annonce->photo4= $fileNameToStore;
      }
      $annonce->marque=$req->input('marque');
      $annonce->modele=$req->input('modele');
      $annonce->prix=$req->input('prix');
      $annonce->type_vehicule=$req->input('type_vehicule');
      $annonce->couleur=$req->input('couleur');
      $annonce->type_vitesse=$req->input('type_vitesse');
      $annonce->carburant=$req->input('carburant');
      $annonce->kilometre=$req->input('kilometre');
      $annonce->capacite=$req->input('capacite');
      $annonce->climatisation=$req->input('climatisation');
      $annonce->autre_specification=$req->input('autre_specification');
      $annonce->description=$req->input('description');
      $annonce->statut=$req->input('statut');
      $annonce->user()->associate(auth('api')->user());
    
      $annonce->save();
      return response()->json(['succés'=>"Enregistrement du vehicule avec succés"], 200);            

    }}

    public function images ($filename,$photo)
    {
        $path = public_path('storage')."/".$filename.'/'. $photo;
        $file = File::get($path);
        $response = Response($file, 200);
        $response->header('Content-Type', 'image/jpeg');
        return $response;
    
    }

    public function saveimage($storage,$name,$req){
      try {
        $filepath = storage_path($storage);
        Log::channel('custom_log')->info(date("Y-m-d").' ImageFile '.$req);
        $req->move($filepath,$name);
      } catch (\Exception $e) {
        abort(403, 'File not upload. Please try again');
          //return $e->getMessage();
      }      
    }
    
    public function evenement(Request $req){
      $validator = Validator::make($req->all(), [ 
        'adresse' => 'required', 
        'frequence' => 'required', 
        'horaire' => 'required', 
        'num_autorisation'=> 'required',
        'information_supplementaire' => 'required', 
        'description' => 'required', 
        'prix' => 'required', 
        'statut' => 'required', 
       
    ]); 
      
          //var_dump(auth('api')->user()->id_professionnel);die();
    if ($validator->fails()) { 
        return response()->json(['error'=>$validator->errors()], 401);            
    }else{
      
      $annonce= new evenement;
      if($req->hasFile('photo1') ){
        $image_name = $req->file('photo1')->getClientOriginalName();
        $filename = pathinfo($image_name,PATHINFO_FILENAME);
        $image_ext = $req->file('photo1')->getClientOriginalExtension();
        $fileNameToStore = $filename.'-'.time().'.'.$image_ext;
        $path =  $req->file('photo1')->storeAs('public/images/evenement',$fileNameToStore);
        $annonce->photo1= $fileNameToStore;
      }
      if($req->hasFile('photo2') ){
        $image_name = $req->file('photo2')->getClientOriginalName();
        $filename = pathinfo($image_name,PATHINFO_FILENAME);
        $image_ext = $req->file('photo2')->getClientOriginalExtension();
        $fileNameToStore = $filename.'-'.time().'.'.$image_ext;
        $path =  $req->file('photo2')->storeAs('public/images/vehicule',$fileNameToStore);
        $annonce->photo2= $fileNameToStore;
      }
      if($req->hasFile('photo3') ){
        $image_name = $req->file('photo3')->getClientOriginalName();
        $filename = pathinfo($image_name,PATHINFO_FILENAME);
        $image_ext = $req->file('photo3')->getClientOriginalExtension();
        $fileNameToStore = $filename.'-'.time().'.'.$image_ext;
        $path =  $req->file('photo3')->storeAs('public/images/vehicule',$fileNameToStore);
        $annonce->photo3= $fileNameToStore;
      }
      if($req->hasFile('photo4') ){
        $image_name = $req->file('photo4')->getClientOriginalName();
        $filename = pathinfo($image_name,PATHINFO_FILENAME);
        $image_ext = $req->file('photo4')->getClientOriginalExtension();
        $fileNameToStore = $filename.'-'.time().'.'.$image_ext;
        $path =  $req->file('photo4')->storeAs('public/images/vehicule',$fileNameToStore);
        $annonce->photo4= $fileNameToStore;
      }
      $annonce->adresse=$req->input('adresse');
      $annonce->frequence=$req->input('frequence');
      $annonce->horaire=$req->input('horaire');
      $annonce->num_autorisation=$req->input('num_autorisation');
      $annonce->information_supplementaire=$req->input('information_supplementaire');
      $annonce->description=$req->input('description');
      $annonce->prix=$req->input('prix');
      $annonce->statut=$req->input('statut');
     
      $annonce->user()->associate(auth('api')->user());
    
      $annonce->save();
      return response()->json(['succés'=>"Enregistrement de l'evenement avec succés"], 200);            

    }}



    public function getdepartement($id)
    {

      $article = departement::with(['region'])->whereHas('region', function ($query) use ($id) {
        $query->where('id_pays', $id);
    })->get();
      return response($article, 200)  ;
    }

    public function getchambre()
    {
      $article = chambre::with(['user.professionnel','user.particulier'])->get();
      return response()->json($article); 
    }
    public function getvehicule()
    {
      $article = vehicule::with(['user.professionnel','user.particulier'])->get();
      return response()->json($article); 
    }
    public function getevenement()
    {
      $article = evenement::with(['user.professionnel','user.particulier'])->get();
      return response()->json($article); 
    }



    ////////////BACK-OFFICE/////////////////
    public function validerannonce($id)
    {
      $annonce =annonce::find($id);  
      $annonce->statut='acceptee';
      $annonce->save();
      return response()->json($annonce); 
    }



    public function listeservice($module)
    {
     // return strtolower(substr(auth('api')->user()->codemembre,0,2));
      if(strtolower(substr(auth('api')->user()->codemembre,0,2))=="gm"){
        $pays="Gambia";
      }else if(strtolower(substr(auth('api')->user()->codemembre,0,2))=="sn"){
        $pays="Sénégal";
      }
      $service=service::where([['nomcomplet', 'LIKE', $pays ],['module',$module]])->get();
      return response()->json($service); 
    }

    public static function  getidservice($module)
    {
      $service=service::select('idService')->where([['nomcomplet', 'LIKE', '%' . auth('api')->user()->pays . '%'],['module',$module]])->get();
      return $service; 
    }

    public function listetarificationlivraison()
    {
      $service=tarificationlivraison::get();
 
      return response()->json($service); 
    }

    public static function  getidservicewithmoduleonly($module)
    {
      $service=service::select('idService')->where('module',$module)->get();
      return $service; 
    }

        public function getDownload($version)
    {
        //PDF file is stored under project/public/download/info.pdf
        if($version=='fr'){
          $file= public_path(). "/storage/cgu_files/CONDITIONS GENERALES D'UTILISATION - IVEEZ_FRANCAIS.pdf";
        } else if($version=='en'){
          $file= public_path(). "/storage/cgu_files/TERMS OF USE- IVEEZ_ENGLISH.pdf";
        }

        $headers = array(
                  'Content-Type: application/pdf',
                );

        return response()->download($file, 'cgu_'.$version.'.pdf', $headers);
    }
 
}
