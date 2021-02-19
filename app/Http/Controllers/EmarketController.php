<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\professionnel;
use App\plat;
use App\panier;
use App\region;
use App\chambre;
use App\commande;
use App\evenement;
use App\notification;
use App\vehicule;
use App\restauration;
use App\User;
use App\departement;
use App\souscategorie;
use App\annonce;
use App\automobile;
use App\commande_plat;
use App\habillement;
use App\immobilier;
use App\transaction;
use App\boutique;
use App\annoncesboutique;
use App\servicevendu;
use App\service;
use App\imageannonce;
use Validator;
use File;
use Illuminate\Support\Facades\Storage;

class EmarketController extends Controller
{
    public function oneannonce($id)
    {
      $annonce =annonce::find($id);  
      return response()->json($annonce); 
    }
    
    public function annonce(Request $req){
      $validator = Validator::make($req->all(), [ 
        'city' => 'required', 
        'publish_type' => 'required', 
        'price' => 'required', 
        'payment_type' => 'required', 
        'title' => 'required', 
        'description' => 'required', 
        'publish_type' => 'required', 
    ]); 
      
          //var_dump(auth('api')->user()->id_professionnel);die();
    if ($validator->fails()) { 
        return response()->json(['error'=>$validator->errors()], 401);            
    }else{
      $annonce= new annonce;
      $ss=souscategorie::where('lib_souscat',$req->input('subcategory'))->first(); 
      $annonce->idsouscategorie=$ss->id_souscat;
      $annonce->prix=$req->input('price');
      $article = annonce::all();  
      $artic= count($article)+1;
      $annonce->referenceannonce=auth('api')->user()->codemembre.'-'.$artic;
      $annonce->typeannonce=$req->input('publish_type');
      $annonce->paiementtranche=$req->input('payment_type');
      $dept=departement::where('lib_dept',$req->input('city'))->first(); 
      $annonce->departement()->associate($dept);
      $annonce->titre=$req->input('title');
      $annonce->troc='non';
      $annonce->statutvente='en vente';
      $annonce->statut='en attente';
      $annonce->localisation=$req->input('localisation');
      $annonce->description=$req->input('description');
      $annonce->dateannonce=date("Y-m-d H:i:s");
      $annonce->idmembre=auth('api')->user()->idmembre;

      

      if($req->input('categorie')=="Habillement et accessoires"){
        $habillement= new habillement;
        $habillement->type=$req->input('clothing_type');     
        $habillement->marque=$req->input('brand');  
        $habillement->modele=$req->input('model');  
        $habillement->couleur=$req->input('color');  
        $habillement->taille=$req->input('size');  
        $annonce->save();
        $habillement->annonce()->associate($annonce);
        $habillement->save();
      }else if($req->input('categorie')=="Immobilier" ){
        $immobilier= new immobilier;
        $immobilier->surface=$req->input('surface');     
       
        $immobilier->typeoperation=$req->input('type');   
        $immobilier->nombrepiece=$req->input('n_rooms');  
        $immobilier->datedisponibilite=$req->input('open_date');  
        $immobilier->droitvisite=$req->input('visit_amount');  
        $immobilier->montantdroit=$req->input('montant');  
        $annonce->save();
        $a=annonce::latest('idannonce')->first();
        $immobilier->idannonce=$a->idannonce;
        $immobilier->save();
      } else if($req->input('categorie')=="Automobile et Autres" ){
        $automobile= new automobile;
        
        $automobile->typeoperation=$req->input('type');  
        $automobile->couleur=$req->input('color');  
        $automobile->kilometre=$req->input('mileage');  
        $automobile->puissance=$req->input('power');  
        $automobile->boite=$req->input('gearbox');  
        $automobile->carburant=$req->input('fuel_type');  
        $automobile->jante=$req->input('rim_type');  
        $automobile->cylindre=$req->input('n_cylinders'); 
        $annonce->save();
        $automobile->annonce()->associate($annonce);
        $automobile->save();
      }
    
      $a=annonce::latest('idannonce')->first();
     
      for($i=0;$i<$req->numberOfImages;$i++){
        $iman= new imageannonce;
        $img=$req->input('image'.$i);
      
        $base64_str = substr($img, strpos($img, ",")+1);
        //var_dump($base64_str);die();
        $data = base64_decode($base64_str);
        $time="photo/".$a->idannonce+$i.'-'.time().'.png';
        Storage::disk('annonce')->put($time, $data);
        $iman->idannonce= $a->idannonce;  
        $iman->urlimage=$time;  
        $iman->parametre=$i; 
       
        $iman->save();
      }
      $url=$time;
      if($req->input('publish_type')=='article'){
      
        $iman= new annoncesboutique;
        $iman->idannonce= $a->idannonce;  
        $iman->idshowroom=$req->input('idshowroom');  
        $iman->visibilite=0; 
       
        $iman->save();
      }
      
      return response()->json(['succes'=>"Enregistrement de lannonce avec succes","code"=>200,
      'id_annonce'=>$a->idannonce,
      'type'=>$req->input('publish_type'),
      'structureimage'=>'api.iveez.com/api/image/{type_publication}/{imagename}',
      'example'=>"api.iveez.com/api/image/annonce/".$url,
      'annonce'=>$annonce,
      ]);            

    }
  }

