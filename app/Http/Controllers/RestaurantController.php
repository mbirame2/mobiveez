<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\plat;
use App\User;
use App\service;
use App\departement;
use App\servicevendu;
use App\panier;
use App\specialite;
use App\commanderestauration;
use App\restauration;
use App\imagerestauration;
use App\favoris;
use App\typecuisine;
use App\gestionnaire;
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
        $annonce->plat=$req->input('plat');
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

  if($req->input('idreservationtable')){
    $panier= reservationtable::where('idreservationtable',$req->input('idreservationtable'))->first(); 
  }
  else{
    $a=reservationtable::latest('idreservationtable')->first();
    $idreservationtable=$a->idreservationtable +1 ;
    $panier= new reservationtable;
    $panier->referencereservationtable=auth('api')->user()->codemembre."r".$idreservationtable.date("dmY");
    $panier->statut='AWAITING';
    $panier->invite=$req->input('listeinvites');
    $panier->idmembre=$req->input('idmembre');
    $panier->idrestauration=$req->input('idrestauration');
    $panier->besoins=$req->input('besoins');
    $panier->nombrepersonne=$req->input('nombreplaces');

  }

  $panier->titre=$req->input('titre');
  $panier->feedback=$req->input('feedback');
  $panier->motif=$req->input('motif');
  $panier->heurearrivee=$req->input('heurereservation');
  $panier->datereservation=$req->input('datereservation');
 // $panier->statut=$req->input('statut');

  $panier->save();

 
  return response()->json(['idrestauration'=> $req->input('idrestauration') ,'idreservationtable'=>$panier->idreservationtable,'statut'=> $panier->statut], 200); 
}

