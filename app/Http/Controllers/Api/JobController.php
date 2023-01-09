<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Job as JobResource;
use App\Models\Addons as AddonModel;
use App\Models\Jobs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{

    public function index()
    {

        try {
            return JobResource::collection(Jobs::paginate(9));
        } catch (\Throwable $th) {

            // abort(code: 500, message: 'fail to fetch');

            //throw $th; this throwble should be used for logs details
            // return response()->json([
            //     'status' => false,
            //     'message' => $th->getMessage(),
            // ], 500);
        }
    }

    public function store(Request $request)
    {

        //Validations Rules //////////////////////////
        $rules = array(
            'title' => 'required',
            'image' => 'required',
            'description' => 'required',
            'keyword' => 'required',
            'price' => 'required',
            'completein' => 'required',
            'addons' => 'required',
            'user_id' => 'required',
            'categ_id' => 'required',
            'subcateg_id' => 'required'
        );
        /// end of Validation Rules ////////////////////

        //Validation Custom Messages
        // $messages = array('title'=>'All data required');

        // Validator Check //////////////////////////////
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all(); //convert them into one array
            return response()->json([
                'status' => false,
                'reason' => 'Validation Fails',
                'messages' => $errors,
            ], 422);
        } else {
            # put data to DB after Succes Validation
            try {

                // start of image logics ////////////////////////////////////////////////////////////////
                $trim_imgs_path = '';
                if ($request->hasFile('image')) {
                    $imgs = $request->file('image');
                    $all_imgs_path = '';
                    if (is_array($imgs)) {
                        foreach ($imgs as $key => $img) {
                            $new_img_name = random_int(100000, 999999) . $key . '.' . $img->getClientOriginalExtension();
                            // save image in laravel Private Storage ///////////////////////////////////
                            Storage::disk('public')->put($new_img_name, file_get_contents($img));
                            /////////////////////////////////////////////////////////////////////////////
                            $all_imgs_path = $all_imgs_path . $new_img_name . ',';
                        }
                        $trim_imgs_path = substr($all_imgs_path, 0, -1);
                    } else {
                        $one_img = $request->image;
                        $trim_imgs_path = random_int(100000, 999999) . '.' . $one_img->getClientOriginalExtension();
                        // save image in laravel Private Storage ///////////////////////////////////
                        Storage::disk('public')->put($trim_imgs_path, file_get_contents($one_img));
                        /////////////////////////////////////////////////////////////////////////////
                    }
                } else {
                    $trim_imgs_path = "File Not Found";
                }
                // end of Image Logics /////////////////////////////////////////////////////////////////////////////////

                // start of Keword Logic ////////////////////////////////////////////////////////////////////////////
                $converted_keyword = '';
                if (is_array($request->keyword)) {
                    $converted_keyword = implode(',', $request->keyword);
                } else {
                    $converted_keyword = $request->keyword;
                }
                // end of Keyword Logic //////////////////////////////////////////////////////////////////////////////


                // save $req to DB //////////////////////////////
                $jobs = Jobs::create([
                    'title' => $request->title,
                    'image' => $trim_imgs_path,
                    'description' => $request->description,
                    'keyword' => $converted_keyword,
                    'price' => $request->price,
                    'completein' => $request->completein,
                    'user_id' => $request->user_id,
                    'categ_id' => $request->categ_id,
                    'subcateg_id' => $request->subcateg_id
                ]);
                /////////////////////////////////////////////////

                // start of addon logic //////////////

                if (is_array($request->addons)) {

                    foreach ($request->addons as $key => $addon) {
                        AddonModel::create([
                            "title" => $addon["title"],
                            "price" => $addon['price'],
                            "job_id" => $jobs->id
                        ]);
                    }
                } else {
                    return "Addon is not Array";
                }

                //////////////////////////////////////

                // return Job API Resource JSON Response //////////////
                return response()->json([
                    'status' => true,
                    'messages' => "Object Created",
                    'data' => $jobs->id
                ], 201);
                ///////////////////////////////////////////////////////


            } catch (\Throwable $th) {
                // abort(code: 500, message: 'fail to create');
                //throw $th;
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                ], 500);
            }
        }
        //// end of Validator Check ///////////////////////

    }

    public function show($id)
    {

        try {
            // Validation of $id should goes here


            /////////////////////////////////////

            $job = Jobs::find($id);
            if ($job) {
                return new JobResource($job);
            } else {
                return response()->json([
                    'status' => false,
                    'messages' => "Object Not Found"
                ], 404);
            }
        } catch (\Throwable $th) {
            //throw $th;
            abort(code: 500, message: 'fail to find object');
        }
    }

    public function update(Request $request, $id)
    {

        try {
            // Validation of $id should goes here


            /////////////////////////////////////

            // Validation of $request should goes here

            //Validations Rules //////////////////////////
            $rules = array(
                'title' => 'required',
                'image' => 'required',
                'description' => 'required',
                'keyword' => 'required',
                'price' => 'required',
                'completein' => 'required',
                "addons" => "required",
                'user_id' => 'required',
                'categ_id' => 'required',
                'subcateg_id' => 'required'
            );
            /// end of Validation Rules ////////////////////

            //Validation Custom Messages
            // $messages = array('title'=>'All data required');


            // Validator Check //////////////////////////////
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $messages = $validator->messages();
                $errors = $messages->all(); //convert them into one array
                return response()->json([
                    'status' => false,
                    'reason' => 'Validation Fails',
                    'messages' => $errors,
                ], 422);
            }

            /////////////////////////////////////

            $job = Jobs::find($id);
            if ($job) {
                $job->update([
                    'title' => $request->title,
                    'image' => $request->image,
                    'description' => $request->description,
                    'keyword' => $request->keyword,
                    'price' => $request->price,
                    'completein' => $request->completein,
                    'user_id' => $request->user_id,
                    'categ_id' => $request->categ_id,
                    'subcateg_id' => $request->subcateg_id
                ]);
                // start of addon logic //////////////

                if (is_array($request->addons)) {
                    $addon = AddonModel::where('job_id',$job->id)->get();
                    foreach ($request->addons as $key => $addon) {
                        $addon->update([
                            "title" => $addon["title"],
                            "price" => $addon['price'],
                            "job_id" => $job->id
                        ]);
                    }
                } else {
                    return "Addon is not Array";
                }

                //////////////////////////////////////
                return new JobResource($job);
            } else {
                return response()->json([
                    'status' => false,
                    'messages' => "Object Not Found"
                ], 404);
            }
        } catch (\Throwable $th) {
            //throw $th;
            abort(code: 500, message: 'fail to update');
            //Logs implementation goes down herer


            ////////////////////////////////////////////
        }
    }

    public function destroy($id)
    {
        // Validation of $id should goes here

        //////////////////////////////////////

        try {
            //code...
            $job = Jobs::where('id', $id)->delete();
            if ($job) {
                # code...
                return response()->json([
                    'status' => true,
                    'messages' => "Delete Success",
                    "data" => []
                ], 200);
            } else {
                # code...
                return response()->json([
                    'status' => false,
                    'messages' => "Object Not Found"
                ], 404);
            }
        } catch (\Throwable $th) {
            //throw $th;
            abort(code: 500, message: 'fail to delete');
            //Logs implementation goes down herer


            ////////////////////////////////////////////
        }
    }
}
