<?php

namespace App\Http\Controllers;

use App\User;
use App\chambre;
use App\favoris;
use App\hebergement;
use App\imagechambre;
use App\reserverhotel;
use App\imagehebergement;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    /**
     * 
     *  *****  REQUETE POST ***********
     */

    public function chambre(Request $req){
  
      
        if($req->input('idchambre')){
            $chambre= chambre::where('idchambre',$req->input('idchambre'))->first();     
        }else{
            $chambre= new chambre;
            }
     
        $chambre->prix=$req->input('prix');
        $chambre->typechambre=$req->input('typechambre');
        $chambre->capacite=$req->input('capacite');
        $chambre->description=$req->input('description');
        $chambre->typelit=$req->input('typelit');
        $chambre->climatisation=$req->input('climatisation');
        $chambre->douche=$req->input('douche');
        $chambre->baignoire=$req->input('baignoire');
        $chambre->televiseur=$req->input('televiseur');
        $chambre->refrigerateur=$req->input('refrigerateur');
        $chambre->minibar=$req->input('minibar');
        $chambre->douche=$req->input('douche');
        $chambre->eauminerale=$req->input('eauminerale');
        $chambre->balcon=$req->input('balcon');
        $chambre->selectionfilm=$req->input('selectionfilm');
        $chambre->conditionannulation=$req->input('conditionannulation');
        $chambre->ventilation=$req->input('ventilation');
        $chambre->petitdejeuner=$req->input('petitdejeuner');

        $chambre->idhebergement=$req->input('idhebergement');

        $chambre->save();
        $num=$chambre->idchambre;
        for($i=0;$i<$req->numberOfImages;$i++){
            if($req->input('photochambre'.$i)){
           
         
             $img=$req->input('photochambre'.$i);
             $iman= new imagechambre;
             $base64_str = substr($img, strpos($img, ",")+1);
             //var_dump($base64_str);die();
             $data = base64_decode($base64_str);
             $time=$num+$i.'-'.time().'.png';
             Storage::disk('chambre')->put($time, $data);
             $iman->idchambre= $chambre->idchambre;  
             $iman->urlimagechambre="chambre/".$time;  
             $iman->parametreimagechambre=$i; 
             //array_push($details, $annonce);
             $iman->save();
             $chambre['photochambre'.$i]=$iman->urlimagechambre;
            }
         
           }
           Storage::disk('vue')->put($num.'_chambre.txt', 0);

        return response()->json(['succés'=>"Enregistrement du chambre avec succés"], 200);            
  
      }

      public function hebergement(Request $req){
  
      
        if($req->input('idhebergement')){
            $hebergement= hebergement::where('idhebergement',$req->input('idhebergement'))->first();     
        }else{
            $hebergement= new hebergement;
            }
        $hebergement->id_dep=$req->input('id_dep');
        $hebergement->idmembre=$req->input('idmembre');
        $hebergement->typehebergement=$req->input('typehebergement');
        $hebergement->designation=$req->input('designation');
        $hebergement->description=$req->input('description');
        $hebergement->adresse=$req->input('adresse');
        $hebergement->siteweb=$req->input('siteweb');
        $hebergement->telephone=$req->input('telephone');
        $hebergement->heurearrivee=$req->input('heurearrivee');
        $hebergement->heuredepart=$req->input('heuredepart');
        $hebergement->nombreetoile=$req->input('nombreetoile');
        $hebergement->tauxreduction=$req->input('tauxreduction');
        $hebergement->wifigratuit=$req->input('wifigratuit');
        $hebergement->restaurationinterne=$req->input('restaurationinterne');
        $hebergement->parking=$req->input('parking');
        $hebergement->navetteaeroport=$req->input('navetteaeroport');
        $hebergement->annulationgratuite=$req->input('annulationgratuite');
        $hebergement->installationpourenfant=$req->input('installationpourenfant');
        $hebergement->animaldomestiqueaccepte=$req->input('animaldomestiqueaccepte');
        $hebergement->statut="acceptee";

              
        $hebergement->save();
        $num=$hebergement->idhebergement;
        for($i=0;$i<$req->numberOfImages;$i++){
            if($req->input('photohebergement'.$i)){
           
         
             $img=$req->input('photohebergement'.$i);
             $iman= new imagehebergement;
             $base64_str = substr($img, strpos($img, ",")+1);
             //var_dump($base64_str);die();
             $data = base64_decode($base64_str);
             $time=$num+$i.'-'.time().'.png';
             Storage::disk('photohebergement')->put($time, $data);
             $iman->idhebergement= $hebergement->idhebergement;  
             $iman->urlimagehebergement="photohebergement/".$time;  
             $iman->parametreimagehebergement=$i; 
             //array_push($details, $annonce);
             $iman->save();
             $hebergement['photohebergement'.$i]=$iman->urlimagehebergement;
            }
         
           }
           Storage::disk('vue')->put($num.'_hebergement.txt', 0);

        return response()->json(['succés'=>"Enregistrement de l'hebergement avec succés"], 200);            
  
      }
  

      public function reserverhotel(Request $req){
  
        $reserverhotel= new reserverhotel;
        $reserverhotel->idchambre=$req->input('idchambre');
        $reserverhotel->idmembre=$req->input('idmembre');
        $reserverhotel->arrivee=$req->input('arrivee');
        $reserverhotel->depart=$req->input('depart');
        $reserverhotel->besoins=$req->input('besoins');
        $reserverhotel->datereservation=$req->input('datereservation');
        $reserverhotel->statut="en attente";            
        $reserverhotel->save();  

        return response()->json(['response'=>"success"], 200);            
  
      }

      public function addfavoris(Request $req)
        {
        $favoris= new favoris;
        $favoris->id_membre=$req->id_membre;
        $favoris->id_chambre=$req->id_chambre;
        $favoris->id_hebergement=$req->id_hebergement;
        $favoris->save();
        $favoris['code']=200;
        return response($favoris); 
        }



       /**
     * 
     *  *****  REQUETE GET ***********
     */


        public function getchambre() {
     //   $list=[31,32,33,34,35,36];
    
        $article = chambre::select('idhebergement','idchambre','typechambre','prix','typelit')->orderBy('idchambre','desc')->paginate(30);
        foreach($article as $articl){
            $hebergement = hebergement::where('idhebergement',$articl->idhebergement)->first();
            $articl['idmembre']=$hebergement['idmembre'];
            $articl['adresse']=$hebergement['adresse'];
            
            $membre = imagechambre::where('idchambre',$articl->idchambre)->first();
            $reserverhotel = reserverhotel::where('idchambre',$articl->idchambre)->first();
            $articl['idreservationhebergement']=$reserverhotel['idreservationhebergement'];
            $user = User::select('idmembre','codemembre')->where('idmembre',$articl->idmembre)->first();
            $articl['codemembre']=$user->codemembre;
            $articl['urlimagechambre']=$membre['urlimagechambre'];
        
           
        //   $articl['url']="api.iveez.com/api/image/{imagename}";   
        }
        return response()->json($article); 
    }

    public function gethotel() {
      //  $list=[31,32,33,34,35,36];
    
        $article = hebergement::select('idhebergement','idmembre','designation','nombreetoile','typehebergement','adresse','heurearrivee','heuredepart')->orderBy('idhebergement','desc')->paginate(30);
        foreach($article as $articl){
           
            $membre = imagehebergement::where('idhebergement',$articl->idhebergement)->first();
    
            $user = User::select('idmembre','codemembre')->where('idmembre',$articl->idmembre)->first();
            $articl['codemembre']=$user->codemembre;
            $articl['urlimagehebergement']=$membre['urlimagehebergement'];
        
           
        //   $articl['url']="api.iveez.com/api/image/{imagename}";   
        }
        return response()->json($article); 
    }


    public function onechambre($idchambre) {
        $articl = chambre::where('idchambre',$idchambre)->first();

        $favoris= favoris::where('id_chambre',$idchambre)->first(); 
        $articl['idfavoris']=$favoris['idfavoris'];
        
        $membre = imagechambre::where('idchambre',$articl->idchambre)->get();
        $articl['images']=$membre;
       
    
        $hebergement = hebergement::where('idhebergement',$articl->idhebergement)->first();
        $user = User::select('idmembre','codemembre')->where('idmembre',$hebergement->idmembre)->first();
        $articl['codemembre']=$user->codemembre;
        $articl['designation']=$hebergement['designation'];
        $articl['adresse']=$hebergement['adresse'];
        
       
        return response()->json($articl); 
    }

    public function chambreshotel($idhotel) {
        //   $list=[31,32,33,34,35,36];
       
           $article = chambre::select('idhebergement','idchambre','typechambre','prix','typelit')->where('idhebergement',$idhotel)->orderBy('idchambre','desc')->paginate(30);
           foreach($article as $articl){
               $hebergement = hebergement::where('idhebergement',$articl->idhebergement)->first();
               $articl['idmembre']=$hebergement['idmembre'];
               $articl['adresse']=$hebergement['adresse'];
               
               $membre = imagechambre::where('idchambre',$articl->idchambre)->first();
               $reserverhotel = reserverhotel::where('idchambre',$articl->idchambre)->first();
               $articl['idreservationhebergement']=$reserverhotel['idreservationhebergement'];
               
               $user = User::select('idmembre','codemembre')->where('idmembre',$articl->idmembre)->first();
               $articl['codemembre']=$user->codemembre;
               $articl['urlimagechambre']=$membre['urlimagechambre'];
           
              
           }
           return response()->json($article); 
       }
    
       public function onehotel($id) {
        //  $list=[31,32,33,34,35,36];
      
            $articl = hebergement::where('idhebergement',$id)->first();
            
            $membre = imagehebergement::where('idhebergement',$articl->idhebergement)->first();
            $articl['images']=$membre;
            $user = User::select('idmembre','codemembre')->where('idmembre',$articl->idmembre)->first();
            $articl['codemembre']=$user->codemembre;        
                
            $favoris= favoris::where('id_hebergement',$id)->first(); 
            $articl['idfavoris']=$favoris['idfavoris'];
            
            if(File::exists(storage_path('app/public/compteur/'.$id.'_hebergement.txt'))){
            $file=File::get(storage_path('app/public/compteur/'.$id.'_hebergement.txt'));
            }else {
            Storage::disk('vue')->put($id.'_hebergement.txt', 0);
            $file=0;
            }
        
            $articl['vues']=$file;
            Storage::disk('vue')->put($id.'_hebergement.txt', $file+1);
        
          
          return response()->json($articl); 
      }


      ///// FAVORIS ////// 
        public function deletefavoris($id)
        {
        $favoris= favoris::where('idfavoris',$id)->delete(); 
        return response()->json(['success'=>"supprime avec succés"], 200); 
        }


//////////////////////////////////////////



}
