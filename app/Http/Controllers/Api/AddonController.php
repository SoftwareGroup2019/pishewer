<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Addon as AddonResource;
use App\Models\Addons as AddonModel;
use Illuminate\Http\Request;

class AddonController extends Controller
{

    public function index()
    {
        return AddonResource::collection(AddonModel::all());
    }



    public function store(Request $request)
    {
        
    }


    public function show($id)
    {
        
    }


 
    public function update(Request $request, $id)
    {
        
    }


    public function destroy($id)
    {
        
    }
}
