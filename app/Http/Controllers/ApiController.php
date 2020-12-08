<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\professionnel;
use App\plat;
use App\chambre;
use App\evenement;
use App\vehicule;
use App\User;
use App\particulier;
use App\annonce;
use App\automobile;
use App\commande_plat;
use App\habillement;
use App\immobilier;
use Validator;
use File;

class ApiController extends Controller
{
    // Insertion des annonces
    public function annonce(Request $req){
        $validator = Validator::make($req->all(), [ 
          'categorie' => 'required', 
          'sous_categorie' => 'required', 
          'prix' => 'required', 
          'type_publication'=> 'required',
          'paiement' => 'required', 
          'ville' => 'required', 
          'titre' => 'required', 
          'quartier' => 'required',
          'description' => 'required', 
          'photo' => 'required', 
      ]); 
        
            //var_dump(auth('api')->user()->id_professionnel);die();
      if ($validator->fails()) { 
          return response()->json(['error'=>$validator->errors()], 401);            
      }else{
        $annonce= new annonce;
        if($req->input('categorie')=="habillement"){
          $habillement= new habillement;
          $habillement->type=$req->input('type');     
          $habillement->marque=$req->input('marque');  
          $habillement->modele=$req->input('modele');  
          $habillement->couleur=$req->input('couleur');  
          $habillement->taille=$req->input('taille');  
          $habillement->save();
          $annonce->habillement()->associate($habillement);
        }else if($req->input('categorie')=="immobilier" && $req->input('sous_categorie')!='materiel_construction'){
          $immobilier= new immobilier;
          $immobilier->surface=$req->input('surface');     
          $immobilier->nombre_piece=$req->input('nombre_piece');  
          $immobilier->date=$req->input('date');  
          $immobilier->droit_visite=$req->input('droit_visite');  
          $immobilier->montant=$req->input('montant');  
          $immobilier->save();
          $annonce->immobilier()->associate($immobilier);
        } else if($req->input('categorie')=="automobile" && $req->input('sous_categorie')!='assurance_autos'){
          $automobile= new automobile;
          $automobile->categorie=$req->input('categorie');     
          $automobile->marque=$req->input('marque');  
          $automobile->modele=$req->input('modele');  
          $automobile->capacite=$req->input('capacite');  
          $automobile->couleur=$req->input('couleur');  
          $automobile->kilometre=$req->input('kilometre');  
          $automobile->puissance=$req->input('puissance');  
          $automobile->boite=$req->input('boite');  
          $automobile->carburant=$req->input('carburant');  
          $automobile->jante=$req->input('jante');  
          $automobile->cylindre=$req->input('cylindre'); 
          $automobile->save();
          $annonce->automobile()->associate($automobile);
        }
        if($req->hasFile('photo') ){
          $image_name = $req->file('photo')->getClientOriginalName();
          $filename = pathinfo($image_name,PATHINFO_FILENAME);
          $image_ext = $req->file('photo')->getClientOriginalExtension();
          $fileNameToStore = $filename.'-'.time().'.'.$image_ext;
          $path =  $req->file('photo')->storeAs('public/images/annonce',$fileNameToStore);
          $annonce->photo= $fileNameToStore;
        }
        $annonce->categorie=$req->input('categorie');
        $annonce->sous_categorie=$req->input('sous_categorie');
        $annonce->prix=$req->input('prix');
        $annonce->type_publication=$req->input('type_publication');
        $annonce->paiement=$req->input('paiement');
        $annonce->ville=$req->input('ville');
        $annonce->titre=$req->input('titre');
        $annonce->quartier=$req->input('quartier');
        $annonce->description=$req->input('description');
        $annonce->dateannonce=date("Y-m-d H:i:s");
        $annonce->user()->associate(auth('api')->user());
      
        $annonce->save();
        return response()->json(['succés'=>"Enregistrement de lannonce avec succés"], 200);            

      }}


