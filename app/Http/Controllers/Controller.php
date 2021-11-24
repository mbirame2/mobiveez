<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

   // $req->file('imagename2')->move(public_path('storage'),$req->file('imagename2')->getClientOriginalName());
    //$article->Imagename2=$req->file('imagename2')->getClientOriginalName();
}
