<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobListMessage as JLR;
use App\Models\JobListMessage as JLM;
use App\Http\Resources\Message as MSR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JobListMessageController extends Controller
{
   
    public function store(Request $request)
    {

        //Validations Rules //////////////////////////
        $rules = array(
            'seller_id' => 'required',
            'buyer_id' => 'required',
            'job_id' => 'required',
        );
        /// end of Validation Rules ////////////////////

        // Validator Check /////////////////////////////
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



                // Save to DB ///////////////////////////////////////////
                $jlm = JLM::create([
                    'seller_id' => $request->seller_id,
                    'buyer_id' => $request->buyer_id,
                    'job_id' => $request->job_id
                ]);
                /////////////////////////////////////////////////////////

                // return Job API Resource JSON Response //////////////
                return response()->json([
                    "message"=>"Object Created"
                ],201);
                ///////////////////////////////////////////////////////

            } catch (\Throwable $th) {
                abort(code: 500, message: 'fail to create');
                // //throw $th;
                // return response()->json([
                //     'status' => false,
                //     'message' => $th->getMessage(),
                // ], 500);
            }
        }
        //// end of Validator Check ///////////////////////

    }

    public function show($id)
    {
        try {

            $job_list_msg_show = JLM::find($id);
            return new JLR($job_list_msg_show);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function getuserjoblist($userid)
    {
        try {

            // $getJBL = DB::table('job_list_messages')->where('seller_id', $userid)->orWhere('buyer_id',$userid)->get();

            $getJBL = JLM::where('seller_id',$userid)->orWhere('buyer_id',$userid)->get();


            // $getSeller = DB::table('users')->select('email','fullname')->where('id',$getJBL->seller_id)->get();

            // return response()->json([
            //     'seller' => $getJBL
            // ],200);

            return JLR::collection($getJBL);
            
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function textmessagesperjoblist($joblistid){
        try {
           $txt_msg = DB::table('messages')->where('job_list_msg_id',$joblistid)->get();
        //    dd($txt_msg);
           return MSR::collection($txt_msg);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

}
