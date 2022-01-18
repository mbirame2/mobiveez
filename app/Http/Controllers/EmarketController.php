<?php

namespace App\Http\Controllers;
use File;
use App\plat;
use App\User;
use Validator;
use App\marque;
use App\modele;
use App\panier;
use App\region;
use App\annonce;
use App\chambre;
use App\favoris;
use App\service;
use App\boutique;
use App\commande;
use App\vehicule;
use App\categorie;
use App\evenement;
use App\automobile;
use App\immobilier;
use App\departement;
use App\habillement;
use App\hebergement;
use App\marque_moto;
use App\transaction;
use App\gestionnaire;
use App\imageannonce;
use App\notification;
use App\restauration;
use App\servicevendu;
use App\commande_plat;
use App\professionnel;
use App\souscategorie;
use App\Mail\StatutUser;
use App\propositionprix;
use App\annoncesboutique;

use App\reservationtable;
use App\livraisoncommande;
use Illuminate\Http\Request;
use App\commanderestauration;
use App\tarificationlivraison;
use App\commandereservationtable;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Scheduling\Schedule;

class EmarketController extends Controller
{

    public function oneannonce($id)
    {
      $annonce =annonce::with('departement')->where('idannonce',$id)->select('titre','prix','bloquer_commande','statut','localisation','id_dep','idannonce','referenceannonce','idmembre','idsouscategorie','description','nomvendeur','paiementtranche','typeannonce','dateannonce','validite')->first();   
      if($annonce){
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
       
        $annoncesboutique=  annoncesboutique::select("idshowroom")->where('idannonce',$annonce->idannonce)->first();
        #return $annoncesboutique;
        if($annoncesboutique){
           $annonce['idshowroom']=$annoncesboutique->idshowroom;
        }else{
           
          $annonce['idshowroom']=null;
        }

        $habillement=habillement::where('idannonce',$annonce->idannonce)->first();
        $immobilier=immobilier::where('idannonce',$annonce->idannonce)->first();
        $automobile=automobile::where('idannonce',$annonce->idannonce)->first();
        if($habillement){
          $annonce['habillement']=$habillement;
        }else if($immobilier){
          $annonce['immobilier']=$immobilier;
        }else if($automobile){
          if($automobile->idmodelevoiture!=0){
            $modele= modele::where( 'idmodelevoiture', $automobile->idmodelevoiture)->first() ;
            $marque=marque::where( 'idmarquevoiture', $modele->idmarquevoiture)->first(); 
            $automobile['modele']=$modele->designation_modelevoiture;
            $automobile['idmodelevoiture']=$modele->idmodelevoiture;
            $automobile['idmarquevoiture']=$modele->idmarquevoiture;
  
            $automobile['marque']=$marque->designation_marquevoiture;
  
          }else{
            $automobile['idmodelevoiture']=$automobile['idmarquevoiture']=$automobile['marque']=$automobile['modele']=null;
  
          }
         
          $annonce['automobile']=$automobile;
        }
      Storage::disk('vue')->put($annonce->referenceannonce.'_biens.txt', $file+1);
      return response()->json($annonce); 
      } else{
        return response()->json(['response'=>null]); 
      }
    }



    public function allannonce($pays)
    {
      $article = annonce::select('titre','prix','localisation','statut','idmembre','idannonce','referenceannonce')->where('referenceannonce', 'like', $pays.'%')->where('statut','acceptee')->orderBy('idannonce','desc')->paginate(30);
      $allannonce=[];
      foreach($article as $articl){
        $membre = imageannonce::where('idannonce',$articl->idannonce)->first();
        if(File::exists(storage_path('app/public/compteur/'.$articl->referenceannonce.'_biens.txt'))){
        $file=File::get(storage_path('app/public/compteur/'.$articl->referenceannonce.'_biens.txt'));
        }else if(File::exists(storage_path('app/public/compteur/'.strtolower($articl->referenceannonce).'_biens.txt'))){
          $file=File::get(storage_path('app/public/compteur/'.strtolower($articl->referenceannonce).'_biens.txt'));
          }else {
          $file=0;
        }
        if($membre){
          $articl['image']=$membre->urlimage;
          $articl['imageparametre']=$membre->parametre;
        }
       
        $articl['vues']=$file;
     //   $articl['url']="api.iveez.com/api/image/{imagename}";   
    }
    return response()->json($article); 
  }
    public function proannonce($id)
    {
      $article = annonce::select('titre','prix','statut','localisation','idmembre','idannonce','referenceannonce','bloquer_commande')->where([['statut','!=','suppression'],['idmembre',$id]])->orderBy('idannonce','desc')->paginate(30);
      foreach($article as $articl){
        $membre = imageannonce::where('idannonce',$articl['idannonce'])->get();
        if(File::exists(storage_path('app/public/compteur/'.$articl->referenceannonce.'_biens.txt'))){
        $file=File::get(storage_path('app/public/compteur/'.$articl->referenceannonce.'_biens.txt'));
        }else if(File::exists(storage_path('app/public/compteur/'.strtolower($articl->referenceannonce).'_biens.txt'))){
          $file=File::get(storage_path('app/public/compteur/'.strtolower($articl->referenceannonce).'_biens.txt'));
          }else {
          $file=0;
        }
        $annoncesboutique=  annoncesboutique::select("idannonceshowroom","idshowroom")->where('idannonce',$articl->idannonce)->first();
        #return $annoncesboutique;
        if($annoncesboutique){
          $articl['idannonceshowroom']=$annoncesboutique->idannonceshowroom;
          $articl['idshowroom']=$annoncesboutique->idshowroom;
        }else{
          $articl['idannonceshowroom']=null;
          $articl['idshowroom']=null;
        }
        $prix=  propositionprix::where([['idannonce',$articl->idannonce],['statut','=',null]])->count();
        $articl['total_offer']=$prix;
        $articl['image']=$membre;
        
        $articl['vues']=$file;
        $servicevendu = servicevendu::select('idservice','dateachat','datefinservice')->where('idannonce', $articl->idannonce)->where('datefinservice', '>=', date('Y-m-d H:i:s'))->first();
       //return response()->json($servicevendu->idservice); 
        if($servicevendu){
        $service=service::where('idService',$servicevendu->idservice)->first();
        $service['dateachat']=$servicevendu['dateachat'];
        $service['datefinservice']=$servicevendu['datefinservice'];
        $articl['service']=$service;
      //  $articl['service']['dateachat']=$servicevendu['dateachat'];
        //$articl['service']['datefinservice']=$servicevendu['datefinservice'];
      }else{
        $articl['service']=null;
      }
        
        
    }
  //  $article=$article->paginate(15);
      return response()->json($article); 
    }