    public function similarannonce($name)
    {
    
     // $annonce=[];
      $sscat =souscategorie::select('id_souscat')->where('nom_souscat',$name)->first();   
     // $annonce=souscategorie::select('id_souscat')->where('nom_souscat',$name)->first();   
      $article=annonce::select('titre','prix','localisation','idannonce')->where('idsouscategorie',$sscat->id_souscat)->orderBy('idannonce','desc')->get();
     // array_push($annonce, $sscat);
     foreach($article as $articl){
        $membre = imageannonce::where('idannonce',$articl->idannonce)->get();
        $articl['image']=$membre;
        $articl['url']="api.iveez.com/api/image/{imagename}" ;
        
    }
      return response()->json($article); 
    }

    public function search_article($name)
    {
     $annonce=annonce::where([['titre','LIKE','%'.$name.'%'],['statut','acceptee']])->get();  
 
      return response($annonce); 
    }

    public function verify_contact($numero)
    {
      if (User::where('telephoneportable', '=', $numero)->exists()) {
        return response()->json([
          "code"=>200,
          "response"=> "number ".$numero." exist"
        ]); 
     }
      return response()->json([
        "code"=>400,
        "response"=> "number ".$numero." doesn't exist"
      ]); 
    }



    public function oneboutique($id)
    {
      $annonce =boutique::find($id);  
      return response()->json($annonce); 
    }

    public function getboutique()
    {
      $boutique = boutique::where('etatshowroom','acceptee')->orderBy('idshowroom','desc')->paginate(30);
 
  //  $article=$article->paginate(15);
      return response()->json($boutique); 
    }
    public function getnotification()
    {
      $notification = notification::where('idmembre',auth('api')->user()->idmembre)->orderBy('idnotification','desc')->get(); 
 
  //  $article=$article->paginate(15);
      return response()->json($notification); 
    }
    public function removenotification($id)
    {
      $notification = notification::where('idnotification','=',$id)->delete(); ; 
 
  //  $article=$article->paginate(15);
   
      return response()->json(['success'=>"Suppression de la notification dans le panier avec succés"], 200); 
    }
    public function getarticleboutique($id)
    {
      $boutique = annoncesboutique::where('idshowroom',$id)->orderBy('idannonceshowroom','desc')->paginate(30);
      foreach($boutique as $articl){
        $membre = annonce::where([['idannonce',$articl->idannonce],['statut','acceptee']])->get();
        $articl['articles']=$membre;
        $articl['url']="api.iveez.com/api/image/{imagename}";
        
    }
  //  $article=$article->paginate(15);
      return response()->json($boutique); 
    }
    public function getuserboutique($id)
    {
      $boutique = boutique::where([['idmembre',$id],['etatshowroom','acceptee']])->orderBy('idshowroom','desc')->paginate(30);
 
  //  $article=$article->paginate(15);
      return response()->json($boutique); 
    }
    public function boutique(Request $req){
      $validator = Validator::make($req->all(), [ 
        'id_dep' => 'required', 
        'nomshowroom' => 'required', 
       
    ]); 
      
          //var_dump(auth('api')->user()->id_professionnel);die();
    if ($validator->fails()) { 
        return response()->json(['error'=>$validator->errors()], 401);            
    }else{
      
      $boutique= new boutique;

      $boutique->idmembre=auth('api')->user()->idmembre;
      $boutique->etatshowroom="en attente";
      $boutique->id_dep=$req->input('id_dep');
      $boutique->localisation=$req->input('localisation');
      $boutique->idcategorieshowroom=$req->input('idcategorieshowroom');
      $boutique->nomshowroom=$req->input('nomshowroom');
      $boutique->descriptionshowroom=$req->input('descriptionshowroom');
      $boutique->telephone=$req->input('telephone');
      $boutique->jourdebut=$req->input('jourdebut');
      $boutique->jourfin=$req->input('jourfin');
      $boutique->heuredebut=$req->input('heuredebut');
      $boutique->heurefin=$req->input('heurefin');
      $boutique->siteweb=$req->input('siteweb');
      
      $boutique->dateshowroom=date("Y-m-d H:i:s");
      $img=$req->input('logo');
      
      $base64_str = substr($img, strpos($img, ",")+1);
      //var_dump($base64_str);die();
      $data = base64_decode($base64_str);
      $time=$a->idannonce+$i.'-'.time().'.png';
      Storage::disk('annonce')->put($time, $data);
    
      $boutique->logoshowroom="photo/".$time; 
      $boutique->save();
      return response()->json(['success'=>"Enregistrement de la boutique avec succés"], 200);            

    }}

