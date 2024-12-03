<?php

namespace App\Http\Controllers\Api\contact;

use App\Models\Contact;
use App\Traits\response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{

    // register
    public function register(Request $request){
        // check data valid or no
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:contacts',
            'address' => 'required|string|max:255',
            'balance' => 'nullable|numeric',
            'is_active' => 'nullable|boolean',
            'activity_type' => 'nullable|string|max:255',
            'government' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
            'credit_limit' => 'nullable|numeric',
            'sales_segment_id' => 'nullable|exists:sales_segments,id',
            'contact_code' => 'nullable|string|max:4|unique:contacts',
            'contact_type' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return $this->failed($validator->errors(), 400);
        }
        // create contact
        $contact = Contact::create($request->all());
        // return response and msg 
        if($contact){
            return response()->json([
                'message' => 'Contact registered successfully',
                'contact' => $contact,
            ], 201);
        }else{
            return response()->json([
                'message' => 'Registration failed'
            ]);
        }
    }
    // login
    public function login(Request $request){

        // get contact
        $contact = Contact::where('contact_code', $request->contact_code)->where('phone', $request->phone)->first();
        // check set or no
        if (!$contact) {
            return $this->failed('Unauthorized: Invalid contact code or phone', 401, 'E01');
        }
        // crate token and login
        if (! $token = auth('contact_api')->login($contact)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        // return data
        $response = ['user'  => auth('contact_api')->user(),'token' => $token,];
        return $this->success(trans('api.success'),200, 'data', $response
        );
    }
    // logout
    public function logout()
    {
        auth('contact_api')->logout();

        return response::success(trans('auth.success'));
    }
    // show profile
    public function show()
    {

        $contact = auth('contact_api')->user();

        return $this->success(
            trans('auth.success'),
            200,
            'data',$contact
        ); 

       
        return response()->json($user);
    }
    // update profile
    public function update(Request $request)
    {
        $contact = auth('contact_api')->user();

        $filterRequest = $request->only(
            'phone','name'
        );
        $contact->update($filterRequest);
        return $this->success(trans('auth.success'),200, 'data',$contact);
    }


}
