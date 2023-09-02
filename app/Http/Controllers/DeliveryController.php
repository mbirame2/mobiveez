<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use App\zone;
use App\livraison;
use App\tarificationlivraison;
use Validator;

class DeliveryController extends Controller
{
    
    public function getZone($id_dept){
        $zone = zone::where('id_dept',$id_dept)->with('departement')->get();
        return response()->json($zone); 
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

    public function deliver(Request $request){

        $validatedData = Validator::make($request->all(), [ 
            'id_dept' => 'required',
            'poids' => 'required',
            'nomExpediteur' => 'required',
            'taille' => 'required',
            'typeColis' => 'required',
            'pointCollecte' => 'required',
            'telephoneDestinataire' => 'required',
            'adresseDestinataire' => 'required',
            'reference' => 'required',
            'nomDestinataire' => 'required',
            'photoColis' => 'required|file',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data['dateLivraison']=date("Y-m-d H:i:s");
        $time=auth('api')->user()->idmembre.'-'.time().$request->file('photoColis')->getClientOriginalExtension();
        if ($req->hasFile('photoColis')) {
          $apicontroller->saveimage('app/public/delivery',$time,$req->file('photoColis'));
        }
        $data['photoColis']=$time;
        livraison::create($data);

        return response()->json(['message'=>'success'], 200);

    } 

    public function getdeliver($id){
        $livraison = livraison::where('id',$id)->get(); 
        return response()->json($livraison);
    }
}
