<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\professionnel;
use App\categorie;
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
      $annonce =annonce::where([['statut','acceptee'],['idannonce',$id]])->select('titre','prix','localisation','idannonce','referenceannonce','idmembre','idsouscategorie','description','nomvendeur','paiementtranche','typeannonce','dateannonce','validite')->first();   
      if(File::exists(storage_path('app/public/compteur/'.$annonce->referenceannonce.'_biens.txt'))){
        $file=File::get(storage_path('app/public/compteur/'.$annonce->referenceannonce.'_biens.txt'));
        }else if(File::exists(storage_path('app/public/compteur/'.strtolower($annonce->referenceannonce).'_biens.txt'))){
          $file=File::get(storage_path('app/public/compteur/'.strtolower($annonce->referenceannonce).'_biens.txt'));
          }else {
          $file=0;
        }
        $membre = imageannonce::where('idannonce',$annonce->idannonce)->get();
        $annonce['image']=$membre;
        $user=User::with('departement')->select('prenom','nom','departement_id','localisation','profil','email','telephoneportable')->where('idmembre',$annonce->idmembre)->first();
        $annonce['vues']=$file;
        $annonce['proprietaire']=$user;
      Storage::disk('vue')->put($annonce->referenceannonce.'_biens.txt', $file+1);
      return response()->json($annonce); 
    }



    public function allannonce()
    {
      $article = annonce::select('titre','prix','localisation','idmembre','idannonce','referenceannonce')->where('statut','acceptee')->orderBy('idannonce','desc')->paginate(30);
      foreach($article as $articl){
        $membre = imageannonce::where('idannonce',$articl->idannonce)->first();
        if(File::exists(storage_path('app/public/compteur/'.$articl->referenceannonce.'_biens.txt'))){
        $file=File::get(storage_path('app/public/compteur/'.$articl->referenceannonce.'_biens.txt'));
        }else if(File::exists(storage_path('app/public/compteur/'.strtolower($articl->referenceannonce).'_biens.txt'))){
          $file=File::get(storage_path('app/public/compteur/'.strtolower($articl->referenceannonce).'_biens.txt'));
          }else {
          $file=0;
        }
        $articl['image']=$membre->urlimage;
        $articl['vues']=$file;
     //   $articl['url']="api.iveez.com/api/image/{imagename}";   
    }
    return response()->json($article); 
  }
    public function proannonce($id)
    {
      $article = annonce::select('titre','prix','localisation','idmembre','idannonce','referenceannonce')->where([['statut','acceptee'],['idmembre',$id]])->orderBy('idannonce','desc')->paginate(30);
      foreach($article as $articl){
        $membre = imageannonce::where('idannonce',$articl->idannonce)->get();
        if(File::exists(storage_path('app/public/compteur/'.$articl->referenceannonce.'_biens.txt'))){
        $file=File::get(storage_path('app/public/compteur/'.$articl->referenceannonce.'_biens.txt'));
        }else if(File::exists(storage_path('app/public/compteur/'.strtolower($articl->referenceannonce).'_biens.txt'))){
          $file=File::get(storage_path('app/public/compteur/'.strtolower($articl->referenceannonce).'_biens.txt'));
          }else {
          $file=0;
        }
        $articl['image']=$membre;
        $articl['vues']=$file;
        $articl['url']="api.iveez.com/api/image/{imagename}";
        
    }
  //  $article=$article->paginate(15);
      return response()->json($article); 
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
      $details=[];
      

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
        array_push($details, $habillement);
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
        array_push($details, $immobilier);
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
        array_push($details, $automobile);
        $automobile->annonce()->associate($annonce);
        $automobile->save();
        
      }
    
      $a=annonce::latest('idannonce')->first();
     // var_dump($a);die();
      for($i=0;$i<$req->numberOfImages;$i++){
        $iman= new imageannonce;
        $img=$req->input('image'.$i);
      
        $base64_str = substr($img, strpos($img, ",")+1);
        //var_dump($base64_str);die();
        $data = base64_decode($base64_str);
        $time=$a->idannonce+$i.'-'.time().'.png';
        Storage::disk('annonce')->put($time, $data);
        $iman->idannonce= $a->idannonce;  
        $iman->urlimage="photo/".$time;  
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
      Storage::disk('vue')->put($a->referenceannonce.'_biens.txt', 0);

      return response()->json(['succes'=>"Enregistrement de lannonce avec succes","code"=>200,
      'id_annonce'=>$a->idannonce,
      'type'=>$req->input('publish_type'),
      'structureimage'=>'api.iveez.com/api/image/{imagename}',
      'example'=>"api.iveez.com/api/image/".$url,
      'annonce'=>$annonce,
      'details'=>$details
      ]);            

    }
  }

    public function similarannonce($name)
    {
    
     // $annonce=[];
     // $annonce=souscategorie::select('id_souscat')->where('nom_souscat',$name)->first();   
      $article=annonce::select('titre','prix','localisation','referenceannonce','idannonce')->where([['idsouscategorie',$name],['statut','acceptee']])->orderBy('idannonce','desc')->paginate(30);
     // array_push($annonce, $sscat);
     foreach($article as $articl){
        $membre = imageannonce::where('idannonce',$articl->idannonce)->first();
        $articl['image']=$membre;
        if(File::exists(storage_path('app/public/compteur/'.$articl->referenceannonce.'_biens.txt'))){
          $file=File::get(storage_path('app/public/compteur/'.$articl->referenceannonce.'_biens.txt'));
          }else if(File::exists(storage_path('app/public/compteur/'.strtolower($articl->referenceannonce).'_biens.txt'))){
            $file=File::get(storage_path('app/public/compteur/'.strtolower($articl->referenceannonce).'_biens.txt'));
            }else {
            $file=0;
          }
          $articl['vues']=$file;
        
    }
      return response()->json($article); 
    }

    public function search_article($name)
    {
      $list=souscategorie::select('id_souscat')->with('categorie')->whereHas('categorie', function ($query) use($name) {
        $query->where('nom_cat', 'LIKE', '%' . $name . '%');
    })->get();
   // return response($list);
    
     $annonce=annonce::select('titre','prix','localisation','referenceannonce','idannonce','description','idsouscategorie')->where('statut','acceptee')->orderBy('idannonce','desc')->where(function ($query) use($name,$list) {
        $query->orWhere('description', 'LIKE', '%' . $name . '%');
        $query->orWhere( 'localisation', 'LIKE','%'.$name.'%');
        $query->orWhere( 'titre', 'LIKE','%'.$name.'%');
        $query->orWhere( 'referenceannonce', 'LIKE','%'.$name.'%');
        if($list){
          $query->orWhereIn('idsouscategorie', $list);
        }
      })->paginate(30);

     foreach($annonce as $articl){
      $membre = imageannonce::where('idannonce',$articl->idannonce)->first();
      $articl['image']=$membre->urlimage;
      unset($articl['description']);
      if(File::exists(storage_path('app/public/compteur/'.$articl->referenceannonce.'_biens.txt'))){
        $file=File::get(storage_path('app/public/compteur/'.$articl->referenceannonce.'_biens.txt'));
        }else if(File::exists(storage_path('app/public/compteur/'.strtolower($articl->referenceannonce).'_biens.txt'))){
          $file=File::get(storage_path('app/public/compteur/'.strtolower($articl->referenceannonce).'_biens.txt'));
          }else {
          $file=0;
        }
        $articl['vues']=$file;
      
  }
      return response($annonce); 
    }

    public function verify_contact(Request $req)
    {
      $numbers=[];
     
      foreach($req->input('numbers') as $articl){
        $test=User::select('prenom','typecompte','num_whatsapp','nom','codemembre','idmembre','profil','email','telephoneportable')->where('telephoneportable','LIKE','%'.$articl.'%')->first();
        if ($test) {
          //$numbers[$articl]=
          array_push($numbers, $test);
        }
      }
     
        
      return response()->json([
        "code"=>200,
        "response"=> $numbers
      ]); 
    }



    public function oneboutique($id)
    {
      $annonce =boutique::where([['etatshowroom','acceptee'],['idshowroom',$id]])->select('idmembre','descriptionshowroom','idshowroom','heuredebut','heurefin','logoshowroom','id_dep','idcategorieshowroom','jourdebut','jourfin','localisation','telephone','nomshowroom','logoshowroom')->first();  
      $user=User::select('prenom','nom' ,'codemembre','email')->where('idmembre',$annonce->idmembre)->first();

      if(File::exists(storage_path('app/public/compteur/'.$annonce->idshowroom.'_showrooms.txt'))){
        $file=File::get(storage_path('app/public/compteur/'.$annonce->idshowroom.'_showrooms.txt'));
        }else{
          $file=0;
        }
        $annonce['vues']=$file;
        $annonce['proprietaire']=$user;
        Storage::disk('vue')->put($annonce->idshowroom.'_showrooms.txt', $file+1);
      return response()->json($annonce); 
    }

    public function getboutique()
    {
   //   $membre = User::select('idmembre','nom','prenom','codemembre')->where('idmembre',auth('api')->user()->idmembre)->first();
      $boutique = boutique::select('idmembre','descriptionshowroom','idshowroom','heuredebut','heurefin','logoshowroom','id_dep','idcategorieshowroom','jourdebut','jourfin','localisation','telephone','nomshowroom')->where([['etatshowroom','acceptee'],['idmembre',auth('api')->user()->idmembre]])->orderBy('idshowroom','desc')->paginate(30);
      foreach($boutique as $articl){
      //  $membre = User::select('idmembre','nom','prenom','codemembre')->where('idmembre',$articl->idmembre)->first();
        //$articl['user']=$membre;
        
        if(File::exists(storage_path('app/public/compteur/'.$articl->idshowroom.'_showrooms.txt'))){
          $file=File::get(storage_path('app/public/compteur/'.$articl->idshowroom.'_showrooms.txt'));
          }else{
            $file=0;
          } 
          $articl['vues']=$file;
          $user=User::select('prenom','nom' ,'telephoneportable','codemembre','email')->where('idmembre',$articl->idmembre)->first();
          $articl['proprietaire']=$user;
    }
    
  //  $article=$article->paginate(15);
      return response()->json($boutique); 
    }
    public function getnotification()
    {
      $notification = notification::where('idmembre',auth('api')->user()->idmembre)->orderBy('idnotification','desc')->get(); 
 
  //  $article=$article->paginate(15);
      return response()->json($notification); 
    }
    public function liste_souscategorie()
    {
      $souscategorie = souscategorie::select('nom_souscat','lib_souscat','id_souscat','lib_souscaten','id_cat')->get(); 
      return response()->json($souscategorie); 
    }
    public function liste_categorie()
    {
      $notification = categorie::select('nom_cat','lib_caten','lib_cat','id_cat')->get(); 
 
  //  $article=$article->paginate(15);
      return response()->json($notification); 
    }
    public function gettransaction()
    {
      $notification = transaction::where('id_membre',auth('api')->user()->idmembre)->orderBy('id_transaction','desc')->get(); 
      
        foreach($notification as $not){
          $credit=preg_split ("/\,/", $not->description); 
          $not['credit']= $credit[0];
          $not['credit_total']= $credit[1];
        }
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
      $boutique = annoncesboutique::select('idannonceshowroom','idannonce')->where('idshowroom',$id)->orderBy('idannonceshowroom','desc')->paginate(30);
      foreach($boutique as $article){
        $membre = annonce::select('titre','prix','localisation','idannonce','referenceannonce')->where([['idannonce',$article->idannonce],['statut','acceptee']])->first();
        $image = imageannonce::where('idannonce',$article->idannonce)->get();
              
          if(File::exists(storage_path('app/public/compteur/'.$membre->referenceannonce.'_biens.txt'))){
            $file=File::get(storage_path('app/public/compteur/'.$membre->referenceannonce.'_biens.txt'));
            }else if(File::exists(storage_path('app/public/compteur/'.strtolower($membre->referenceannonce).'_biens.txt'))){
              $file=File::get(storage_path('app/public/compteur/'.strtolower($membre->referenceannonce).'_biens.txt'));
              }else {
              $file=0;
            }
            $article['articles']=$membre;  
            $article['image']=$image;   
            $article['articles']['vues']=$file;
           
      
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
      
      $boutique->dateshowroom=date("Y-m-d H:i:s");
      $img=$req->input('logo');
      if($img){
      $base64_str = substr($img, strpos($img, ",")+1);
      //var_dump($base64_str);die();
      $data = base64_decode($base64_str);
      $time=$boutique->idmembre.'-'.time().'.png';
      Storage::disk('annonce')->put($time, $data);
    
      $boutique->logoshowroom="photo/".$time; 
      }
      $boutique->save();
      return response()->json(['success'=>"Enregistrement de la boutique avec succés"], 200);            

    }}

    public function search_boutique($name)
    {
      $dept=departement::select('id_dept')->where('lib_dept','LIKE', '%' . $name . '%')->first(); 
     // return $dept;
      $list=User::where('codemembre', 'LIKE', '%' . $name . '%')->select('idmembre')->get();
      $annonce =boutique::select('idmembre','descriptionshowroom','idshowroom','heuredebut','heurefin','logoshowroom','id_dep','idcategorieshowroom','jourdebut','jourfin','localisation','telephone','nomshowroom','logoshowroom')->where('etatshowroom','acceptee')->where(function ($query) use($name,$list,$dept) {
        $query->orWhere('descriptionshowroom', 'LIKE', '%' . $name . '%');
        if($list){
       $query->orWherein( 'idmembre', $list);}
       if($dept){
        $query->orWhere( 'id_dep', $dept->id_dept);}
        $query->orWhere( 'localisation', 'LIKE','%'.$name.'%');
        $query->orWhere( 'nomshowroom', 'LIKE','%'.$name.'%');})->paginate(30);
  
   //   $sscat =souscategorie::select('id_souscat')->where('nom_souscat','LIKE','%'.$name.'%')->get(); 
     // echo($sscat);
     foreach($annonce as $ann){
     if(File::exists(storage_path('app/public/compteur/'.$ann->idshowroom.'_showrooms.txt'))){
      $file=File::get(storage_path('app/public/compteur/'.$ann->idshowroom.'_showrooms.txt'));
      }else{
        $file=0;
      }
      $ann['vues']=$file;}

      return response($annonce); 
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
      $transaction->description=$req->credit.",".$user->compte;
      $transaction->save();
      $notification= new notification;
      $notification->idmembre=auth('api')->user()->idmembre;
      $notification->date=date("Y-m-d H:i:s");
      $notification->message="Achat credit :".$req->credit.". Credit total: ".$user->compte;
      $notification->save();
      return response()->json(['success'=>'Enregistré'], 200); 
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
      $transaction->description=$req->credit.",".$user->compte;
      $transaction->save();
      $notification= new notification;
      $notification->idmembre=auth('api')->user()->idmembre;
      $notification->date=date("Y-m-d H:i:s");
      $notification->message="Credit vendu: ".$req->credit.". Credit total: ".$user->compte;
      $notification->save();
      return response()->json(['success'=>'Enregistré'], 200); 
    }

    public function ajout_panier($id)
    {
      $panier= new panier;
      $panier->idmembre=auth('api')->user()->idmembre;
      $panier->idannonce=$id;
      $panier->statut='';
      $panier->date=date("Y-m-d H:i:s");
      $panier->save();
   
     
      return response()->json(['success'=>"Ajout panier avec succés"], 200); 
    }
    public function getboutiqueservice()
    {
      $list=[28,29,30];
      $servicevendu = servicevendu::select('idannonce','idservice')->whereIn('idservice', $list)->where('datefinservice', '>=', date('Y-m-d H:i:s'))->orderBy('idvente','desc')->paginate(30);
      foreach($servicevendu as $articl){
        $annonce = boutique::select('idmembre','descriptionshowroom','idshowroom','heuredebut','heurefin','logoshowroom','id_dep','idcategorieshowroom','jourdebut','jourfin','localisation','telephone','nomshowroom')->where([['idshowroom',$articl->idannonce],['etatshowroom','acceptee']])->first();
        
        $service = service::select('idservice','nomService')->where('idservice',$articl->idservice )->first();
        if(File::exists(storage_path('app/public/compteur/'.$annonce->idshowroom.'_showrooms.txt'))){
        $file=File::get(storage_path('app/public/compteur/'.$annonce->idshowroom.'_showrooms.txt'));
        }else if(File::exists(storage_path('app/public/compteur/'.strtolower($annonce->idshowroom).'_showrooms.txt'))){
          $file=File::get(storage_path('app/public/compteur/'.strtolower($annonce->idshowroom).'_biens_showrooms.txt'));
          }else {
          $file=0;
        }
        unset($articl['idannonce']);
        unset($articl['idservice']);
        $articl['boutique']=$annonce;
        $articl['service']=$service->nomService;
        $articl['vues']=$file;
        
    }
     
      return response()->json($servicevendu); 
    }
    public function delete_panier($id)
    {
       
      $result=panier::where('idannonce','=',$id)->delete(); 
     
      return response()->json(['success'=>"Suppression de l'article dans le panier avec succés"], 200); 
    }
    public function liste_panier($id)
    {
      $panier =panier::select('idannonce','idpanier')->where([['idmembre','=',$id],['statut','!=','commandé']])->get();
    
      foreach($panier as $articl){
        
        $membre = annonce::select('localisation','idannonce','idsouscategorie','prix','referenceannonce','titre','validite','idmembre')->where('idannonce',$articl->idannonce)->first();
        $articl['annonce']=$membre;
        $image = imageannonce::select('urlimage')->where('idannonce',$membre->idannonce)->first();
        $articl['annonce']['image']=$image;
        $articl['url']="api.iveez.com/api/image/{imagename}";
        
    } 
     if($panier->isEmpty()){
     
      $panier=0;
     }
      return response()->json($panier); 
    }


    public function commander(Request $req)
    {
     
      
      foreach($req->panier as $reqpanier){
       
        $panier =panier::with('annonce')->where('idpanier','=',$reqpanier['idpanier'])->first();
      //return $panier;
      $commande= new commande;
      //$commande->idpanier=$reqpanier['idpanier'];
      $commande->panier()->associate($panier);
      $commande->quantite=$reqpanier['quantite'];
      $commande->datecommande=date("Y/m/d");
      $commande->statut="en attente";
      $panier->statut="commandé";
      $panier->save();
      //$number = commande::select('idcommande')->latest();  
      $commande->reference=$panier->annonce->referenceannonce."c".$reqpanier['idpanier'].date("dmY");
      $commande->save();
      
      $notification= new notification;
      $notification->idmembre=$panier->annonce->idmembre;
      $notification->date=date("Y-m-d H:i:s");
      $notification->message=$panier->annonce->titre." a été commandé. ";
      $notification->save();
      }
      $notification= new notification;
      $notification->idmembre=auth('api')->user()->idmembre;
      $notification->date=date("Y-m-d H:i:s");
      $notification->message="Commande en attente de validatiation par le(s) propiétaire(s)";
      $notification->save();
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
      $servicevendu = servicevendu::select('idannonce','idservice')->where('datefinservice', '>=', date('Y-m-d H:i:s'))->orderBy('idvente','desc')->paginate(30);
      foreach($servicevendu as $articl){
        $annonce = annonce::select('titre','prix','localisation','idmembre','idannonce','referenceannonce')->where([['idannonce',$articl->idannonce],['statut','acceptee']])->first();
        
        $service = service::select('idservice','nomService')->where('idservice',$articl->idservice )->first();
        $img = imageannonce::where('idannonce',$annonce->idannonce)->first();
        if(File::exists(storage_path('app/public/compteur/'.$annonce->referenceannonce.'_biens.txt'))){
        $file=File::get(storage_path('app/public/compteur/'.$annonce->referenceannonce.'_biens.txt'));
        }else if(File::exists(storage_path('app/public/compteur/'.strtolower($annonce->referenceannonce).'_biens.txt'))){
          $file=File::get(storage_path('app/public/compteur/'.strtolower($annonce->referenceannonce).'_biens.txt'));
          }else {
          $file=0;
        }
        
        $articl['article']=$annonce;
        $articl['service']=$service->nomService;
        $articl['image']=$img->urlimage;
        $articl['vues']=$file;
        
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
    public function payepourmoi($id)
    {
      $notification= new notification;
      $notification->idmembre=$id;
      $user =auth('api')->user();
      $notification->date=date("Y-m-d H:i:s");
      $notification->message=$user->prenom.' '.$user->nom." vous sollicite pour le paiement de sa commande sur iveez ";
      $notification->save();

      $notification= new notification;
      $notification->idmembre=$user>idmembre;
      $notification->date=date("Y-m-d H:i:s");
      $notification->message="Demande envoyée avec succés, vous recevrez une notification en cas de retour.";
      $notification->save();
  //  $article=$article->paginate(15);
      return response()->json(['success'=>"Succés de la commande"], 200); 
    }
    public function listecommande()
    {
      $service = commande::select('idpanier','datecommande','reference','quantite')->where('statut','en attente')->whereHas('panier', function ($query) {
       
        $query->where('statut', 'commandé');
    })->get();
    foreach($service as $articl){
      $membre = annonce::select('localisation','idmembre','idsouscategorie','prix','referenceannonce','titre','idannonce')->where([['idannonce',$articl->panier->idannonce],['statut','acceptee']])->first();
     // return $membre;
     $user = User::select('prenom','nom','telephoneportable','email','localisation','idmembre','codemembre')->where('idmembre',$membre['idmembre'])->first();

      $image = imageannonce::select('urlimage','idannonce')->where('idannonce',$membre['idannonce'])->first();
      
      $articl['panier']['annonce']=$membre;
      $articl['panier']['proprietaire']=$user;
      $articl['panier']['image']=$image['urlimage'];
      unset($articl['panier']['idpanier']);unset($articl['panier']['idmembre']);
      unset($articl['panier']['idannonce']);unset($articl['panier']['statut']);
      unset($articl['panier']['date']);unset($articl['idpanier']);unset($articl['panier']['quantite']);
  }
  //  $article=$article->paginate(15);
      return response()->json($service); 
    }

    public function statutcompte($id)
    {
     // $commande= new commande;
      $user=User::where('idmembre','=',auth('api')->user()->idmembre)->first(); 
      $user->etatcompte=$id;
      $user->save();
      return response()->json(['success'=>"Statut de l'utilisateur mise à jour"], 200);   
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
  if($req->input('categorie')=='habillement'){
    $list=habillement::select('idannonce')->get();
  }else if($req->input('categorie')=='automobile'){
    $list=automobile::select('idannonce')->get();
  }else if($req->input('categorie')=='immobilier'){
    $list=immobilier::select('idannonce')->get();
  }else{
    $list='';
  }
  
//var_dump($results);die();
    $annonce= annonce::with('departement')->where(function ($query) use($req,$list) {
    $query->Where( 'statut','acceptee');
      $query->where('referenceannonce', 'LIKE', '%' . $req->input('reference') . '%');
      $query->Where( 'localisation', 'LIKE','%'.$req->input('localisation').'%');
      $query->Where( 'titre', 'LIKE','%'.$req->input('titre').'%');
      if($req->input('prix_min') && $req->input('prix_max')){
        $query->Where( 'prix','>=',$req->input('prix_min'));
        $query->Where( 'prix','<=',$req->input('prix_max'));
      }else if($req->input('prix_max') && !$req->input('prix_min')){
        $query->Where( 'prix','<=',$req->input('prix_max'));
      }else if(!$req->input('prix_max') && $req->input('prix_min')){
        $query->Where( 'prix','>=',$req->input('prix_min'));
      }
    if($list!=''){
      $query->whereIn('idannonce', $list);
    }
    
    //  $query->orwhere($field, 'like',  '%' . $string .'%');
    
  })->whereHas('departement', function ($query) use ($req) {
    $query->where('lib_dept', 'LIKE', '%' .$req->input('departement'). '%');
  })
  ->get();
  
    return response($annonce); 
  }
}