      public function plat(Request $req){
        $validator = Validator::make($req->all(), [ 
          'prix' => 'required', 
          'temps_preparation' => 'required', 
          'jour_disponible' => 'required', 
          'plat_accompagnement'=> 'required',
          'description' => 'required', 
          'categorie' => 'required', 
          'nombre_plat' => 'required',
      ]); 
        
            //var_dump(auth('api')->user()->id_professionnel);die();
      if ($validator->fails()) { 
          return response()->json(['error'=>$validator->errors()], 401);            
      }else{
        
        $annonce= new plat;
        if($req->hasFile('photo') ){
          $image_name = $req->file('photo')->getClientOriginalName();
          $filename = pathinfo($image_name,PATHINFO_FILENAME);
          $image_ext = $req->file('photo')->getClientOriginalExtension();
          $fileNameToStore = $filename.'-'.time().'.'.$image_ext;
          $path =  $req->file('photo')->storeAs('public/images/plat',$fileNameToStore);
          $annonce->photo= $fileNameToStore;
        }
        $annonce->categorie=$req->input('categorie');
        $annonce->prix=$req->input('prix');
        $annonce->temps_preparation=$req->input('temps_preparation');
        $annonce->jour_disponible=$req->input('jour_disponible');
        $annonce->plat_accompagnement=$req->input('plat_accompagnement');
        $annonce->description=$req->input('description');
        $annonce->statut="en attente";
        $annonce->nombre_plat=$req->input('nombre_plat');
        $annonce->user()->associate(auth('api')->user());
      
        $annonce->save();
        return response()->json(['succés'=>"Enregistrement du plat avec succés"], 200);            

      }}


      public function chambre(Request $req){
        $validator = Validator::make($req->all(), [ 
          'titre' => 'required', 
          'prix_nuitee' => 'required', 
          'service_chambre' => 'required', 
          'type_lit'=> 'required',
          'capacite' => 'required', 
          'description' => 'required', 
          'statut' => 'required', 
      ]); 
        
            //var_dump(auth('api')->user()->id_professionnel);die();
      if ($validator->fails()) { 
          return response()->json(['error'=>$validator->errors()], 401);            
      }else{
        
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

      }}


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
          $path =  $req->file('photo1')->storeAs('public/images/vehicule',$fileNameToStore);
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

      public function images ($photo,$filename)
      {
          $path = public_path('storage')."/images/".$photo.'/'. $filename;
          $file = File::get($path);
          $response = Response($file, 200);
          $response->header('Content-Type', 'image/jpeg');
          return $response;
      
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


      public function commande_plat(Request $req){
        $validator = Validator::make($req->all(), [ 
          'id_plat' => 'required', 
          'nombre_plat' => 'required', 
          'type' => 'required', 
          'date' => 'required', 
         
      ]); 
        
            //var_dump(auth('api')->user()->id_professionnel);die();
      if ($validator->fails()) { 
          return response()->json(['error'=>$validator->errors()], 401);            
      }else{
        
        $annonce= new commande_plat;

        $annonce->nombre_plat=$req->input('nombre_plat');
        $annonce->type=$req->input('type');
        $annonce->adresse_livraison=$req->input('adresse_livraison');
        $annonce->date=$req->input('date');
        $annonce->accompagnement=$req->input('accompagnement');
        $annonce->besoin_particulier=$req->input('besoin_particulier');
        $annonce->disponible="non";
       
        $annonce->user()->associate(auth('api')->user());
        if($req->input('destinataire')){
          $article = User::where('id',$req->input('destinataire'))->first();
          $annonce->destinataire()->associate($article);
        }
        $article = plat::where('id',$req->input('id_plat'))->first();
        $annonce->plat()->associate($article);
    
        $annonce->save();
        return response()->json(['succés'=>"Enregistrement du plat avec succés"], 200);            

      }}







      // LES CONTROLLEURS DE GET METHODE
      public function getplat()
    {
      $article = plat::with(['user.professionnel','user.particulier'])->get();
      return response()->json($article); 
    }

    public function getannonce()
    {
      $article = annonce::with(['user.professionnel','user.particulier','automobile','habillement','immobilier'])->get();
      return response()->json($article); 
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
}