    public function annonce(Request $req, ApiController $apicontroller){
      $validator = Validator::make($req->all(), [ 
        'publish_type' => 'required', 
        'price' => 'required', 
        'payment_type' => 'required', 
        'title' => 'required', 
    ]); 
      
          //var_dump(auth('api')->user()->id_professionnel);die();
    if ($validator->fails()) { 
        return response()->json(['error'=>$validator->errors()], 401);            
    }else{
      $annonce= new annonce;
      //$ss=souscategorie::where('id_souscat',$req->input('subcategory'))->first(); 
      $annonce->idsouscategorie=$req->input('subcategory');
      $annonce->prix=$req->input('price');
      $article = annonce::all();  
      $artic= count($article)+1;
      $annonce->referenceannonce=$req->input('codemembre').'-'.$artic;
      $annonce->typeannonce=$req->input('publish_type');
      $annonce->paiementtranche=$req->input('payment_type');
      $dept=departement::where('id_dept',$req->input('id_dept'))->first(); 
      $annonce->departement()->associate($dept);
      $annonce->titre=$req->input('title');
      $annonce->troc='non';
      $annonce->statutvente='en vente';
      $annonce->statut='en attente';
      $annonce->localisation=$req->input('localisation');
      $annonce->description=$req->input('description');
      $annonce->dateannonce=date("Y-m-d H:i:s");
      $annonce->idmembre=$req->input('idmembre');
      $details=[];
      

      if($req->input('categorie')==9){
        $habillement= new habillement;
        $habillement->type=$req->input('clothing_type');     
        $habillement->marque=$req->input('brand');  
        $habillement->modele=$req->input('model');  
        $habillement->couleur=$req->input('color');  
        $habillement->taille=$req->input('size'); 
        $det=$habillement; 
        $annonce->save();
       
        $habillement->annonce()->associate($annonce);
        $habillement->save();
        array_push($details, $habillement);
      }else if($req->input('categorie')==1 ){
        $immobilier= new immobilier;
        $immobilier->surface=$req->input('surface');     
       
        $immobilier->typeoperation=$req->input('type');   
        $immobilier->nombrepiece=$req->input('n_rooms');  
        $immobilier->datedisponibilite=$req->input('open_date');  
        $immobilier->droitvisite=$req->input('visit_amount');  
        $immobilier->montantdroit=$req->input('montant');  
        $det=$immobilier;
        $annonce->save();
        $a=annonce::latest('idannonce')->first();
        $immobilier->idannonce=$a->idannonce;
        $immobilier->save();
        $annonce['immobilier']=$immobilier;
      //  array_push($details, $immobilier);
        array_push($details, $annonce);
      } else if($req->input('categorie')==3 ){
        $modele=null;
        $marque=null;
        $automobile= new automobile;
        $marque=marque::where( 'idmarquevoiture', $req->input('brand'))->first(); 
        
        if( $marque){
          $modele=new modele ;
          $modele->designation_modelevoiture=$modele->designation_modelevoitureen=$modele->designation_modelevoitureeng=$req->input('model');
          $modele->idmarquevoiture=$marque->idmarquevoiture;
          $modele->save();
          $a=modele::latest('idmodelevoiture')->first();
          $automobile->idmodelevoiture=$a->idmodelevoiture;
        }
        

        $automobile->vehicule_type=$req->input('vehicle_type'); 
        $automobile->place=$req->input('place');
       
        $automobile->climatisation=$req->input('air_conditionning');
        $automobile->typeoperation=$req->input('type');  
        $automobile->couleur=$req->input('color');  
        $automobile->kilometre=$req->input('mileage');  
        $automobile->puissance=$req->input('power');  
        $automobile->boite=$req->input('gearbox');  
        $automobile->carburant=$req->input('fuel_type');  
        $automobile->jante=$req->input('rim_type');  
        $automobile->cylindre=$req->input('n_cylinders'); 
      
        $annonce->save();
        //$det=$automobile;
        $automobile->annonce()->associate($annonce);
        $automobile->save();
        $detail=$automobile;
        if($modele!=null){
          $detail['model']=$modele->designation_modelevoiture;
         
        }
        if($marque){
          $detail['brand']=$marque->designation_marquevoiture;
        }
          
      
       
        array_push($details, $detail);
      }else{
        $annonce->save();
        array_push($details, $annonce);
      }
    
      $a=annonce::latest('idannonce')->first();
     // var_dump($a);die();
      for($i=0;$i<$req->numberOfImages;$i++){
        $iman= new imageannonce;
      //  $img=$req->input('image'.$i);
      
     //   $base64_str = substr($img, strpos($img, ",")+1);
        //var_dump($base64_str);die();
     //   $data = base64_decode($base64_str);
       // $time=$a->idannonce+$i.'-'.time().'.png';
      //  Storage::disk('annonce')->put($time, $data);

      //  $time=$result->idmembre.'-'.time().'.png';
        $time=$a->idannonce+$i.'-'.time().'.png';
        $apicontroller->saveimage('app/public/photo',$time,$req->file('image'.$i));

        $iman->idannonce= $a->idannonce;  
        $iman->urlimage="photo/".$time;  
        $iman->parametre=$i; 
        //array_push($details, $annonce);
        $iman->save();
        $details['image'.$i]=$iman->urlimage;


      //  array_push($details, $iman->urlimage);
      
      }
    //  $url=$time;
      if($req->input('publish_type')=='article' && $req->input('idshowroom')){
      
        $iman= new annoncesboutique;
        $iman->idannonce= $a->idannonce;  
        $iman->idshowroom=$req->input('idshowroom');  
        $iman->visibilite=0; 
       
        $iman->save();
        
       // array_push($details, $iman);
      }
      $details['idshowroom']=$req->input('idshowroom'); 
      Storage::disk('vue')->put($a->referenceannonce.'_biens.txt', 0);

      return response()->json(['succes'=>"Enregistrement de lannonce avec succes","code"=>200,
      'data'=>$details,
      'type'=>$req->input('publish_type'),
      
      ]);            

    }
  }




  
  public function updateannonce(Request $req, ApiController $apicontroller){
    
    $annonce= annonce::where('idannonce',$req->input('idannonce'))->first(); 
    //$ss=souscategorie::where('id_souscat',$req->input('subcategory'))->first(); 
    $annonce->idsouscategorie=$req->input('idsouscategorie');
    $annonce->prix=$req->input('prix');
  
    $annonce->typeannonce=$req->input('typeannonce');
    $annonce->paiementtranche=$req->input('paiementtranche');
    $dept=departement::where('id_dept',$req->input('id_dept'))->first(); 
    $annonce->departement()->associate($dept);
    $annonce->titre=$req->input('titre');

    $annonce->statut='en attente';

    $annonce->localisation=$req->input('localisation');
    $annonce->description=$req->input('description');
    $details=[];
    

    if($req->input('idcategorie')==9){
      if ($req->input('idhabillement')){
        $habillement= habillement::where('id',$req->input('idhabillement'))->first(); 

      }else if(! $req->input('idhabillement')){
        $habillement= new habillement;
       
      }
      $habillement->type=$req->input('type');     
      $habillement->marque=$req->input('marque');  
      $habillement->modele=$req->input('modele');  
      $habillement->couleur=$req->input('couleur');  
      $habillement->taille=$req->input('taille'); 
      $det=$habillement; 
      $annonce->save();
     
      $habillement->annonce()->associate($annonce);
      $habillement->save();
      automobile::where('idannonce',$req->input('idannonce'))->delete(); 
      immobilier::where('idannonce',$req->input('idannonce'))->delete(); 
      array_push($details, $habillement);
    }else if($req->input('idcategorie')==1 ){

          if ($req->input('idimmobilier')){
            $immobilier=  immobilier::where('idimmobilier',$req->input('idimmobilier'))->first(); 

          }else if(! $req->input('idimmobilier')){
            $immobilier= new immobilier;
          

          }
      $immobilier->surface=$req->input('surface');     
     
      $immobilier->typeoperation=$req->input('typeoperation');   
      $immobilier->nombrepiece=$req->input('nombrepiece');  
      $immobilier->datedisponibilite=$req->input('datedisponibilite');  
      $immobilier->droitvisite=$req->input('droitvisite');  
      $immobilier->montantdroit=$req->input('montantdroit');  
      $det=$immobilier;
      $annonce->save();

      $immobilier->idannonce=$req->input('idannonce');
      $immobilier->save();
      automobile::where('idannonce',$req->input('idannonce'))->delete(); 
      habillement::where('idannonce',$req->input('idannonce'))->delete(); 
      $annonce['immobilier']=$immobilier;
    //  array_push($details, $immobilier);
      array_push($details, $annonce);
    } else if($req->input('idcategorie')==3 ){

          if ($req->input('idautomobile')){
            $automobile=  automobile::where( 'idautomobile', $req->input('idautomobile'))->first(); 
          //  $modele= modele::where( 'idmodelevoiture', $req->input('idmodelevoiture'))->first(); 

          } else  if ( !$req->input('idautomobile')) {
            $automobile= new automobile;
          }
          if($req->input('idmodelevoiture')){
            modele::where('idmodelevoiture',$req->input('idmodelevoiture'))->delete(); 
          }

          if($req->input('designation_modelevoiture')){
            $modele= new modele; 
            $modele->designation_modelevoiture=$modele->designation_modelevoitureen=$modele->designation_modelevoitureeng=$req->input('designation_modelevoiture');
            $modele->idmarquevoiture=$req->input('idmarquevoiture');
            $modele->save();

            $a=modele::latest('idmodelevoiture')->first();
           $automobile->idmodelevoiture=$a->idmodelevoiture;
          } 
    

      $automobile->vehicule_type=$req->input('vehicule_type'); 
      $automobile->place=$req->input('place');
     
      $automobile->climatisation=$req->input('climatisation');
      $automobile->typeoperation=$req->input('typeoperation');  
      $automobile->couleur=$req->input('couleur');  
      $automobile->kilometre=$req->input('kilometre');  
      $automobile->puissance=$req->input('puissance');  
      $automobile->boite=$req->input('boite');  
      $automobile->carburant=$req->input('carburant');  
      $automobile->jante=$req->input('jante');  
      $automobile->cylindre=$req->input('cylindre'); 


      $annonce->save();
      //$det=$automobile;
      $automobile->annonce()->associate($annonce);
      $automobile->save();
      immobilier::where('idannonce',$req->input('idannonce'))->delete(); 
      habillement::where('idannonce',$req->input('idannonce'))->delete(); 

      $detail=$automobile;
      
     
      array_push($details, $detail);
    }else{
      $annonce->save();
      array_push($details, $annonce);
    }

    for($i=0;$i<$req->numberOfImages;$i++){
      $iman= new imageannonce;
      //$img=$req->input('image'.$i);
    
     // $base64_str = substr($img, strpos($img, ",")+1);
      //var_dump($base64_str);die();
      //$data = base64_decode($base64_str);
      $time=$req->idannonce+$i.'-'.time().'.png';
      //Storage::disk('annonce')->put($time, $data);
      //$time=$a->idannonce+$i.'-'.time().'.png';
      $apicontroller->saveimage('app/public/photo',$time,$req->file('image'.$i));

      $iman->idannonce= $req->idannonce;  
      $iman->urlimage="photo/".$time;  
      $iman->parametre=$i; 
      //array_push($details, $annonce);
      $iman->save();
     
    //  array_push($details, $iman->urlimage);
    
    }
 
    return response()->json(['succes'=>"Modification de lannonce avec succes","code"=>200,
   
    ]);            

  
}




