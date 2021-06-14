<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\plat;
use App\User;
use App\service;
use App\departement;
use App\servicevendu;
use App\panier;
use App\restauration;
use App\imagerestauration;
use App\favoris;
use App\reservationtable;
use App\commandereservationtable;

use File;

use Illuminate\Support\Facades\Storage;
use App\commande_plat;

class RestaurantController extends Controller
{
    

    public function plat(Request $req){
    
        if($req->input('idmenu')){
          $annonce= plat::where('idmenu',$req->input('idmenu'))->first(); 


        }else{
        $annonce= new plat;
        }
        
        $annonce->categorie_plat=$req->input('categorie_plat');

        $annonce->statut="en attente";
        $annonce->accompagnements=$req->input('accompagnements');

        $annonce->prix=$req->input('prix');
        $annonce->dureepreparation=$req->input('dureepreparation');
        $annonce->lundi=$req->input('lundi');
        $annonce->mardi=$req->input('mardi');
        $annonce->mercredi=$req->input('mercredi');
        $annonce->jeudi=$req->input('jeudi');
        $annonce->vendredi=$req->input('vendredi');
        $annonce->samedi=$req->input('samedi');
        $annonce->dimanche=$req->input('dimanche');
        $annonce->plat=$req->input('designation');
        $annonce->description=$req->input('description');
        $article = restauration::select('idrestauration')->where('idrestauration',$req->input('idrestauration'))->first();
        #var_dump($article);die();
        $annonce->restauration()->associate($article);
        $a=plat::latest('idmenu')->first();
        $num=$a->idmenu+1;
        if($req->input('photo')){
         
          $img=$req->input('photo');
        
          $base64_str = substr($img, strpos($img, ",")+1);
          //var_dump($base64_str);die();
          
          $data = base64_decode($base64_str);
          $time=$num.'-'.time().'.png';
          Storage::disk('menu')->put($time, $data);
          $annonce->photo="menu/".$time;
        }
       
        $annonce->save();
        unset($annonce['restauration']);
     

        $annonce['code']=200;
        Storage::disk('vue')->put($num.'_menu.txt', 0);

        return response()->json($annonce);            

}

public function reservationtable(Request $req)
{
  $a=reservationtable::latest('idreservationtable')->first();
  $idreservationtable=$a->idreservationtable +1 ;
  $panier= new reservationtable;
  $panier->idmembre=$req->input('idmembre');
  $panier->idrestauration=$req->input('idrestauration');
  $panier->titre=$req->input('titre');
  $panier->invite=$req->input('listeinvites');
  $panier->nombrepersonne=$req->input('nombreplaces');
  $panier->heurearrivee=$req->input('heurereservation');
  $panier->besoins=$req->input('besoins');
  $panier->datereservation=$req->input('datereservation');
  $panier->referencereservationtable=auth('api')->user()->codemembre."r".$idreservationtable.date("dmY");
  $panier->statut='AWAITING';

  $panier->save();

 
  return response()->json(['idrestauration'=> $req->input('idrestauration') ,'idreservationtable'=>$panier->idreservationtable,'statut'=> $panier->statut], 200); 
}

public function listereservationtable($id){

  $reservationtable=reservationtable::where([['statut','!=','REJECTED'],['idmembre',$id]])->orwhere([['invite', 'LIKE','%'.$id.'%' ],['statut','!=','REJECTED']])->orderBy('idreservationtable','desc')->get();
  foreach($reservationtable as $articl){
    $membre = imagerestauration::where('idrestauration',$articl->idrestauration)->first();
    $articl['photo']=$membre->urlimagerestauration;
    $invite = explode(', ', $articl['invite']);
    $user = User::select('idmembre','prenom','nom','profil','codemembre')->whereIn('idmembre',$invite)->get();
    $articl['listeinvites']=$user;
    $commande = commandereservationtable::select( 'idmenu', 'idmembre', 'quantite')->where('idreservationtable',$articl['idreservationtable'])->get();
    foreach($commande as $command){
      $article = plat::select('photo', 'prix','plat')->where('idmenu',$command['idmenu'])->first();
      $command['photo']=$article['photo'];
      $command['prix']=$article['prix'];
      $command['plat']=$article['plat'];
    }
    $articl['listecommandes']=$commande;

  }
  return response($reservationtable); 

}
public function declineinvitation($idreservation,$idmembre){
  $reservationtable=reservationtable::where('idreservationtable',$idreservation)->first();
  $invite = explode(', ', $reservationtable['invite']);
  if (($key = array_search($idmembre, $invite)) !== false) {
    unset($invite[$key]);
  }
  $invite = implode(', ', $invite);
  $reservationtable->invite=$invite;
  $reservationtable->save();
return response()->json(['message'=>'success'], 200);

}

public function restauration(Request $req){
    
  if($req->input('idrestauration')){
    $annonce= restauration::where('idrestauration',$req->input('idrestauration'))->first(); 


  }else{
  $annonce= new restauration;
  }
  
  $annonce->adresse=$req->input('adresse');

  $annonce->statut="en attente";
  $annonce->capacite=$req->input('capacite');

  $annonce->designation=$req->input('designation');
  $annonce->description=$req->input('description');
  $annonce->fermeture=$req->input('fermeture');
  $annonce->id_dep=$req->input('id_dep');
  $annonce->ouverture=$req->input('ouverture');
  $annonce->typecuisine=$req->input('typecuisine');
  $annonce->typerestauration=$req->input('typerestauration');
  $annonce->idmembre=$req->input('idmembre');

  #var_dump($article);die();
  $annonce->save();

  $a=restauration::latest('idrestauration')->first();

  $num=$a->idrestauration;
  for($i=0;$i<$req->numberOfImages;$i++){
   if($req->input('photorestauration'.$i)){
  

    $img=$req->input('photorestauration'.$i);
    $iman= new imagerestauration;
    $base64_str = substr($img, strpos($img, ",")+1);
    //var_dump($base64_str);die();
    $data = base64_decode($base64_str);
    $time=$num+$i.'-'.time().'.png';
    Storage::disk('photorestauration')->put($time, $data);
    $iman->idrestauration= $annonce->idrestauration;  
    $iman->urlimagerestauration="photorestauration/".$time;  
    $iman->parametreimagerestauration=$i; 
    //array_push($details, $annonce);
    $iman->save();
    $annonce['photorestauration'.$i]=$iman->urlimagerestauration;
   }

  }
  $dept=departement::where('id_dept',$req->input('id_dep'))->first(); 
  $user = User::select('idmembre','codemembre')->where('idmembre',$req->input('idmembre'))->first();

  Storage::disk('vue')->put($num.'_restauration.txt', 0);
  $annonce['typecuisine']=json_decode($annonce['typecuisine']);

  $annonce['departement']=$dept->lib_dept;
  $annonce['codemembre']=$user->codemembre;

  $annonce['code']=200;
  return response()->json($annonce);            

}

public function ajout_panier($id)
{
  $panier = panier::where([["idmembre", auth('api')->user()->idmembre],["idmenu",$id],["statut",'!=',"commandé"]])->first(); 
  if ($panier) {
      return response()->json([
          "status"=>403,
          "message"=> "Plat déja dans le panier"
    ]);
  }
  $panier= new panier;
  $panier->idmembre=auth('api')->user()->idmembre;
  $panier->idmenu=$id;
  $panier->statut='';
  $panier->date=date("Y-m-d H:i:s");
  $panier->save();

 
  return response()->json(['success'=>"Ajout panier avec succés",'idpanier'=>$panier->idpanier], 200); 
}

public function delete_panier($id)
{
   
  $result=panier::where('idpanier','=',$id)->delete(); 
 
  return response()->json(['success'=>"Supprimer avec succés"], 200); 
}
  public function liste_panier($id)
  {
  $panier =panier::select('idmenu','idpanier','quantite')->where([['idmenu','!=',null],['idmembre','=',$id],['statut','!=','commandé']])->get();

  foreach($panier as $articl){
    
    $article = plat::select('photo','idmenu', 'prix','idrestauration','lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche','bloquer_commande','plat')->where('idmenu',$articl->idmenu)->first();
  //  $membre = annonce::select('localisation','idannonce','bloquer_commande','idsouscategorie','prix','referenceannonce','titre','validite','idmembre')->where('idannonce',$articl->idannonce)->first();
    $articl['plat']=$article;
    $restauration = restauration::select('idmembre','idrestauration','designation','statut')->where('idrestauration',$article['idrestauration'])->first();
    $articl['plat']['designation']=$restauration['designation'];
  } 
  if($panier->isEmpty()){

  $panier=0;
  }
  return response()->json($panier); 
  }


