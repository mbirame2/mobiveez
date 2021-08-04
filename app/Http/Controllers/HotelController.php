<?php

namespace App\Http\Controllers;

use File;
use App\User;
use App\chambre;
use App\favoris;
use App\service;
use App\hebergement;
use App\imagechambre;
use App\servicevendu;
use App\reserverhotel;
use App\imagehebergement;
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
        $hebergement->piscine=$req->input('piscine');
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
  
        if($req->input('idreservationhebergement')){
            $reserverhotel= reserverhotel::where('idreservationhebergement',$req->input('idreservationhebergement'))->first();     
        }else{
            $reserverhotel= new reserverhotel;
            }
        $reserverhotel->destinataire=$req->input('destinataire');

        $reserverhotel->idchambre=$req->input('idchambre');
        $reserverhotel->idmembre=$req->input('idmembre');
        $reserverhotel->arrivee=$req->input('arrivee');
        $reserverhotel->depart=$req->input('depart');
        $reserverhotel->besoins=$req->input('besoins');
        $reserverhotel->datereservation=$req->input('datereservation');
//        $reserverhotel->statut="en attente";            
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


        public function statutreservation (Request $req)
        {
        $article = reserverhotel::where('idreservationhebergement',$req->idreservationhebergement)->first();

        $article->motif=$req->motif;
        $article->statut=$req->statut;
        $article->save();
        $article['code']=200;
        return response($article); 
        }


        public function buyboosthotel(Request $req)
            {
            $result=User::where('idmembre','=',$req->idmembre)->first(); 
            if($result->compte < $req->credit){
                return response()->json(['message'=>"Credit insuffisant",'code'=>401], 200); 
            }else {
            $result->compte= $result->compte - $req->credit;
            $result->save();

            $servicevendu = new servicevendu;
            if($req->idchambre){
                $idannonce=$req->idchambre;
            }else{
                $idannonce=$req->idhebergement;
            }
            $servicevendu->idannonce= $idannonce; 
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


            public function boostvip($module,$id)
                {
                    if($module=="chambre"){
                        $list=[700,701,702];
                    }else if ($module=="hotel"){
                        $list=[43,44,45,46,47,48];
                    }
               
                $servicevendus = servicevendu::select('datefinservice','dateachat','idservice')->where( 'idannonce','=',$id )->whereIn('idservice',$list)->orderBy('idvente','desc')->get();  
                foreach($servicevendus as $servicevendu){
                    $service=service::select('nomService','montantService','module')->where('idservice',$servicevendu->idservice)->first();
                    $servicevendu['service']=$service;
                    
                }
                //  $article=$article->paginate(15);

                return response()->json($servicevendus); 
                }



    /**
     * 
     *  *****  REQUETE GET ***********
     */


        public function getchambre() {
     //   $list=[31,32,33,34,35,36];
    
        $article = chambre::select('idhebergement','idchambre','typechambre','bloquer_reservation','prix','typelit')->orderBy('idchambre','desc')->paginate(30);
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
    
        $article = hebergement::select('idhebergement','idmembre','designation','nombreetoile','typehebergement','adresse','heurearrivee','heuredepart')->where('statut','acceptee')->orderBy('idhebergement','desc')->paginate(30);
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
       
           $article = chambre::select('idhebergement','bloquer_reservation','idchambre','typechambre','prix','typelit')->where('idhebergement',$idhotel)->orderBy('idchambre','desc')->paginate(30);
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

       public function getreservationchambre($idmembre) {
        //   $list=[31,32,33,34,35,36];
       
           $article = reserverhotel::select('idmembre','idchambre','datereservation','statut')->where('idmembre',$idmembre)->get();
           foreach($article as $articl){
              
               $chambre = chambre::select('typechambre','bloquer_reservation','prix','idhebergement')->where('idchambre',$articl->idchambre)->first();
               $articl['typechambre']=$chambre['typechambre'];
               $articl['prix']=$chambre['prix'];

               $hebergement = hebergement::where('idhebergement',$chambre->idhebergement)->first();
               $articl['designation']=$hebergement['designation'];
               
               $membre = imagechambre::where('idchambre',$articl->idchambre)->first();
               $articl['urlimagechambre']=$membre['urlimagechambre'];
           
              
           }
           return response()->json($article); 
       }


       public function onereservationchambre($idreservation) {
        //   $list=[31,32,33,34,35,36];
       
           $articl = reserverhotel::where('idreservationhebergement',$idreservation)->first();
           
           $user = User::select('idmembre','codemembre','prenom','nom','localisation')->where('idmembre',$articl->destinataire)->first();
           $articl['destinataire']=$user;

               $chambre = chambre::select('typechambre','bloquer_reservation','prix','idhebergement')->where('idchambre',$articl->idchambre)->first();
               $articl['typechambre']=$chambre['typechambre'];
               $articl['prix']=$chambre['prix'];

               $hebergement = hebergement::where('idhebergement',$chambre->idhebergement)->first();
               $articl['designation']=$hebergement['designation'];
               
               $membre = imagechambre::where('idchambre',$articl->idchambre)->first();
               $articl['urlimagechambre']=$membre['urlimagechambre'];
           
            
           
           return response()->json($articl); 
       }
    
       public function onehotel($id) {
        //  $list=[31,32,33,34,35,36];
      
            $articl = hebergement::where('idhebergement',$id)->first();
            
            $membre = imagehebergement::where('idhebergement',$articl->idhebergement)->get();
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


        public function meshotels($id) {
            //  $list=[31,32,33,34,35,36];
          
              $article = hebergement::select('idhebergement','idmembre','designation','nombreetoile','typehebergement','adresse','heurearrivee','heuredepart')->where([['idmembre',$id],['statut','acceptee']])->orderBy('idhebergement','desc')->get();
              foreach($article as $articl){
                 
                  $membre = imagehebergement::where('idhebergement',$articl->idhebergement)->first();
          
                  $user = User::select('idmembre','codemembre')->where('idmembre',$articl->idmembre)->first();
                  $articl['codemembre']=$user->codemembre;
                  $articl['urlimagehebergement']=$membre['urlimagehebergement'];
              
                 
              //   $articl['url']="api.iveez.com/api/image/{imagename}";   
              }
              return response()->json($article); 
          }



          public function deleteimagehebergement($filename,$id)
          {
           // $notification = notification::where('idmembre',auth('api')->user()->idmembre)->orderBy('idnotification','desc')->get(); 
            $image = imagehebergement::where('urlimagehebergement',$filename.'/'.$id)->delete();
            Storage::disk('photohebergement')->delete($id);
        //  $article=$article->paginate(15);
            return response()->json(['success'=>"Suppression de l'image avec succés"], 200); 
          }

          public function deleteimagechambre($filename,$id)
          {
           // $notification = notification::where('idmembre',auth('api')->user()->idmembre)->orderBy('idnotification','desc')->get(); 
            $image = imagechambre::where('urlimagechambre',$filename.'/'.$id)->delete();
            Storage::disk('chambre')->delete($id);
        //  $article=$article->paginate(15);
            return response()->json(['success'=>"Suppression de l'image avec succés"], 200); 
          }

          public function deletehotel($id)
          {
            $annonce = hebergement::where('idhebergement','=',$id)->first(); 
            $annonce->statut='suppression';
            $annonce->save();
        //  $article=$article->paginate(15);
         
            return response()->json(['success'=>"Supprimer avec succés"], 200); 
          }

          public function deletechambre($id)
          {
            $annonce = chambre::where('idchambre','=',$id)->delete(); 

            return response()->json(['success'=>"Supprimer avec succés"], 200); 
          }


          public function bloquer_reservation($idchambre,$statut)
            {
            // $commande= new commande;
            $result=chambre::where('idchambre','=',$idchambre)->first(); 
        
                $result->bloquer_reservation=$statut;
            
            $result->save();
            return response()->json(['success'=>"Ok"], 200);            
            }


            public function listefavoris($id)
                {
                $favoris= favoris::where('id_membre',$id)->get(); 
                $hebergements=[];
                $chambres=[];
                foreach($favoris as $test){

                    $articl = hebergement::select('idhebergement','idmembre','designation','nombreetoile','typehebergement','adresse','heurearrivee','heuredepart')->where([['idhebergement',$test['id_hebergement']],['statut','acceptee']])->first();
                    $chambre = chambre::select('idhebergement','idchambre','typechambre','bloquer_reservation','prix','typelit')->where('idchambre',$test['id_chambre'])->first();

                    if($articl){
                        $membre = imagehebergement::where('idhebergement',$articl->idhebergement)->first();
            
                        $user = User::select('idmembre','codemembre')->where('idmembre',$articl->idmembre)->first();
                        $articl['codemembre']=$user->codemembre;
                        $articl['urlimagehebergement']=$membre['urlimagehebergement'];
                        $articl['idfavoris']=$test->idfavoris;
                        array_push($hebergements, $articl);
                    }else if ($chambre){
                        $hebergement = hebergement::where('idhebergement',$chambre->idhebergement)->first();
                        $chambre['idmembre']=$hebergement['idmembre'];
                        $chambre['adresse']=$hebergement['adresse'];
                        
                        $membre = imagechambre::where('idchambre',$chambre->idchambre)->first();
                        $reserverhotel = reserverhotel::where('idchambre',$chambre->idchambre)->first();
                        $chambre['idreservationhebergement']=$reserverhotel['idreservationhebergement'];
                        $user = User::select('idmembre','codemembre')->where('idmembre',$chambre->idmembre)->first();
                        $chambre['codemembre']=$user->codemembre;
                        $chambre['urlimagechambre']=$membre['urlimagechambre'];
                        $chambre['idfavoris']=$test->idfavoris;
                        array_push($chambres, $chambre);
                    
                    }

                }
                return response()->json(['hebergement'=>$hebergements,'chambre'=>$chambres], 200);

                }


        public function listeservice()
            {
            $list=[43,44,45,46,47,48,700,701,702];
            $service = service::whereIn('idService',$list)->get();

            //  $article=$article->paginate(15);
            return response()->json($service); 
            }


        
        public function getchambreservice() {
            //   $list=[31,32,33,34,35,36];
            $list=[43,44,45,46,47,48];
                $servicevendu = servicevendu::select('dateachat','idannonce','datefinservice')->whereIn('idservice', $list)->where('datefinservice','>',date("Y/m/d-H:i"))->orderBy('idvente','desc')->paginate(30);

                foreach($servicevendu as $article){
                    $articl = chambre::select('idhebergement','idchambre','typechambre','bloquer_reservation','prix','typelit')->where('idchambre',$article['idannonce'])->first();
                    $hebergement = hebergement::where('idhebergement',$articl->idhebergement)->first();
                    $articl['idmembre']=$hebergement['idmembre'];
                    $articl['adresse']=$hebergement['adresse'];
                    
                    $membre = imagechambre::where('idchambre',$articl->idchambre)->first();
                    $reserverhotel = reserverhotel::where('idchambre',$articl->idchambre)->first();
                    $articl['idreservationhebergement']=$reserverhotel['idreservationhebergement'];
                    $user = User::select('idmembre','codemembre')->where('idmembre',$articl->idmembre)->first();
                    $articl['codemembre']=$user->codemembre;
                    $articl['urlimagechambre']=$membre['urlimagechambre'];
                
                    $article['chambre']=$articl;
                //   $articl['url']="api.iveez.com/api/image/{imagename}";   
                }
                return response()->json($servicevendu); 
            }

        
        public function gethotelservice() {
            //  $list=[31,32,33,34,35,36];
            $list=[700,701,702];
            $annonce = hebergement::select('idhebergement')->where('statut','acceptee')->get();
            $servicevendu = servicevendu::select('dateachat','idannonce','datefinservice')->whereIn('idservice', $list)->whereIn('idannonce', $annonce)->where('datefinservice','>',date("Y/m/d-H:i"))->orderBy('idvente','desc')->paginate(30);
           
            foreach($servicevendu as $article){
                $articl = hebergement::select('idhebergement','idmembre','designation','nombreetoile','typehebergement','adresse','heurearrivee','heuredepart')->where('idhebergement',$article['idannonce'])->first();

                $membre = imagehebergement::where('idhebergement',$articl->idhebergement)->first();
        
                $user = User::select('idmembre','codemembre')->where('idmembre',$articl->idmembre)->first();
                $articl['codemembre']=$user->codemembre;
                $articl['urlimagehebergement']=$membre['urlimagehebergement'];
                $article['hebergement']=$articl;
                
            //   $articl['url']="api.iveez.com/api/image/{imagename}";   
            }
            return response()->json($servicevendu); 
        }
//////////////////////////////////////////



}