    public function search_boutique($name)
    {
      $annonce =boutique::where('nomshowroom','LIKE','%'.$name.'%')->get();  
   //   $sscat =souscategorie::select('id_souscat')->where('nom_souscat','LIKE','%'.$name.'%')->get(); 
     // echo($sscat);
     
      return response()->json($annonce); 
    }

    public function ajout_credit(Request $req)
    {
      $user =User::find(auth('api')->user()->idmembre);
      $user->compte =$user->compte+$req->credit;  
      $user->save(); 
      $transaction= new transaction;
      $transaction->id_membre=auth('api')->user()->idmembre;
      $transaction->type="achat";
      $transaction->date=date("Y-m-d H:i:s");
      $transaction->description="Achat credit de ".$req->credit.". Credit total: ".$user->compte;
      $transaction->save();
      $notification= new notification;
      $notification->idmembre=auth('api')->user()->idmembre;
      $notification->date=date("Y-m-d H:i:s");
      $notification->message="Achat credit de ".$req->credit.". Credit total: ".$user->compte;
      $notification->save();
      return response()->json(['success'=>$transaction->description], 200); 
    }
    public function remove_credit(Request $req)
    {
      $user =User::find(auth('api')->user()->idmembre);
      if($user->compte<$req->credit){
        return response()->json(['error'=>"Credit insuffisant"], 200);
      }
      $user->compte =$user->compte-$req->credit;  
      $user->save(); 
      $transaction= new transaction;
      $transaction->id_membre=auth('api')->user()->idmembre;
      $transaction->type="vente";
      $transaction->date=date("Y-m-d H:i:s");
      $transaction->description="Credit vendu: ".$req->credit.". Credit total: ".$user->compte;
      $transaction->save();
      $notification= new notification;
      $notification->idmembre=auth('api')->user()->idmembre;
      $notification->date=date("Y-m-d H:i:s");
      $notification->message="Credit vendu: ".$req->credit.". Credit total: ".$user->compte;
      $notification->save();
      return response()->json(['success'=>$transaction->description], 200); 
    }

    public function ajout_panier($id)
    {
      $panier= new panier;
      $panier->idmembre=auth('api')->user()->idmembre;
      $panier->idannonce=$id;
      $panier->date=date("Y-m-d H:i:s");
      $panier->save();
   
     
      return response()->json(['success'=>"Ajout panier avec succés"], 200); 
    }
    public function delete_panier($id)
    {
       
      $result=panier::where('idannonce','=',$id)->delete(); 
     
      return response()->json(['success'=>"Suppression de l'article dans le panier avec succés"], 200); 
    }
    public function liste_panier()
    {
      $panier =panier::where('idmembre','=',auth('api')->user()->idmembre)->get();
    
      foreach($panier as $articl){
        $membre = annonce::where([['idannonce',$articl->idannonce],['statut','acceptee']])->get();
        $articl['annonce']=$membre;
        $articl['url']="api.iveez.com/api/image/{imagename}";
        
    } 
     if($panier->isEmpty()){
     
      $panier=0;
     }
      return response()->json($panier); 
    }