      /////////LES CONTROLLEURS DE GET METHODE/////////////////
 
    public function getplat()
    {
      $article = plat::select('photo','idmenu', 'prix','idrestauration','lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche','bloquer_commande', 'dureepreparation','plat')->where('statut','acceptee')->orderBy('idmenu','desc')->paginate(30);
      foreach($article as $articl){
    //    $membre = imageannonce::where('idannonce',$articl->idannonce)->first();
   //       $panier = panier::where([["idmembre", auth('api')->user()->idmembre],["idannonce",$id],["statut",'!=',"commandé"]])->first(); 

      $result=panier::where([["idmembre", auth('api')->user()->idmembre],['idmenu','=',$articl->idmenu]])->first(); 
      if($result){
        $articl['idpanier']=$result->idpanier;
      }else{
        $articl['idpanier']=null;
      }
        if(File::exists(storage_path('app/public/compteur/'.$articl->idmenu.'_menu.txt'))){
        $file=File::get(storage_path('app/public/compteur/'.$articl->idmenu.'_menu.txt'));
        }else {
          Storage::disk('vue')->put($articl->idmenu.'_menu.txt', 0);
          $file=0;
        }
       
        $articl['vues']=$file;
     //   $articl['url']="api.iveez.com/api/image/{imagename}";   
    }
    return response()->json($article); 
  }

