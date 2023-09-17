<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use App\zone;
use App\livreur;
use App\livraison;
use App\departement;
use App\tarificationlivraison;
use Validator;
use App\Http\Controllers\ApiController;

class DeliveryController extends Controller
{
    
    public function getZone($id_dept){
        $zone = zone::where('id_dept',$id_dept)->with('departement')->get();
        return response()->json($zone); 
    }

    public function saveLivreur(Request $request, ApiController $apicontroller){

        $validatedData = Validator::make($request->all(), [ 
            'nomlivreur' => 'required',
            'prenomlivreur' => 'required',
            'email' => 'required',
            'telephone' => 'required',
            'typelivreur' => 'required',
            'id_zone' => 'required',
            'permisconduire' => 'required|file',
            'cartedintentite' => 'required|file',
            'photolivreur' => 'required|file',
        ]);
        if ($request->hasFile('photolivreur')) {
            $photolivreur=auth('api')->user()->idmembre.time().'.'.$request->file('photolivreur')->getClientOriginalExtension();
            $apicontroller->saveimage('app/public/livreur',$photolivreur,$request->file('photolivreur'));
            $request['photolivreur']="livreur/".$photolivreur;
        }
        if ($request->hasFile('cartedintentite')) {
            $cartedintentite=auth('api')->user()->idmembre.time().'.'.$request->file('cartedintentite')->getClientOriginalExtension();
            $apicontroller->saveimage('app/public/livreur',$cartedintentite,$request->file('cartedintentite'));
            $request['cartedintentite']="livreur/".$cartedintentite;
        }
        if ($request->hasFile('permisconduire')) {
            $permisconduire=auth('api')->user()->idmembre.time().'.'.$request->file('permisconduire')->getClientOriginalExtension();
            $apicontroller->saveimage('app/public/livreur',$permisconduire,$request->file('permisconduire'));
            $request['permisconduire']="livreur/".$permisconduire;
        }

        livreur::create($request->all());

        return response()->json(['message'=>'success'], 200);
    }

    public function getTarificationZone($id){
        $tarificationlivraison = tarificationlivraison::where('id_membre',$id)->with('zone')->get(); 
        return response()->json($tarificationlivraison); 
    }

    public function tarificationZone(Request $request){

        $validatedData = Validator::make($request->all(), [ 
            '*.id_membre' => 'required',
            '*.id_zone' => 'required',
            '*.tarif' => 'required',
        ]);
        if ($validatedData->fails()) { 
            return response()->json(['error'=>$validatedData->errors()], 401);            
        }
        foreach ($request->all() as $data) {
            //Log::debug('request:', $data);
            if(isset($data['id'])){
                tarificationlivraison::where('id', $data['id'])->update($data);
            }else{
                tarificationlivraison::create($data);
            }
        }

        return response()->json(['message'=>'success'], 200);
    }

    public function deleteTarification(Request $request){
        Log::channel('custom_log')->info(date("Y-m-d").' id[0] :'.$request[0]);

        tarificationlivraison::whereIn('id', $request)->delete();

        return response()->json(['message'=>'success'], 200);

    }

    public function deliver(Request $request, ApiController $apicontroller){

        $validatedData = Validator::make($request->all(), [ 
            'id_dept' => 'required',
            'poids' => 'required',
            'nomExpediteur' => 'required',
            'taille' => 'required',
            'typeColis' => 'required',
            'pointCollecte' => 'required',
            'telephoneDestinataire' => 'required',
            'adresseDestinataire' => 'required',
            'nomDestinataire' => 'required',
            'photo' => 'required|file',
        ]);

        if ($validatedData->fails()) { 
            return response()->json(['error'=>$validatedData->errors()], 401);            
        }
        $request['dateLivraison']=date("Y-m-d H:i:s");
        $photoColis1=auth('api')->user()->idmembre.'-1-'.time().'.jpg';
        $photoColis2=auth('api')->user()->idmembre.'-2-'.time().'.jpg';
        $photoColis3=auth('api')->user()->idmembre.'-3-'.time().'.jpg';
        $photoColis4=auth('api')->user()->idmembre.'-4-'.time().'.jpg';
        $request['idmembre']=auth('api')->user()->idmembre;
        $request['photoColis1']="delivery/".$photoColis1;
        $request['photoColis2']="delivery/".$photoColis2;
        $request['photoColis3']="delivery/".$photoColis3;
        $request['photoColis4']="delivery/".$photoColis4;
        if ($request->hasFile('photoColis1')) {
          $apicontroller->saveimage('app/public/delivery',$photoColis1,$request->file('photoColis1'));
        }
        if ($request->hasFile('photoColis2')) {
            $apicontroller->saveimage('app/public/delivery',$photoColis2,$request->file('photoColis2'));
        }
        if ($request->hasFile('photoColis3')) {
            $apicontroller->saveimage('app/public/delivery',$photoColis3,$request->file('photoColis3'));
        }
        if ($request->hasFile('photoColis4')) {
          $apicontroller->saveimage('app/public/delivery',$photoColis4,$request->file('photoColis4'));
        }
        livraison::create($request->all());

        return response()->json(['message'=>'success'], 200);

    } 

    public function getdeliver($id){
        $livraison = livraison::where('id',$id)->get(); 
        $dep= departement::select('lib_dept')->where('id_dept', $livraison->id_dep)->first();
        $livraison['lib_dept']=$dep->lib_dept;
        return response()->json($livraison);
    }

    public function getlivreur(){
        $livreur = livreur::get(); 
        
        return response()->json($livreur);
    }
}