public function statutreservationtable(Request $req)
{

    $panier= reservationtable::where('idreservationtable',$req->input('idreservationtable'))->first(); 
    $panier->statut=$req->input('statut');
    $panier->save();
  
    return response()->json(['message'=>'success'], 200);

}
public function listereservationid($cle, $valeur)
{

    $panier= reservationtable::select('idrestauration','titre','referencereservationtable','datereservation','heurearrivee','statut','idreservationtable','idmembre')->where($cle,$valeur)->get(); 
    foreach($panier as $articl){
      $membre = imagerestauration::where('idrestauration',$articl['idrestauration'])->first();
      $articl['photorestauration']=$membre['urlimagerestauration'];
    }
  
    return response()->json($panier);

}
public function listereservationtable($id){

  $reservationtable=reservationtable::where([['statut','!=','REJECTED'],['idmembre',$id]])->orwhere([['invite', 'LIKE','%'.$id.'%' ],['statut','!=','REJECTED']])->orderBy('idreservationtable','desc')->get();
  foreach($reservationtable as $articl){
    $membre = imagerestauration::where('idrestauration',$articl->idrestauration)->first();
    $restauration = restauration::where('idrestauration',$articl->idrestauration)->first();
    $articl['designation']=$restauration['designation'];
    $articl['photo']=$membre['urlimagerestauration'];
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

public function onereservationtable($id){

  $articl=reservationtable::where('idreservationtable',$id)->first();
 // foreach($reservationtable as $articl){
    $membre = imagerestauration::where('idrestauration',$articl['idrestauration'])->first();
    $articl['photo']=$membre['urlimagerestauration'];
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

 // }
  return response($articl); 

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
public function removemenuontable($idcommandereservationtable){
  $commande = commandereservationtable::where('idcommandereservationtable',$idcommandereservationtable)->delete();
  return response()->json(['message'=>'success'], 200);
}
public function addmenuontable(Request $req){
  $reservationtable = new commandereservationtable;
  $reservationtable->idmenu=$req->idmenu;
  $reservationtable->idmembre=$req->idmembre;
  $reservationtable->idreservationtable=$req->idreservationtable;
  $reservationtable->quantite=$req->quantite;
  $reservationtable->besoin=$req->besoin;
  $reservationtable->save();
  return response()->json(['message'=>'success'], 200);
}

public function addinvitetable($idreservation,$idmembre){
  $reservationtable=reservationtable::where('idreservationtable',$idreservation)->first();
  $invite = explode(', ', $reservationtable['invite']);
 
  if( $reservationtable['nombrepersonne'] > count($invite) ){
    array_push($invite, $idmembre);
    $invite = implode(', ', $invite);
    $reservationtable->invite=$invite;
    $reservationtable->save();
  
    return response()->json(['message'=>'success'], 200);
  } 
    return response()->json(['error'=>'Le nombre d invités ne peut pas dépasser le nombre de places réservées '], 200);
  
 

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

  if($req->input('typecuisine')){
    $typecuisine = $req->input('typecuisine');
    $dept=specialite::where('idrestauration',$annonce->idrestauration)->delete(); 

    foreach($typecuisine as $type){
      $iman= new specialite;
      $iman->idrestauration= $annonce->idrestauration;  
      $iman->idtypecuisine=$type;
      $iman->save();
    }
    $annonce['typecuisine']= $typecuisine;
  }
  $annonce['departement']=$dept['lib_dept'];
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
     $articl['photorestauration']=$membre['urlimagerestauration'];

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

  $article = restauration::select('adresse','id_dep','idmembre','designation','fermeture','idrestauration','ouverture','statut','typerestauration')->where([['statut','!=','suppression'],['idmembre',$id]])->orderBy('idrestauration','desc')->get();
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
      $articl['idmembre']=$user->idmembre;
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
  
  $articl = restauration::select('adresse','description','capacite','id_dep','idmembre','designation','fermeture','idrestauration','ouverture','statut','typerestauration')->where('idrestauration',$id)->first();
   
        if(File::exists(storage_path('app/public/compteur/'.$articl->idrestauration.'_restauration.txt'))){
        $file=File::get(storage_path('app/public/compteur/'.$articl->idrestauration.'_restauration.txt'));
        }else {
          $file=0;
        }
        $membre = imagerestauration::select('urlimagerestauration')->where('idrestauration',$articl->idrestauration)->get();

        $dept=departement::where('id_dept',$articl->id_dep)->first(); 
        $articl['departement']=$dept->lib_dept;

     //   $articl['typecuisine'] = explode(', ', $articl['typecuisine']);

      $user = User::select('idmembre','codemembre')->where('idmembre',$articl->idmembre)->first();
      $articl['codemembre']=$user->codemembre;
      $articl['photorestauration']=$membre;
      $specialite= specialite::select('idtypecuisine')->where('idrestauration',$articl['idrestauration'])->get(); 
      $type=typecuisine::whereIn('idtypecuisine',$specialite)->get();
      $articl['typecuisine']=$type;
      $favoris= favoris::where('id_restauration',$articl['idrestauration'])->first(); 
      $articl['idfavoris']=$favoris['idfavoris'];
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

public function typecuisine()
{
  $service = typecuisine::get();

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
    $article = restauration::select('designation')->where('idrestauration',$annonce['idrestauration'])->first();
    $annonce['designation']=$article['designation'];
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
    $annonce = restauration::select('adresse','idmembre','id_dep','designation', 'fermeture','idrestauration','ouverture','typerestauration')->where('idrestauration',$articl->idannonce)->first();
    $user = User::select('idmembre','codemembre')->where('idmembre',$annonce->idmembre)->first();
    $annonce['codemembre']= $user->codemembre;
    $membre = imagerestauration::where('idrestauration',$annonce->idrestauration)->first();
    $annonce['photorestauration']=$membre->urlimagerestauration;
    
    unset($articl['idannonce']);
    $dept=departement::where('id_dept',$annonce->id_dep)->first(); 
    $annonce['departement']=$dept->lib_dept;

    $articl['restauration']=$annonce;
  }

  return response()->json($servicevendu); 
}
public function searchrestaurant($name)
{
  $list=[31,32,33,34,35,36];

  $article = restauration::select('adresse','id_dep','idmembre','designation','fermeture','idrestauration','ouverture','typerestauration')->where('statut','acceptee')->where(function ($query) use($name) {
    $query->whereRaw('LOWER(designation) like ?', '%'.strtolower($name).'%');
    $query->orwhereRaw('LOWER(adresse) like ?', '%'.strtolower($name).'%');
    $query->orwhereRaw('LOWER(typerestauration) like ?', '%'.strtolower($name).'%');
    })->paginate(30);

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
public function searchplat($name)
{
  $article = plat::select('photo','idmenu', 'prix','idrestauration','lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche','bloquer_commande', 'dureepreparation','plat')->where('statut','acceptee')->where(function ($query) use($name) {
    $query->whereRaw('LOWER(prix) like ?', '%'.strtolower($name).'%');
    $query->orwhereRaw('LOWER(plat) like ?', '%'.strtolower($name).'%');
    })->paginate(30);
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
}
return response()->json($article); 
}


public function searchcategorieplat($name)
{
  $article = plat::select('photo','idmenu', 'prix','idrestauration','lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche','bloquer_commande', 'dureepreparation','plat')->where([['statut','acceptee'],['categorie_plat', 'LIKE','%'.$name.'%']])->paginate(30);
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
}
return response()->json($article); 
}


public function deleteimage($filename,$id)
{
 // $notification = notification::where('idmembre',auth('api')->user()->idmembre)->orderBy('idnotification','desc')->get(); 
  $image = imagerestauration::where('urlimagerestauration',$filename.'/'.$id)->delete();
  Storage::disk('photorestauration')->delete($id);
//  $article=$article->paginate(15);
  return response()->json(['success'=>"Suppression de l'image avec succés"], 200); 
}

public function boostplat($id)
{
  $list=[697,698,699];
  $servicevendus = servicevendu::select('datefinservice','dateachat','idservice')->where( 'idannonce','=',$id )->whereIn('idservice',$list)->orderBy('idvente','desc')->get();  
  foreach($servicevendus as $servicevendu){
    $service=service::select('nomService','montantService','module')->where('idservice',$servicevendu->idservice)->first();
    $servicevendu['service']=$service;
    
  }
//  $article=$article->paginate(15);

  return response()->json($servicevendus); 
}

public function boostrestaurant($id)
{
  $list=[31,32,33,34,35,36];
  $servicevendus = servicevendu::select('datefinservice','dateachat','idservice')->where( 'idannonce','=',$id )->whereIn('idservice',$list)->orderBy('idvente','desc')->get(); 
  foreach($servicevendus as $servicevendu){
    $service=service::select('nomService','montantService','module')->where('idservice',$servicevendu->idservice)->first();
    $servicevendu['service']=$service;
    
  }
//  $article=$article->paginate(15);

  return response()->json($servicevendus); 
}

public function deleteplat($id)
{
  $annonce = plat::where('idmenu','=',$id)->first(); ; 
  $annonce->statut='suppression';
  $annonce->save();
//  $article=$article->paginate(15);

  return response()->json(['success'=>"Suppression du plat avec succés"], 200); 
}

public function deleterestaurant($id)
{
  $annonce = restauration::where('idrestauration','=',$id)->first(); ; 
  $annonce->statut='suppression';
  $annonce->save();
//  $article=$article->paginate(15);

  return response()->json(['success'=>"Suppression du restaurant avec succés"], 200); 
}

public function bloquer_commande($idannonce,$statut)
    {
     // $commande= new commande;
      $result=plat::where('idmenu','=',$idannonce)->first(); 
 
        $result->bloquer_commande=$statut;
      
      $result->save();
      return response()->json(['success'=>"Ok"], 200);            
    }

public function buyboostrestauration(Request $req)
    {
      $result=User::where('idmembre','=',$req->idmembre)->first(); 
      if($result->compte < $req->credit){
        return response()->json(['message'=>"Credit insuffisant",'code'=>401], 200); 
      }else {
      $result->compte= $result->compte - $req->credit;
      $result->save();

      $servicevendu = new servicevendu;
      $servicevendu->idannonce= $req->idrestauration; 
      $servicevendu->etatvente= 'en attente'; 
      $servicevendu->idservice= $req->idservice; 
      $Date1=date("Y/m/d-H:i");
      $Date2 = date('Y/m/d-H:i', strtotime($Date1 . " + ".$req->days." day"));
      //$Date1=gmdate('Y/m/d-h:i', strtotime($Date1) );
      //$Date2=gmdate('Y/m/d-h:i', strtotime($Date2) );
      $servicevendu->datefinservice= $Date2 ; 
      $servicevendu->dateachat= $Date1; 
      $servicevendu->save();

    
      return response()->json(['success'=>"Enregistré avec succes"], 200); 
      }
    }


     //////////////////GESTIONNAIRE

  public function addgestionnaire(Request $req)
  {
    $gestionnaire= new gestionnaire;
    $gestionnaire->idmembre=$req->input('idgestionnaire');
    $gestionnaire->date=date("Y-m-d H:i:s");
    $gestionnaire->idrestauration= $req->input('idrestauration');
    $gestionnaire->save();
    return response(['success'=>"ajouter avec succés"], 200); 
  }
  
  public function listegestionnaire($id)
  {
    $gestionnaire= gestionnaire::where([['idmembre',$id],['idrestauration','!=',null]])->get(); 
     foreach($gestionnaire as $test){
       
    //  $membre = boutique::select('localisation','idmembre','idsouscategorie','prix','referenceannonce','titre','idannonce')->where([['idannonce',$articl->panier->idannonce],['statut','acceptee']])->first();
    $articl = restauration::select('adresse','description','capacite','id_dep','idmembre','designation','fermeture','idrestauration','ouverture','statut','typerestauration')->where('idrestauration',$test->idrestauration)->first();
   
    if(File::exists(storage_path('app/public/compteur/'.$articl->idrestauration.'_restauration.txt'))){
    $file=File::get(storage_path('app/public/compteur/'.$articl->idrestauration.'_restauration.txt'));
    }else {
      $file=0;
    }
    $membre = imagerestauration::select('urlimagerestauration')->where('idrestauration',$articl->idrestauration)->get();

    $dept=departement::where('id_dept',$articl->id_dep)->first(); 
    $articl['departement']=$dept->lib_dept;

 
  $articl['photorestauration']=$membre;
  $specialite= specialite::select('idtypecuisine')->where('idrestauration',$articl['idrestauration'])->get(); 
  $type=typecuisine::whereIn('idtypecuisine',$specialite)->get();
  $articl['typecuisine']=$type;
  $favoris= favoris::where('id_restauration',$articl['idrestauration'])->first(); 
  $articl['idfavoris']=$favoris['idfavoris'];
    $articl['vues']=$file;


 //   $annonce =boutique::where([['etatshowroom','acceptee'],['idshowroom',$test->idshowroom]])->select('idmembre','descriptionshowroom','idshowroom','heuredebut','heurefin','logoshowroom','id_dep','idcategorieshowroom','jourdebut','jourfin','localisation','telephone','nomshowroom','logoshowroom')->first();  
      $user=User::select('prenom','nom','num_whatsapp','codemembre','localisation','profil','email','telephoneportable')->where('idmembre',$articl->idmembre)->first();
  
  
    
      $test['restauration']=$articl;
      $test['restauration']['proprietaire']=$user;
      
    }
   
    return response($gestionnaire); 
  }
  
  
  public function gestionnairerestaurant($id)
  {
    $gestionnaire= gestionnaire::select('idmembre','id_gestionnaire','is_connected')->where('idrestauration',$id)->get(); 
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

  

  public function commande_plat(Request $req){
   
    $array=[];
      foreach($req->commande as $reqpanier){
    $annonce= new commanderestauration;
    $a=commanderestauration::latest('idcommanderestauration')->first();
    $idcommanderestauration=$a->idcommanderestauration +1 ;

    $annonce->referencecommande=auth('api')->user()->codemembre."-".$reqpanier['idmenu']."c".$idcommanderestauration.date("dmY");
    $annonce->idmenu=$reqpanier['idmenu'];
    $annonce->place=$reqpanier['place'];
    $annonce->adresselivraison=$reqpanier['adresse'];
    $annonce->idmembre=$reqpanier['idmembre'];
    $annonce->quantite=$reqpanier['quantite'];
    $annonce->besoin=$reqpanier['besoins'];
    $annonce->datelivraison=$reqpanier['dateheure'];
    $annonce->datecommande=date("Y/m/d-h:i");
    $annonce->accompagnements=$reqpanier['accompagnements'];
    $annonce->destinataire=$reqpanier['destinataire'];

    $annonce->save();
    array_push($array, $annonce->idcommanderestauration);
      }
      return response()->json(['idcommande'=>$array], 200);            

  }
  public function modifiercommandeplat(Request $req)
  {
   // $commande= new commande;
    $annonce=commanderestauration::where('idcommanderestauration','=',$req->input('idcommanderestauration'))->first(); 
    if($req->input('statut')){
      $annonce->statut=$req->input('statut');
      $annonce->save();
    }else{
    $annonce->adresselivraison=$req->input('adresse');
    $annonce->quantite=$req->input('quantite');
    $annonce->place=$req->input('place');
    $annonce->feedback=$req->input('feedback');
    $annonce->motif=$req->input('motif');
    $annonce->besoin=$req->input('besoin');
    $annonce->datelivraison=$req->input('dateheure');
    $annonce->save();
    }
    return response()->json($annonce);            
  }
  public function supprimercommandeplat($id)
  {
     
    $result=commanderestauration::where('idcommanderestauration','=',$id)->delete(); 
   
    return response()->json(['success'=>"Suppression de la commande avec succés"], 200); 
  }

  public function listecommandeplat($cle,$valeur)
  {
    if($cle=="idmembre"){
      $service = commanderestauration::select('idcommanderestauration','idmenu','statut','datecommande','referencecommande')->where($cle,$valeur )->get();
    }else if ($cle=="idrestauration"){
      $article = plat::select('idmenu')->where($cle,$valeur )->get();
      $service = commanderestauration::select('idcommanderestauration','idmenu','statut','datecommande','referencecommande')->whereIn('idmenu', $article)->get();
    }
    foreach($service as $articl){
      $article = plat::select('photo', 'prix','plat','idrestauration')->where('idmenu',$articl['idmenu'])->first();
      $articl['photo']=$article['photo'];
      $articl['prix']=$article['prix'];
      $articl['plat']=$article['plat'];
      $articl['idrestauration']=$article['idrestauration'];

    }
  return response()->json($service); 

}

public function onecommandeplat($id)
{

    $articl = commanderestauration::where('idcommanderestauration', $id)->first();
  
    $article = plat::select('photo', 'prix','plat','idrestauration')->where('idmenu',$articl['idmenu'])->first();
    $restauration = restauration::select('designation')->where("idrestauration",$article['idrestauration'] )->first();
    $user=User::select('prenom','nom','idmembre')->where('idmembre',$articl->idmembre)->first();

    $articl['designation']=$restauration['designation'];
    $articl['client']=$user;
    $articl['photo']=$article['photo'];
    $articl['prix']=$article['prix'];
    $articl['plat']=$article['plat'];
    $articl['idrestauration']=$article['idrestauration'];

  
return response()->json($articl); 

}

}