  public function getrestaurant()
  {
    $list=[31,32,33,34,35,36];

    $article = restauration::select('adresse','id_dep','idmembre','designation','fermeture','idrestauration','ouverture','typerestauration')->where('statut','acceptee')->orderBy('idrestauration','desc')->paginate(30);
    foreach($article as $articl){
      if(File::exists(storage_path('app/public/compteur/'.$articl->idrestauration.'_restauration.txt'))){
      $file=File::get(storage_path('app/public/compteur/'.$articl->idrestauration.'_restauration.txt'));
      }else {
        Storage::disk('vue')->put($articl->idrestauration.'_restauration.txt', 0);
        $file=0;
      }
      $membre = imagerestauration::where('idrestauration',$articl->idrestauration)->first();

      $dept=departement::where('id_dept',$articl->id_dep)->first(); 
     $user = User::select('idmembre','codemembre')->where('idmembre',$articl->idmembre)->first();
     $articl['codemembre']=$user->codemembre;
     $articl['photorestauration']=$membre->urlimagerestauration;

     $articl['departement']=$dept->lib_dept;
      $articl['vues']=$file;
      $servicevendu = servicevendu::select('dateachat','datefinservice','idservice')->whereIn('idservice', $list)->where([['datefinservice','>',date("Y/m/d-H:i")],['idannonce',$articl['idrestauration']]])->first();
      $service = service::where('idService',$servicevendu['idservice'])->first();
      $service['dateachat']=$servicevendu['dateachat'];
      $service['datefinservice']=$servicevendu['datefinservice'];
      if($servicevendu){$articl['service']=$service;}else{$articl['service']= null;}

   //   $articl['url']="api.iveez.com/api/image/{imagename}";   
  }
  return response()->json($article); 
}


public function mesrestaurants($id)
{
  $list=[31,32,33,34,35,36];

  $article = restauration::select('adresse','id_dep','idmembre','designation','fermeture','idrestauration','ouverture','statut','typerestauration')->where([['statut','acceptee'],['idmembre',$id]])->orderBy('idrestauration','desc')->get();
      foreach($article as $articl){
    //    $membre = imageannonce::where('idannonce',$articl->idannonce)->first();
    //       $panier = panier::where([["idmembre", auth('api')->user()->idmembre],["idannonce",$id],["statut",'!=',"commandé"]])->first(); 

        if(File::exists(storage_path('app/public/compteur/'.$articl->idrestauration.'_restauration.txt'))){
        $file=File::get(storage_path('app/public/compteur/'.$articl->idrestauration.'_restauration.txt'));
        }else {
          $file=0;
        }
        $membre = imagerestauration::select('urlimagerestauration')->where('idrestauration',$articl->idrestauration)->get();

        $dept=departement::where('id_dept',$articl->id_dep)->first(); 
      $user = User::select('idmembre','codemembre')->where('idmembre',$articl->idmembre)->first();
      $articl['codemembre']=$user->codemembre;
      $articl['photorestauration']=$membre;

      $articl['departement']=$dept->lib_dept;
        $articl['vues']=$file;
        $servicevendu = servicevendu::select('dateachat','datefinservice','idservice')->whereIn('idservice', $list)->where([['datefinservice','>',date("Y/m/d-H:i")],['idannonce',$articl['idrestauration']]])->first();
      $service = service::where('idService',$servicevendu['idservice'])->first();
      $service['dateachat']=$servicevendu['dateachat'];
      $service['datefinservice']=$servicevendu['datefinservice'];
      if($servicevendu){$articl['service']=$service;}else{$articl['service']= null;}

    }
  return response()->json($article); 
}

public function platrestaurant($id)
{
  $list=[697,698,699];
  $article = plat::select('photo','idmenu', 'prix','idrestauration','lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche','bloquer_commande', 'dureepreparation','statut','plat')->where([['statut','!=','suppression'],['idrestauration',$id]])->orderBy('idmenu','desc')->paginate(30);
      foreach($article as $articl){
        
        $result=panier::where([["idmembre", auth('api')->user()->idmembre],['idmenu','=',$articl->idmenu]])->first(); 
        if($result){
          $articl['idpanier']=$result->idpanier;
        }else{
          $articl['idpanier']=null;
        }
        if(File::exists(storage_path('app/public/compteur/'.$articl->idmenu.'_menu.txt'))){
        $file=File::get(storage_path('app/public/compteur/'.$articl->idmenu.'_menu.txt'));
        }else {
          Storage::disk('vue')->put($articl->idmenu.'_menu.txt', 0);
          $file=0;
        }
      
        $articl['vues']=$file;
        $servicevendu = servicevendu::select('dateachat','datefinservice','idservice')->whereIn('idservice', $list)->where([['datefinservice','>',date("Y/m/d-H:i")],['idannonce',$articl['idmenu']]])->first();
        $service = service::where('idService',$servicevendu['idservice'])->first();
        $service['dateachat']=$servicevendu['dateachat'];
        $service['datefinservice']=$servicevendu['datefinservice'];
        if($servicevendu){$articl['service']=$service;}else{$articl['service']= null;}
        
    //   $articl['url']="api.iveez.com/api/image/{imagename}";   
    }
return response()->json($article); 
}

public function oneplat($id)
{
  $articl = plat::select('photo','idmenu','statut','description','accompagnements', 'prix','idrestauration','lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche','bloquer_commande', 'categorie_plat','dureepreparation','plat')->where('idmenu',$id)->first();
  $favoris= favoris::where('id_menu',$articl['idmenu'])->first(); 
  $articl['idfavoris']=$favoris['idfavoris'];
  $result=panier::where([["idmembre", auth('api')->user()->idmembre],['idmenu','=',$articl->idmenu]])->first(); 
    if($result){
      $articl['idpanier']=$result->idpanier;
    }else{
      $articl['idpanier']=null;
    }
      $article = restauration::select('idmembre','idrestauration','designation','statut')->where('idrestauration',$articl->idrestauration)->first();
      $articl['designation']=$article->designation;
      $user = User::select('idmembre','codemembre')->where('idmembre',$article->idmembre)->first();
      $articl['codemembre']=$user->codemembre;
      
      if(File::exists(storage_path('app/public/compteur/'.$articl->idmenu.'_menu.txt'))){
      $file=File::get(storage_path('app/public/compteur/'.$articl->idmenu.'_menu.txt'));
      }else {
        Storage::disk('vue')->put($articl->idmenu.'_menu.txt', 0);
        $file=0;
      }
  //    $articl['accompagnements']=json_decode($articl['accompagnements']);

      $articl['vues']=$file;
      Storage::disk('vue')->put($articl->idmenu.'_menu.txt', $file+1);

 //   $articl['url']="api.iveez.com/api/image/{imagename}";   
//}
return response()->json($articl); 
}

public function onerestaurant($id)
{
  
  $articl = restauration::select('adresse','description','typecuisine','capacite','id_dep','idmembre','designation','fermeture','idrestauration','ouverture','statut','typerestauration')->where('idrestauration',$id)->first();
   
        if(File::exists(storage_path('app/public/compteur/'.$articl->idrestauration.'_restauration.txt'))){
        $file=File::get(storage_path('app/public/compteur/'.$articl->idrestauration.'_restauration.txt'));
        }else {
          $file=0;
        }
        $membre = imagerestauration::select('urlimagerestauration')->where('idrestauration',$articl->idrestauration)->get();

        $dept=departement::where('id_dept',$articl->id_dep)->first(); 
        $articl['departement']=$dept->lib_dept;

        $articl['typecuisine'] = explode(', ', $articl['typecuisine']);

      $user = User::select('idmembre','codemembre')->where('idmembre',$articl->idmembre)->first();
      $articl['codemembre']=$user->codemembre;
      $articl['photorestauration']=$membre;

        $articl['vues']=$file;
        Storage::disk('vue')->put($articl->idrestauration.'_restauration.txt', $file+1);
    return response()->json($articl); 
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
    $annonce = plat::select('photo','idmenu','statut', 'prix','idrestauration','lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche','bloquer_commande','plat')->where('idmenu',$test->id_menu)->first();

   
    $boutique = restauration::select('adresse','idmembre','idrestauration','statut')->where('idrestauration',$test['id_restauration'])->first();

 
    if($annonce){
      $result=panier::where([["idmembre", $id],['idmenu','=',$annonce->idmenu]])->first(); 
        if($result){
          $annonce['idpanier']=$result->idpanier;
        }else{
          $annonce['idpanier']=null;
        }
    $annonce['idfavoris']=$test->idfavoris;
    $article = restauration::select('designation')->where('idrestauration',$annonce['idrestauration'])->first();
    $annonce['designation']=$article['designation'];
    array_push($annonces, $annonce);
   }else if($boutique) {
    $user = User::select('codemembre')->where('idmembre',$boutique->idmembre)->first();
    $membre = imagerestauration::select('urlimagerestauration')->where('idrestauration',$boutique->idrestauration)->first();
    $boutique['photorestauration']=$membre->urlimagerestauration;
    $boutique['idfavoris']=$test->idfavoris;
    $boutique['codemembre']=$user->codemembre;
    array_push($showrooms, $boutique);
   }

  }
  return response()->json(['menu'=>$annonces,'restauration'=>$showrooms], 200);

}

