<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\zone;
use App\tarificationlivraison;
use Validator;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    
    public function getZone(){
        $zone = zone::get();
        return response()->json($zone); 
    }

    public function getTarificationZone($id){
        $tarificationlivraison = tarificationlivraison::where('id_membre',$id)->get(); 
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
            $object = tarificationlivraison::create($data);
        }

        return response()->json(['message'=>'success'], 200);
    }
}