    public function commander(Request $req)
    {
      $commande= new commande;

      $commande->idpanier=$req->input('idpanier');
      $commande->datecommande=date("Y-m-d H:i:s");
      $commande->statut="en attente";
      $commande->save();
      return response()->json(['success'=>"Enregistrement. Commande en attente de validatiation par le propiétaire"], 200);            
    }
    public function modifiercommande(Request $req)
    {
     // $commande= new commande;
      $result=commande::where('idcommande','=',$req->input('idcommande'))->first(); 
      
      $result->datecommande=date("Y-m-d H:i:s");
      $result->statut=$req->input('statut');
      $result->save();
      return response()->json(['success'=>"Modification de la commande"], 200);            
    }
    public function supprimercommande($id)
    {
       
      $result=commande::where('idcommande','=',$id)->delete(); 
     
      return response()->json(['success'=>"Suppression de la commande avec succés"], 200); 
    }

    public function getarticleservice()
    {
      $servicevendu = servicevendu::orderBy('idvente','desc')->paginate(30);
      foreach($servicevendu as $articl){
        $membre = annonce::where([['idannonce',$articl->idannonce],['statut','acceptee']])->first();
        $articl['article']=$membre;
        $service = service::where('idservice',$articl->idservice )->first();
        $articl['service']=$service;
        
    }
  //  $article=$article->paginate(15);
      return response()->json($servicevendu); 
    }
    public function listeservice()
    {
      $service = service::paginate(40);
    
  //  $article=$article->paginate(15);
      return response()->json($service); 
    }
    public function listecommande()
    {
      $service = commande::with('panier')->whereHas('panier', function ($query) {
        $query->where('idmembre', auth('api')->user()->idmembre);
    })->get();
    foreach($service as $articl){
      $membre = annonce::where([['idannonce',$articl->panier->idannonce],['statut','acceptee']])->first();
      $articl['panier']['annonce']=$membre;
      
  }
  //  $article=$article->paginate(15);
      return response()->json($service); 
    }

    public function imageprofil(Request $req)
    {
     // $commande= new commande;
      $result=User::where('idmembre','=',auth('api')->user()->idmembre)->first(); 
      
      $img=$req->input('image');
      
        $base64_str = substr($img, strpos($img, ",")+1);
        //var_dump($base64_str);die();
        $data = base64_decode($base64_str);
        $time=$result->idmembre.'-'.time().'.png';
        Storage::disk('profil')->put($time, $data);
        $result->profil="profil/".$time ;

        $result->save();
      return response()->json(['success'=>"Image de l'utilisateur mise à jour"], 200);            
    }
    public function filter_article(Request $req)
    {
   //  $annonce=annonce::where([['titre','LIKE','%'.$req->input('titre').'%'],['referenceannonce','LIKE','%'.$req->input('reference').'%'],['titre','LIKE','%'.$req->input('titre').'%'],['statut','acceptee']])->get();  
   if(!is_null( $req->input('categorie'))){
    $results = souscategorie::where('nom_souscat' ,'LIKE', '%' . $req->input('categorie') . '%')->first();

   }else{
    $results=NULL;
   }
  //var_dump($results);die();
     $annonce= annonce::with('departement')->where(function ($query) use($req,$results) {
      $query->Where( 'statut','acceptee');
        $query->where('referenceannonce', 'LIKE', '%' . $req->input('reference') . '%');
        $query->Where( 'localisation', 'LIKE','%'.$req->input('localisation').'%');
        $query->Where( 'titre', 'LIKE','%'.$req->input('titre').'%');
        if($req->input('prix_min')){
          $query->Where( 'prix','>',$req->input('prix_min'));
        }
        if($req->input('prix_max')){
          $query->Where( 'prix','<',$req->input('prix_max'));
        }
        if(!is_null($results)){
          $query->Where( 'idsouscategorie', $results->id_souscat);
         }
      
      //  $query->orwhere($field, 'like',  '%' . $string .'%');
      
    })->whereHas('departement', function ($query) use ($req) {
      $query->where('lib_dept', 'LIKE', '%' .$req->input('departement'). '%');
    })
    ->get();
   
      return response($annonce); 
    }
}