public function addfavoris(Request $req)
{
  $favoris= new favoris;
  $favoris->id_membre=$req->id_membre;
  $favoris->id_menu=$req->id_menu;
  $favoris->id_restauration=$req->id_restauration;
  $favoris->save();
  $favoris['code']=200;
  return response($favoris); 
}
//////////////////////////////////////////



public function listeservice()
{
  $list=[31,32,33,34,35,36,697,698,699];
  $service = service::whereIn('idService',$list)->get();

//  $article=$article->paginate(15);
  return response()->json($service); 
}

public function getplatservice()
{
  $list=[697,698,699];
  $annonce = plat::select('idmenu')->where('statut','acceptee')->get();
  $servicevendu = servicevendu::select('dateachat','idannonce','datefinservice')->whereIn('idservice', $list)->whereIn('idannonce', $annonce)->where('datefinservice','>',date("Y/m/d-H:i"))->orderBy('idvente','desc')->paginate(30);
  foreach($servicevendu as $articl){
    $annonce = plat::select('photo','idmenu', 'prix','idrestauration','bloquer_commande','plat')->where('idmenu',$articl->idannonce)->first();
    $result=panier::where([["idmembre", auth('api')->user()->idmembre],['idmenu','=',$annonce['idmenu']]])->first(); 
    if($result){
      $annonce['idpanier']=$result->idpanier;
    }else{
      $annonce['idpanier']=null;
    }
    $articl['plat']=$annonce;
    unset($articl['idannonce']);
  }

  return response()->json($servicevendu); 
}


public function getrestaurationservice()
{
  $list=[31,32,33,34,35,36];
  $annonce = restauration::select('idrestauration')->where('statut','acceptee')->get();
  $servicevendu = servicevendu::select('dateachat','idannonce','datefinservice')->whereIn('idservice', $list)->whereIn('idannonce', $annonce)->where('datefinservice','>',date("Y/m/d-H:i"))->orderBy('idvente','desc')->paginate(30);
  foreach($servicevendu as $articl){
    $annonce = restauration::select('adresse','id_dep','designation', 'fermeture','idrestauration','ouverture','typerestauration')->where('idrestauration',$articl->idannonce)->first();
    $membre = imagerestauration::where('idrestauration',$annonce->idrestauration)->first();
    $annonce['photorestauration']=$membre->urlimagerestauration;
    
    unset($articl['idannonce']);
    $dept=departement::where('id_dept',$annonce->id_dep)->first(); 
    $annonce['departement']=$dept->lib_dept;

    $articl['restauration']=$annonce;
  }

  return response()->json($servicevendu); 
}


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



}