    public function similarannonce($pays,$name)
    {
    
     // $annonce=[];
     // $annonce=souscategorie::select('id_souscat')->where('nom_souscat',$name)->first();   
      $article=annonce::select('titre','prix','localisation','referenceannonce','idannonce')->where([['idsouscategorie',$name],['statut','acceptee'],['referenceannonce', 'like', $pays.'%']])->orderBy('idannonce','desc')->paginate(30);
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

    public function search_article($pays,$name)
    {
      $list=souscategorie::select('id_souscat')->with('categorie')->whereHas('categorie', function ($query) use($name) {
        $query->where('nom_cat', 'LIKE', '%' . $name . '%');
    })->get();
 
    
     $user = User::select('idmembre')->whereRaw('LOWER(prenom) like ?', '%'.strtolower($name).'%')->orwhereRaw('LOWER(nom) like ?', '%'.strtolower($name).'%')->get();
 //   return $user;
     $annonce=annonce::select('titre','statut','prix','localisation','idannonce','idmembre','referenceannonce','idannonce','description','idsouscategorie')->where('statut','acceptee')->where(function ($query) use($name,$list,$user) {
  
      $query->whereRaw('LOWER(titre) like ?', '%'.strtolower($name).'%');
      
        $query->orwhereRaw( 'LOWER(description) like ?', '%'.strtolower($name).'%');
        $query->orwhereRaw('LOWER(localisation) like ?', '%'.strtolower($name).'%');

        $query->orwhereRaw('LOWER(prix) like ?', '%'.strtolower($name).'%');
        $query->orwhereRaw('LOWER(referenceannonce) like ?', '%'.strtolower($name).'%');
        $query->orwhereRaw('LOWER(typeannonce) like ?', '%'.strtolower($name).'%');
        $query->orwhereRaw('LOWER(paiementtranche) like ?', '%'.strtolower($name).'%');

       
        
        if($list){
          $query->orWhereIn('idsouscategorie', $list);
        }
        if($user){
          $query->orWhereIn('idmembre', $user);
        }
        $query->orwhereHas('departement', function ($query) use ($name) {
          $query->whereRaw('LOWER(lib_dept) like ?', '%'.strtolower($name).'%');
        });

       // $query->orderByRaw("FIELD(titre , '$name' ) ");
      })->where('referenceannonce', 'like', $pays.'%')->orderByRaw("FIELD(titre , '$name' ) ")->orderBy('idannonce','desc')->paginate(30);
      

     foreach($annonce as $articl){
      $membre = imageannonce::where('idannonce',$articl->idannonce)->first();
      if ($membre){
        $articl['image']=$membre->urlimage;
      }
     
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
      $annonce =boutique::where('idshowroom',$id)->select('idmembre','descriptionshowroom','etatshowroom','idshowroom','heuredebut','heurefin','logoshowroom','id_dep','idcategorieshowroom','jourdebut','jourfin','localisation','telephone','nomshowroom','logoshowroom')->first();  
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

    public function showroomsuser($id)
    {
      $annonces =boutique::where([['etatshowroom','!=','suppression'],['idmembre',$id]])->select('idmembre','descriptionshowroom','idshowroom','heuredebut','heurefin','logoshowroom','etatshowroom','id_dep','idcategorieshowroom','jourdebut','jourfin','localisation','telephone','nomshowroom','logoshowroom')->get();  
     
      foreach($annonces as $annonce){
       // $list=[28,29,30];
       $cat= categorie::select('lib_cat','lib_caten')->where('id_cat', $annonce->idcategorieshowroom)->first();
       $dep= departement::select('lib_dept')->where('id_dept', $annonce->id_dep)->first();
       $annonce['departement']=$dep->lib_dept;
       $annonce['categorie']=$cat;
      if(File::exists(storage_path('app/public/compteur/'.$annonce->idshowroom.'_showrooms.txt'))){
        $file=File::get(storage_path('app/public/compteur/'.$annonce->idshowroom.'_showrooms.txt'));
        }else{
          $file=0;
        }
        $annonce['vues']=$file;
        $servicevendu = servicevendu::select('idservice','dateachat','datefinservice')->where('idannonce', $annonce->idshowroom)->where('datefinservice', '>=', date('Y-m-d H:i:s'))->first();
      //  return response()->json($servicevendu->idservice); 
        if($servicevendu){
        $service=service::where('idService',$servicevendu->idservice)->first();
        $annonce['service']=$service;
        $annonce['service']['dateachat']=$servicevendu->dateachat;
        $annonce['service']['datefinservice']=$servicevendu->datefinservice;
      }else{
        $annonce['service']=null;
      }
       
        
      }
      return response()->json($annonces); 
    }

    public function getboutique($pays)
    {
   //   $membre = User::select('idmembre','nom','prenom','codemembre')->where('idmembre',auth('api')->user()->idmembre)->first();
      $boutique = boutique::select('idmembre','descriptionshowroom','idshowroom','etatshowroom','heuredebut','heurefin','logoshowroom','id_dep','idcategorieshowroom','jourdebut','jourfin','localisation','telephone','nomshowroom')->where('etatshowroom','acceptee')->whereHas('user', function ($query) use ($pays) {
        $query->where('codemembre',  'like', $pays.'%');
       })->orderBy('idshowroom','desc')->paginate(30);
      foreach($boutique as $articl){
      //  $membre = User::select('idmembre','nom','prenom','codemembre')->where('idmembre',$articl->idmembre)->first();
        //$articl['user']=$membre;
        $cat= categorie::select('lib_cat','lib_caten')->where('id_cat', $articl->idcategorieshowroom)->first();
        $dep= departement::select('lib_dept')->where('id_dept', $articl->id_dep)->first();
        $articl['departement']=$dep->lib_dept;
        $articl['categorie']=$cat;

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
    public function getnotification($id,$module)
    {
      $notifications = notification::where([['id_receiver',$id],['module',$module]])->orderBy('idnotification','desc')->get(); 
      foreach($notifications as $notification){
        $notification['data']=json_decode($notification->data);
        $notification['timestamp']=json_decode($notification->timestamp);
        $notification['notification']=json_decode($notification->notification);
      }
  //  $article=$article->paginate(15);
      return response()->json($notifications); 
    }

    public function listemarque()
    {
      $marque = marque::all(); 
      $marque_moto = marque_moto::all(); 
  //  $article=$article->paginate(15);
      return response()->json(['marque_auto'=>$marque,'marque_moto'=>$marque_moto]); 
    }

    public function deleteimage($filename,$id)
    {
     // $notification = notification::where('idmembre',auth('api')->user()->idmembre)->orderBy('idnotification','desc')->get(); 
      $image = imageannonce::where('urlimage',$filename.'/'.$id)->delete();
      Storage::disk('annonce')->delete($id);
  //  $article=$article->paginate(15);
      return response()->json(['success'=>"Suppression de l'image avec succés"], 200); 
    }


    public function liste_souscategorie()
    {
      $souscategorie = souscategorie::select('nom_souscat','lib_souscat','id_souscat','lib_souscaten','id_cat')->orderBy('nom_souscat')->get(); 
      return response()->json($souscategorie); 
    }
    public function liste_categorie()
    {
      $notification = categorie::select('nom_cat','lib_caten','lib_cat','id_cat')->orderBy('nom_cat')->get(); 
 
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
   
      return response()->json(['success'=>"Suppression de la notification avec succés"], 200); 
    }
    public function deleteshowroom($id)
    {
      $boutique = boutique::where('idshowroom','=',$id)->first(); ; 
      $boutique->etatshowroom='suppression';
      $boutique->save();
  //  $article=$article->paginate(15);
   
      return response()->json(['success'=>"Suppression de la boutique avec succés"], 200); 
    }

    public function deleteannonce($id)
    {
      $annonce = annonce::where('idannonce','=',$id)->first(); 
      $annonce->statut='suppression';
      $annonce->save();
  //  $article=$article->paginate(15);
   
      return response()->json(['success'=>"Suppression de l' annonce avec succés"], 200); 
    }

    public function boostshowroom($id)
    {
      //$list=[28,29,30];
     // $list=service::select('idService')->where('nomcomplet', 'LIKE', '%' . auth('api')->user()->pays . '%')->where('module','Showroom')->get();
      $list=ApiController::getidservicewithmoduleonly("Showroom");

      $servicevendus = servicevendu::select('datefinservice','dateachat','idservice')->where( 'idannonce','=',$id )->whereIn('idservice',$list)->orderBy('idvente','desc')->get(); ; 
      foreach($servicevendus as $servicevendu){
        $service=service::select('nomService','module')->where('idservice',$servicevendu->idservice)->first();
        $servicevendu['service']=$service;
        
      }
  //  $article=$article->paginate(15);
   
      return response()->json($servicevendus); 
    }
    public function boostarticle($id)
    {
   
      $list=ApiController::getidservicewithmoduleonly("Annonce");

      $servicevendus = servicevendu::select('datefinservice','dateachat','idservice')->where('idannonce','=',$id)->whereIn('idservice',$list)->orderBy('idvente','desc')->get(); 
      foreach($servicevendus as $servicevendu){
        $service=service::select('nomService','module')->where('idservice',$servicevendu->idservice)->first();
        $servicevendu['service']=$service;
        
      }
      return response()->json($servicevendus); 
    }
    
    public function buyboostarticle(Request $req)
    {
      $result=User::where('idmembre','=',$req->idmembre)->first(); 
      if($result->compte < $req->credit){
        return response()->json(['message'=>"Credit insuffisant",'code'=>401], 200); 
      }else {
      $result->compte= $result->compte - $req->credit;
      $result->save();

      $servicevendu = new servicevendu;
      $servicevendu->idannonce= $req->idannonce; 
      $servicevendu->etatvente= 'en attente'; 
      $servicevendu->idservice= $req->idservice; 
      $Date1=date("Y/m/d-H:i");
      $Date2 = date('Y/m/d-H:i', strtotime($Date1 . " + ".$req->days." day"));
      //$Date1=gmdate('Y/m/d-h:i', strtotime($Date1) );
      //$Date2=gmdate('Y/m/d-h:i', strtotime($Date2) );
      $servicevendu->datefinservice= $Date2 ; 
      $servicevendu->dateachat= $Date1; 
      $servicevendu->save();

    
      return response()->json(['success'=>"Enregistré avec succes",'data'=>$servicevendu], 200); 
      }
    }

    public function annoncesboutique(Request $req){
      if($req->type=="add"){
        $iman= new annoncesboutique;
        $iman->idannonce= $req->idannonce;  
        $iman->idshowroom=$req->input('idshowroom');  
        $iman->visibilite=0; 
        $iman->save();
      }else {
        $iman= annoncesboutique::where('idannonceshowroom',$req->idannonceshowroom)->delete();
      }
      return response()->json(['success'=>"Enregistré avec succes",'data'=>$iman], 200); 
    }
    public function getarticleboutique($id)
    {
      $membre = annonce::select('idannonce')->where('statut','acceptee')->get();

      $boutique = annoncesboutique::select('idannonceshowroom','idannonce')->where('idshowroom',$id)->whereIn('idannonce', $membre)->orderBy('idannonceshowroom','desc')->paginate(30);
     
      foreach($boutique as  $article ){
        
        $membre = annonce::select('titre','prix','localisation','idannonce','referenceannonce')->where([['idannonce',$article->idannonce],['statut','acceptee']])->first();
        if($membre){
        $image = imageannonce::where('idannonce',$article->idannonce)->get();
              
        
        $article['image']=$image; 
        $file=0;
       
          $article['articles']=$membre;  
          if(File::exists(storage_path('app/public/compteur/'.$membre->referenceannonce.'_biens.txt') )){
            $file=File::get(storage_path('app/public/compteur/'.$membre->referenceannonce.'_biens.txt'));
            }else if(File::exists(storage_path('app/public/compteur/'.strtolower($membre->referenceannonce).'_biens.txt'))){
              $file=File::get(storage_path('app/public/compteur/'.strtolower($membre->referenceannonce).'_biens.txt'));
              }
              $article['articles']['vues']=$file;
             
        }  
    }
   // $boutique=json_encode($boutique);
  //  $article=$article->paginate(15);
      return response()->json($boutique); 
    }
    public function getuserboutique($id)
    {
      $boutique = boutique::where([['idmembre',$id],['etatshowroom','acceptee']])->orderBy('idshowroom','desc')->paginate(30);
 
  //  $article=$article->paginate(15);
      return response()->json($boutique); 
    }
    public function getusercredit($id)
    {
      $user=User::select( 'compte')->where('idmembre', $id)->first(); 
  //  $article=$article->paginate(15);
      return response()->json($user); 
    }
    public function boutique(Request $req, ApiController $apicontroller){
    
      
     
      if($req->input('idshowroom')){
        $boutique=boutique::where('idshowroom', $req->input('idshowroom') )->first(); 
      }else{
        $boutique= new boutique;
        
        $boutique->idmembre=auth('api')->user()->idmembre;
      }
      
     
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
      $boutique->siteweb='';
      
      $boutique->dateshowroom=date("Y-m-d H:i:s");
      
      if($req->file('logo')){
      //  $img=$req->input('logo');
    //  $base64_str = substr($img, strpos($img, ",")+1);
      //var_dump($base64_str);die();
   //   $data = base64_decode($base64_str);
      $time=$boutique->idmembre.'-'.time().'.png';
      $apicontroller->saveimage('app/public/photo',$time,$req->file('logo'));

      $boutique->logoshowroom="photo/".$time; 
      }
      $boutique->save();
      return response()->json(['success'=>"Enregistrement de la boutique avec succés",'showroom'=>$boutique], 200);            

    }


    public function filter_boutique(Request $req)
    {
      $annonce =boutique::select('idmembre','descriptionshowroom','idshowroom','heuredebut','heurefin','logoshowroom','id_dep','idcategorieshowroom','jourdebut','jourfin','localisation','telephone','nomshowroom','logoshowroom')->whereHas('user', function ($query) use ($req) {
        $query->where('codemembre',  'like', $req->pays.'%');
       })->where([['etatshowroom','acceptee'],['idcategorieshowroom',$req->id_cat]])->orderBy('idshowroom','desc')->get();


      foreach($annonce as $ann){
        $user = User::select('prenom','nom','telephoneportable','email','localisation','idmembre','codemembre')->where('idmembre',$ann->idmembre)->first();
        $ann['proprietaire']=$user;
        $cat= categorie::select('lib_cat','lib_caten')->where('id_cat', $ann->idcategorieshowroom)->first();
        $ann['categorie']=$cat;
       if(File::exists(storage_path('app/public/compteur/'.$ann->idshowroom.'_showrooms.txt'))){
        $file=File::get(storage_path('app/public/compteur/'.$ann->idshowroom.'_showrooms.txt'));
        }else{
          $file=0;
        }
        $ann['vues']=$file;
      }
  
        return response($annonce); 

    }


    public function search_boutique($pays,$name)
    {
      $dept=departement::select('id_dept')->where('lib_dept','LIKE', '%' . $name . '%')->first(); 
     // return $dept;
      $list=User::where('codemembre', 'LIKE', '%' . $name . '%')->select('idmembre')->get();
      $annonce =boutique::select('idmembre','descriptionshowroom','idshowroom','heuredebut','heurefin','logoshowroom','id_dep','idcategorieshowroom','jourdebut','jourfin','localisation','telephone','nomshowroom','logoshowroom')->where('etatshowroom','acceptee')->where(function ($query) use($name,$list,$dept) {
        $query->whereRaw('LOWER(nomshowroom) like ?', '%'.strtolower($name).'%');
        $query->orwhereRaw('LOWER(descriptionshowroom) like ?', '%'.strtolower($name).'%');
        $query->orwhereRaw('LOWER(localisation) like ?', '%'.strtolower($name).'%');
        if($list){
       $query->orWherein( 'idmembre', $list);}
       if($dept){
        $query->orWhere( 'id_dep', $dept->id_dept);}
       
        })->whereHas('user', function ($query) use ($pays) {
          $query->where('codemembre',  'like', $pays.'%');
         })->orderBy('idshowroom','desc')->paginate(30);
  
   //   $sscat =souscategorie::select('id_souscat')->where('nom_souscat','LIKE','%'.$name.'%')->get(); 
     // echo($sscat);
     foreach($annonce as $ann){
      $user = User::select('prenom','nom','telephoneportable','email','localisation','idmembre','codemembre')->where('idmembre',$ann->idmembre)->first();
      $ann['proprietaire']=$user;
      $cat= categorie::select('lib_cat','lib_caten')->where('id_cat', $ann->idcategorieshowroom)->first();
      $ann['categorie']=$cat;
     if(File::exists(storage_path('app/public/compteur/'.$ann->idshowroom.'_showrooms.txt'))){
      $file=File::get(storage_path('app/public/compteur/'.$ann->idshowroom.'_showrooms.txt'));
      }else{
        $file=0;
      }
      $ann['vues']=$file;
    }

      return response($annonce); 
    }
    
    public function ajout_credit(Request $req)
    {
      $user =User::find($req->idmembre);
      $user->compte =$user->compte+$req->credit;  
      $user->save(); 
      $transaction= new transaction;
      $transaction->id_membre=$req->idmembre;
      $transaction->type="achat";
      $transaction->date=date("Y-m-d H:i:s");
      $transaction->description=$req->credit.",".$user->compte;
      $transaction->save();
     
      return response()->json(['success'=>'Enregistré'], 200); 
    }
    public function offerarticle(Request $req)
    {
     if($req->idproposition){
      $prix= propositionprix::where('idproposition',$req->idproposition)->first();
     }else{
      $prix= new propositionprix;
      $prix->idmembre=auth('api')->user()->idmembre;
      $prix->dateproposition=date("Y-m-d H:i:s");
     }
      
      $prix->quantity=$req->quantity;
      $prix->idannonce=$req->idannonce;
      $prix->urlimageoffre=$req->urlimageoffre;
      $prix->description=$req->description;
    
      $prix->prixproposition=$req->prix;
      $prix->save();
     
      return response()->json(['success'=>'Enregistré'], 200); 
    }
    public function listoffer($id)
    {
     
      $prix=  propositionprix::where([['idannonce',$id],['statut','=',null]])->orderBy('idproposition','desc')->paginate(30);
      foreach($prix as $articl){
        $user = User::select( 'departement_id','localisation','profil','idmembre','prenom','nom','telephoneportable','email','codemembre')->where(
          'idmembre', $articl->idmembre)->first();
       #   return $user->departement_id;
          $dep=departement::where('id_dept',$user['departement_id'])->first(); 
          $articl['departement']=$dep['lib_dept'];
          $articl['vendeur']=$user;
      }
     
     
      return response()->json(['offre'=>$prix], 200); 
    }
// Rejeter une offre
    public function statutoffer(Request $req)
    {
      $prix=  propositionprix::where('idproposition',$req->idproposition)->first();
      if($req->statut){
        $prix->statut=$req->statut;
      } else if ($req->feedback){
        $prix->feedback=$req->feedback;
      }
      $prix->save();
      return response()->json(['result'=>'success'], 200); 

    }

    public function deleteoffer($id)
    {
      $prix=  propositionprix::where('idproposition',$id)->delete();
      return response()->json(['result'=>'success'], 200); 

    }
    public function myoffers($id)
    {
     
      $prix=  propositionprix::where('idmembre',$id)->orderBy('idproposition','desc')->paginate(30);
      foreach($prix as $articl){
 
          $annonce =annonce::where([['statut','acceptee'],['idannonce',$articl->idannonce]])->select('titre','prix','localisation','idannonce','referenceannonce','idmembre','idsouscategorie','description','nomvendeur','paiementtranche','typeannonce','dateannonce','validite')->first();   
          $boutique = annoncesboutique::select('idshowroom')->where('idannonce',$annonce->idannonce )->first();

          if($boutique){
           $annonce['idshowroom']=$boutique->idshowroom;
          }
          $user = User::select('prenom','nom','telephoneportable','email','localisation','idmembre','codemembre')->where('idmembre',$annonce->idmembre)->first();

          $image = imageannonce::where('idannonce',$annonce->idannonce)->first();
           
          $articl['annonce']=$annonce;
          $articl['vendeur']=$user;
          $articl['annonce']['image']=$image->urlimage;
      }
     
     
      return response()->json($prix); 
    }

    public function add_notification(Request $req)
    {
      $notification= new notification;
      $notification->id_sender=$req->id_sender;
      $notification->id_receiver=$req->id_receiver;
      $notification->data=$req->data;
      $notification->timestamp=$req->timestamp;
      $notification->notification=$req->notification;
      $notification->module=$req->module;
      $notification->save();
      return response()->json(['success'=>'Enregistré'
                              ], 200); 
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
      return response()->json(['success'=>'Enregistré'], 200); 
    }

    public function ajout_panier($id)
    {
      $panier = panier::where([["idmembre", auth('api')->user()->idmembre],["idannonce",$id],["statut",'!=',"commandé"]])->first(); 
      if ($panier) {
          return response()->json([
              "status"=>403,
              "message"=> "Article déja dans le panier"
        ]);
      }
      
      $panier= new panier;
      $panier->idmembre=auth('api')->user()->idmembre;
      $panier->idannonce=$id;
      $panier->statut='';
      $panier->date=date("Y-m-d H:i:s");
      $panier->save();
   
     
      return response()->json(['success'=>"Ajout panier avec succés"], 200); 
    }
    public function getboutiqueservice($pays)
    {
     // $list=service::select('idService')->where('module','Showroom')->get();

     // $list=ApiController::getidservice("Showroom");
      
      $list=ApiController::getidservicewithmoduleonly("Showroom");

      $boutique = boutique::select('idshowroom')->where('etatshowroom','acceptee')->whereHas('user', function ($query) use ($pays) {
        $query->where('codemembre',  'like', $pays.'%');
       })->get();

      //return $list;

      $servicevendu = servicevendu::select('idannonce','idservice','dateachat','datefinservice')->whereIn('idservice', $list)->whereIn('idannonce', $boutique)->where('datefinservice','>',date("Y/m/d-H:i"))->orderBy('idvente','desc')->paginate(30);
      foreach($servicevendu as $articl){
        $annonce = boutique::select('idmembre','descriptionshowroom','idshowroom','heuredebut','heurefin','logoshowroom','id_dep','idcategorieshowroom','jourdebut','jourfin','localisation','telephone','nomshowroom')->where([['idshowroom',$articl->idannonce],['etatshowroom','acceptee']])->first();
        $cat= categorie::select('lib_cat','lib_caten')->where('id_cat', $annonce->idcategorieshowroom)->first();
        $dep=departement::where('id_dept',$annonce->id_dep)->first(); 
         
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
        $articl['categorie']=$cat;
        $articl['departement']=$dep->lib_dept;
        $articl['service']=$service->nomService;
        $articl['vues']=$file;
        
    }
     
      return response()->json($servicevendu); 
    }
    public function delete_panier($id)
    {
       
      $result=panier::where('idpanier','=',$id)->delete(); 
     
      return response()->json(['success'=>"Suppression de l'article dans le panier avec succés"], 200); 
    }
    public function liste_panier($id)
    {
      $panier =panier::select('idannonce','idpanier','quantite')->where([['idannonce','!=',null],['idmembre','=',$id],['statut','!=','commandé']])->get();
    
      foreach($panier as $articl){
        
        $membre = annonce::select('localisation','idannonce','bloquer_commande','idsouscategorie','prix','referenceannonce','titre','validite','idmembre')->where('idannonce',$articl->idannonce)->first();
        $articl['annonce']=$membre;

        $boutique = annoncesboutique::select('idshowroom')->where('idannonce',$membre->idannonce )->first();

        if($boutique){
          $articl['idshowroom']=$boutique->idshowroom;
        }

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
     
      $array=[];
      foreach($req->panier as $reqpanier){
       
        $panier =panier::with('annonce')->where('idpanier','=',$reqpanier['idpanier'])->first();
      //return $panier;
      $commande= new commande;
      //$commande->idpanier=$reqpanier['idpanier'];
      $commande->panier()->associate($panier);
      $commande->quantite=$reqpanier['quantite'];
      $commande->datecommande=date("Y/m/d");
      $commande->statut="AWAITING";
      $panier->statut="commandé";
      $panier->save();
     
      $commande->reference=$panier->annonce->referenceannonce."c".$reqpanier['idpanier'].date("dmY");
      $commande->save();

      if($req['livraison']==true){
        $livraisoncommande= new livraisoncommande;
        $livraisoncommande->adresse=$req['adresse'];
        $livraisoncommande->iddestinataire=$req['iddestinataire'];
      //  $livraisoncommande->id_tariflivraison=$reqpanier['id_tariflivraison'];
        $livraisoncommande->besoins=$req['besoins'];
        $livraisoncommande->datelivraisoncommande=date("Y-m-d H:i:s");
        $livraisoncommande->idcommande=$commande->idcommande;
        $livraisoncommande->save();
      }
      
 //     $number = commande::latest('idcommande')->first(); 
       array_push($array, $commande->idcommande);
  
      }
   
      return response()->json(['idcommande'=>$array], 200);            
    }
    public function modifiercommande(Request $req)
    {
     // $commande= new commande;
      $result=commande::where('idcommande','=',$req->input('idcommande'))->first(); 
 
        $result->quantite=$req->input('quantite');
        $result->adresse=$req->input('adresse');
       
        $result->save();
      return response()->json($result);            
    }

    public function bloquer_commande($idannonce,$statut)
    {
     // $commande= new commande;
      $result=annonce::where('idannonce','=',$idannonce)->first(); 
 
        $result->bloquer_commande=$statut;
      
      $result->save();
      return response()->json(['success'=>"Ok"], 200);            
    }

    public function supprimercommande($id)
    {
       
      $result=commande::where('idcommande','=',$id)->delete(); 
     
      return response()->json(['success'=>"Suppression de la commande avec succés"], 200); 
    }

    public function getarticleservice($pays)
    {
     
     // $list=service::select('idService')->where('module','Annonce')->get();

    //  $list=ApiController::getidservice("Annonce");
      $list=ApiController::getidservicewithmoduleonly("Annonce");


      $annonce = annonce::select('idannonce')->where('referenceannonce', 'like', $pays.'%')->where('statut','acceptee')->get();

      $servicevendu = servicevendu::select('idannonce','idservice','dateachat','datefinservice')->whereIn('idservice', $list)->whereIn('idannonce', $annonce)->where('datefinservice','>',date("Y/m/d-H:i"))->orderBy('idvente','desc')->paginate(30);
      foreach($servicevendu as $articl){
        $annonce = annonce::select('titre','prix','localisation','idmembre','idannonce','referenceannonce')->where([['idannonce',$articl->idannonce],['statut','acceptee']])->first();
        if($annonce){
         
        
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
     
    }
  //  $article=$article->paginate(15);
      return response()->json($servicevendu); 
    }
    public function listeservice()
    {
      $service=service::where([['nomcomplet', 'LIKE', '%' . auth('api')->user()->pays . '%'],['module','Showroom']])->orWhere([['nomcomplet', 'LIKE', '%' . auth('api')->user()->pays . '%'],['module','Annonce']])->get();
    
      return response()->json($service); 
    }

 
    public function payepourmoi(Request $req)
    {
      foreach($req->panier as $reqpanier){
       
      //  $panier =panier::with('annonce')->where('idpanier','=',$reqpanier['idpanier'])->first();
      $result=panier::where('idpanier','=',$reqpanier['idpanier'])->first(); 
      $result->quantite= $reqpanier['quantite'] ;
      $result->save();
      }
  //  $article=$article->paginate(15);
      return response()->json(['success'=>"Enregisté avec succés"], 200); 
    }
    public function listecommande($id)
    {
      $allannonce = annonce::select('idannonce')->where('statut','acceptee')->get();

      $service = commande::select('idcommande','idpanier','motif','feedback','statut','datereceptioncommande','adresse','datecommande','reference','quantite','statut')->whereHas('panier', function ($query) use($id,$allannonce) {
        $query->where('idmembre', $id);
        $query->whereIn('idannonce', $allannonce);
        $query->where('statut', 'commandé');
    })->orderBy('idcommande','desc')->get();
    foreach($service as $articl){
      $membre = annonce::select('localisation','idmembre','idsouscategorie','prix','referenceannonce','titre','idannonce')->where([['idannonce',$articl->panier->idannonce],['statut','acceptee']])->first();
     // return $membre;
     $boutique = annoncesboutique::select('idshowroom')->where('idannonce',$membre['idannonce'] )->first();

     if($boutique){
      $membre['idshowroom']=$boutique->idshowroom;
     }

     $user = User::select('prenom','nom','telephoneportable','email','localisation','idmembre','codemembre')->where('idmembre',$membre['idmembre'])->first();

      $image = imageannonce::select('urlimage','idannonce')->where('idannonce',$membre['idannonce'])->first();
      
      $articl['panier']['annonce']=$membre;
      $articl['panier']['vendeur']=$user;
      $articl['panier']['image']=$image['urlimage'];
      unset($articl['panier']['idpanier']);unset($articl['panier']['idmembre']);
      unset($articl['panier']['idannonce']);unset($articl['panier']['statut']);
      unset($articl['panier']['date']);unset($articl['idpanier']);unset($articl['panier']['quantite']);
  }
  //  $article=$article->paginate(15);
      return response()->json($service); 
    }

    public function listevente($id)
    {
  
    $service = annonce::select('idannonce')->where([['idmembre',$id],['statut','acceptee']])->get();
   // $idannonce=$articl->idannonce;
    $services = commande::select('idcommande','idpanier','motif','feedback','statut','adresse','datereceptioncommande','datecommande','reference','quantite','statut')->whereHas('panier', function ($query) use ($service) {
      $query->whereIn('idannonce', $service);
      $query->where('statut', 'commandé');
     })->get();
    foreach($services as $articl){
     
        $membre = annonce::select('localisation','idmembre','idsouscategorie','prix','referenceannonce','titre','idannonce')->where([['idannonce',$articl->panier->idannonce],['statut','acceptee']])->first();
        $boutique = annoncesboutique::select('idshowroom')->where('idannonce',$membre->idannonce )->first();

        if($boutique){
         $membre['idshowroom']=$boutique->idshowroom;
        }
        $user = User::select('prenom','nom','telephoneportable','email','localisation','idmembre','codemembre')->where('idmembre',$articl->panier->idmembre)->first();

        $image = imageannonce::select('urlimage','idannonce')->where('idannonce',$membre->idannonce)->first();
        $articl['annonce']=$membre;
        $articl['annonce']['client']=$user;
        $articl['annonce']['image']=$image['urlimage'];
        unset($articl['panier']);
  
    }
    $proposition = propositionprix::whereIn('idannonce', $service)->where('statut', 'VALIDATED')->get();
    foreach($proposition as $articl){
     
      $membre = annonce::select('localisation','idmembre','idsouscategorie','prix','referenceannonce','titre','idannonce')->where([['idannonce',$articl->idannonce],['statut','acceptee']])->first();
      $boutique = annoncesboutique::select('idshowroom')->where('idannonce',$membre->idannonce )->first();

      if($boutique){
       $membre['idshowroom']=$boutique->idshowroom;
      }
      $user = User::select('prenom','nom','telephoneportable','email','localisation','idmembre','codemembre')->where('idmembre',$articl->idmembre)->first();

      $image = imageannonce::select('urlimage','idannonce')->where('idannonce',$membre->idannonce)->first();
      $articl['annonce']=$membre;
      $articl['annonce']['client']=$user;
      $articl['annonce']['image']=$image['urlimage'];
      unset($articl['panier']);

  }
   // $data =[];
  //  $data = array_merge($services,$proposition);
    //$data['commande']=$services;
    //$data['proposition_prix']=$proposition;

    $dados1 = json_encode($services); 
    $dados2 = json_encode($proposition); 
    $data = array_merge(json_decode($dados1, true),json_decode($dados2, true));

      return response()->json( $data); 
    }

    public function statutcompte($id)
    {
     // $commande= new commande;

      $details=[
             
        'code'=> ''
    ];
    $idmembre=auth('api')->user()->idmembre;
    $user=User::where('idmembre','=',$idmembre)->first(); 
    if($id==0){
   
    $user->etatcompte=0;
    $user->email='';
    $user->telephoneportable='';
    $user->DateDesactivation=date("Y/m/d-h:i");
    $user->save();
      
    $membre = annonce::where('idmembre',$idmembre)->first();
    $membre->statut="suppression";
    $membre->save();

    $membre = boutique::where('idmembre',$idmembre)->first();
    $membre->etatshowroom="suppression";
    $membre->save();

    $membre = restauration::where('idmembre',$idmembre)->first();
    $membre->statut="suppression";
    $membre->save();

    $membre = plat::where('idmembre',$idmembre)->first();
    $membre->statut="suppression";
    $membre->save();

    $membre = chambre::where('idmembre',$idmembre)->first();
    $membre->statut="suppression";
    $membre->save();

    $membre = hebergement::where('idmembre',$idmembre)->first();
    $membre->statut="suppression";
    $membre->save();

    $membre = commande::where('idmembre',$idmembre)->whereHas('panier', function ($query) use ($idmembre) {
      $query->where('idmembre', $idmembre);
    })->first();
    $membre->statut="DELETED";
    $membre->save();

    $membre = commanderestauration::where('idmembre',$idmembre)->first();
    $membre->statut="DELETED";
    $membre->save();

    $membre = reservationtable::where('idmembre',$idmembre)->first();
    $membre->statut="DELETED";
    $membre->save();

    // Delete User
    //$user->delete();
    $details['body']="Votre compte Iveez ".auth('api')->user()->codemembre." vient d'etre supprimer. Merci.";

    Auth::logout();
    
    OauthAccessToken::where("user_id", auth('api')->user()->idmembre)->delete(); 

    }else  if($id==1){
      $details['body']="Vous compte a été réactivé avec succes";
      $user->DateDesactivation=null;
      $user->etatcompte=1;
      $user->save();
    }
   
     
    #return $details;
    Mail::to($user->email)->send(new StatutUser($details));

      return response()->json(['success'=>"Statut de l'utilisateur mise à jour"], 200);   
    }
    public function commandestatut(Request $req)
    {

      $commande=commande::where('idcommande',$req->idcommande)->first(); 
      if($req->statut=='FEEDBACK'){
        $commande->feedback=$req->feedback;
        $commande->save();
      }else if($req->statut=='CANCELLED' || $req->statut=='REJECTED'  ){
        $commande->statut=$req->statut;
        $commande->motif=$req->motif;
        $commande->save();
      } else if($req->statut=='DELIVERED'){
        $commande->statut=$req->statut;
        $commande->adresse=$req->adresse;
       
        $commande->datereceptioncommande=date("Y/m/d-h:i");

        
        $commande->save();
      }  else if($req->statut=='VALIDATED'){
        $commande->statut=$req->statut;
        $commande->save();
      }  
     
      
      return response()->json($commande);   
    }
    
    public function imageprofil(Request $req, ApiController $apicontroller)
    {
     // $commande= new commande;
      $result=User::where('idmembre','=',auth('api')->user()->idmembre)->first(); 
      
      $time=$result->idmembre.'-'.time().'.png';
      $apicontroller->saveimage('app/public/profil',$time,$req->file('image'));
      $result->profil="profil/".$time ;
      $result->save();
      return response()->json(['success'=>"Image de l'utilisateur mise à jour",'image'=>$result->profil], 200);            
    }
    
    public function filter_article(Request $req)
    {
   //  $annonce=annonce::where([['titre','LIKE','%'.$req->input('titre').'%'],['referenceannonce','LIKE','%'.$req->input('reference').'%'],['titre','LIKE','%'.$req->input('titre').'%'],['statut','acceptee']])->get();  
  if($req->input('categorie')){
    $name=$req->input('categorie');
    $souscategorie=souscategorie::select('id_souscat')->where('id_cat',$name)->get();
  }
  else{
    $souscategorie='';
  }
 
//return  $souscategorie;
//var_dump($results);die();
    $annonce= annonce::with('departement')->where('referenceannonce', 'like', $req->input('pays').'%')->where(function ($query) use($req,$souscategorie) {
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
  
    if($souscategorie!=''){
      $query->whereIn('idsouscategorie', $souscategorie);
    }
    
    $query->whereHas('departement', function ($query) use ($req) {
      $query->where('lib_dept', 'LIKE', '%' .$req->input('departement'). '%');
    });
    
  })
  ->select('titre','prix','localisation','idmembre','idannonce','referenceannonce','statut')->where('statut','acceptee')->orderBy('idannonce','desc')->paginate(30);
  
  foreach($annonce as $articl){
    unset($articl['departement']);
    unset($articl['idmembre']);
    $membre = imageannonce::where('idannonce',$articl->idannonce)->first();
    if(File::exists(storage_path('app/public/compteur/'.$articl->referenceannonce.'_biens.txt'))){
    $file=File::get(storage_path('app/public/compteur/'.$articl->referenceannonce.'_biens.txt'));
    }else if(File::exists(storage_path('app/public/compteur/'.strtolower($articl->referenceannonce).'_biens.txt'))){
      $file=File::get(storage_path('app/public/compteur/'.strtolower($articl->referenceannonce).'_biens.txt'));
      }else {
      $file=0;
    }
    if ($membre){
      $articl['image']=$membre->urlimage;
    }
   
    $articl['vues']=$file;
 //   $articl['url']="api.iveez.com/api/image/{imagename}";   
}

    return response($annonce); 
  }


  //////////////////GESTIONNAIRE

  public function addgestionnaire(Request $req)
{
  $gestionnaire= new gestionnaire;
  $gestionnaire->idmembre=$req->input('idgestionnaire');
  $gestionnaire->date=date("Y-m-d H:i:s");
  $gestionnaire->idshowroom= $req->input('idshowroom');
  $gestionnaire->save();
  return response(['success'=>"ajouter avec succés"], 200); 
}

public function listegestionnaire($id)
{
  $gestionnaire= gestionnaire::where([['idmembre',$id],['idshowroom','!=',null]])->get(); 
   foreach($gestionnaire as $test){
     
  //  $membre = boutique::select('localisation','idmembre','idsouscategorie','prix','referenceannonce','titre','idannonce')->where([['idannonce',$articl->panier->idannonce],['statut','acceptee']])->first();
    $annonce =boutique::where([['etatshowroom','acceptee'],['idshowroom',$test->idshowroom]])->select('idmembre','descriptionshowroom','idshowroom','heuredebut','heurefin','logoshowroom','id_dep','idcategorieshowroom','jourdebut','jourfin','localisation','telephone','nomshowroom','logoshowroom')->first();  
    $user=User::select('prenom','nom','num_whatsapp','codemembre','localisation','profil','email','telephoneportable')->where('idmembre',$annonce->idmembre)->first();

    $cat= categorie::select('lib_cat','lib_caten')->where('id_cat', $annonce->idcategorieshowroom)->first();
    $dep=departement::where('id_dept',$annonce->id_dep)->first(); 
    if(File::exists(storage_path('app/public/compteur/'.$annonce->idshowroom.'_showrooms.txt'))){
      $file=File::get(storage_path('app/public/compteur/'.$annonce->idshowroom.'_showrooms.txt'));
      }else{
        $file=0;
      }
      $test['vues']=$file;
    $test['categorie']=$cat;
    $test['departement']=$dep->lib_dept;
    $test['showroom']=$annonce;
    $test['showroom']['proprietaire']=$user;
    
  }
 
  return response($gestionnaire); 
}

public function deletegestionnaire($id)
{
  $gestionnaire= gestionnaire::where('id_gestionnaire',$id)->delete(); 

 
  return response()->json(['success'=>"supprime avec succés"], 200); 
}

public function gestionnaireconnected($id,$value)
{
  $gestionnaire= gestionnaire::where('id_gestionnaire',$id)->first(); 
  $gestionnaire->is_connected= $value;
  $gestionnaire->save();
 
  return response()->json(['success'=>"Enregistré avec succés"], 200); 
}

public function gestionnaireshowroom($id)
{
  $gestionnaire= gestionnaire::select('idmembre','id_gestionnaire','is_connected')->where('idshowroom',$id)->get(); 
 $gest=[];
  foreach($gestionnaire as $test){
  $user=User::select('prenom','nom','num_whatsapp','codemembre','departement_id','localisation','profil','email','telephoneportable')->where('idmembre',$test->idmembre)->first();
  $dept=departement::where('id_dept',$user->departement_id)->first(); 
  $user['id_gestionnaire']=$test->id_gestionnaire;
  $user['idmembre']=$test->idmembre;
  $user['is_connected']=$test->is_connected;
  $user['departement']=$dept->lib_dept;
  unset($user['departement_id']);
  array_push($gest, $user);
  }

  return response()->json($gest); 
}


public function onecommande($id)
{
  $service = commande::select('idcommande','datereceptioncommande','idpanier','datecommande','reference','quantite','statut')->where('idcommande',$id)->whereHas('panier', function ($query) use($id) {
 
    $query->where('statut', 'commandé');
})->first();

$membre = annonce::select('localisation','idmembre','idsouscategorie','prix','referenceannonce','titre','idannonce')->where([['idannonce',$service->panier->idannonce],['statut','acceptee']])->first();
$user = User::select('prenom','nom','telephoneportable','email','localisation','idmembre','codemembre')->where('idmembre',$service->panier->idmembre)->first();
$service['acheteur']=$user;
unset($service['panier']);
$image = imageannonce::select('urlimage','idannonce')->where('idannonce',$membre->idannonce)->first();
$user = User::select('prenom','nom','telephoneportable','email','localisation','idmembre','codemembre')->where('idmembre',$membre->idmembre)->first();

$service['vendeur']=$user;
$service['annonce']=$membre;
$service['image']=$image;
$livraison = livraisoncommande::where('idcommande',$service->idcommande)->first();

$service['livraison']=$livraison;

return response()->json($service); 

}



/////////////////////FAVORISS////////////////////
public function deletefavoris($id)
{
  $favoris= favoris::where('idfavoris',$id)->delete(); 

 
  return response()->json(['success'=>"supprime avec succés"], 200); 
}
public function listefavoris($id)
{
  $favoris= favoris::where('id_membre',$id)->get(); 
  $annonces=[];
  $showrooms=[];
   foreach($favoris as $test){
    $annonce = annonce::select('localisation','idmembre','idsouscategorie','prix','referenceannonce','id_dep','titre','idannonce','bloquer_commande')->where([['idannonce',$test->id_annonce],['statut','acceptee']])->first();
   
    $boutique =boutique::where('idshowroom',$test->id_showroom)->select('idmembre','idshowroom','heuredebut','heurefin','descriptionshowroom','logoshowroom','id_dep','idcategorieshowroom','jourdebut','jourfin','localisation','telephone','nomshowroom','logoshowroom')->first();  
 
    if($annonce){
    $imageannonce = imageannonce::where('idannonce',$annonce->idannonce)->first();
    $annonce['idfavoris']=$test->idfavoris;
    $annonce['image']=$imageannonce->urlimage;
    $dep= departement::select('lib_dept')->where('id_dept', $annonce->id_dep)->first();
    $annonce['departement']=$dep->lib_dept;
    array_push($annonces, $annonce);
   }else if($boutique) {
     if($boutique){
      $user = User::select('prenom','nom','telephoneportable','email','localisation','idmembre','codemembre')->where('idmembre',$boutique->idmembre)->first();
      if(File::exists(storage_path('app/public/compteur/'.$boutique->idshowroom.'_showrooms.txt'))){
        $file=File::get(storage_path('app/public/compteur/'.$boutique->idshowroom.'_showrooms.txt'));
        }else if(File::exists(storage_path('app/public/compteur/'.strtolower($boutique->idshowroom).'_showrooms.txt'))){
          $file=File::get(storage_path('app/public/compteur/'.strtolower($boutique->idshowroom).'_biens_showrooms.txt'));
          }else {
          $file=0;
        }
     }else{
       $user=null;
     }
    $dep= departement::select('lib_dept')->where('id_dept', $boutique->id_dep)->first();
    $boutique['departement']=$dep->lib_dept;
    $boutique['idfavoris']=$test->idfavoris;
    $boutique['vues']=$file;
    $boutique['proprietaire']=$user;
    array_push($showrooms, $boutique);
   }
   
  }
  return response()->json(['annonce'=>$annonces,'showroom'=>$showrooms], 200);

}

public function addfavoris(Request $req)
{
  $favoris= new favoris;
  $favoris->id_membre=$req->id_membre;
  $favoris->id_annonce=$req->id_annonce;
  $favoris->id_showroom=$req->id_showroom;
  $favoris->save();
  return response(['success'=>"ajouter avec succés"], 200); 
}
//////////////////////////////////////////
}




