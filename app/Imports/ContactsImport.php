<?php

namespace App\Imports;

use App\Models\City;
use App\Models\Contact;
use App\Models\Governorate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class ContactsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Validate required fields
        $validatedData = $this->validateRow($row);
        // Check if balance is null, set default to 0
        $balance = $validatedData['balance'] ?? 0.00;

        // Check if opening_balance is null, set default to 0
        $opening_balance = $validatedData['opening_balance'] ?? 0.00;

        // Check if credit_limit is null, set default to 0
        $credit_limit = $validatedData['credit_limit'] ?? 0.00;

        // Allow address to be nullable
        $address = $validatedData['address'] ?? null;
        // Check if the code is null
        $code = $validatedData['code'];
        if (empty($code)) {
            // Get the last contact's code and increment it by 1
            $lastCode = Contact::max('code');
            $code = $lastCode ? $lastCode + 1 : 1;
        }

        // Check if the contact already exists by code
        $existingContact = Contact::where('code', $code)->first();

        // Get the governorate and city IDs from the validated data
        $governorateId = $validatedData['governorate'];
        $cityId = $validatedData['city'];
        Log::info($governorateId);
        Log::info($cityId);
        Log::info($validatedData);

        if (empty($existingContact)) {
            // If contact does not exist, create a new one
            return new Contact([
                'code'           => $code,
                'name'           => $validatedData['name'],
                'type'           => $validatedData['type'],
                'phone'          => $validatedData['phone'],
                'address'        => $address,
                'balance'        => $balance,
                'opening_balance'=> $opening_balance,
                'credit_limit'   => $credit_limit,
                'governorate_id' => $governorateId,
                'city_id'        => $cityId,
            ]);
        } else {
            // Update the existing contact
            $existingContact->fill([
                'name'           => $validatedData['name'],
                'type'           => $validatedData['type'],
                'phone'          => $validatedData['phone'],
                'address'        => $address,
                'balance'        => $balance,
                'opening_balance'=> $opening_balance,
                'credit_limit'   => $credit_limit,
                'governorate_id' => $governorateId,
                'city_id'        => $cityId,
            ]);

            $existingContact->save();

            return $existingContact;
        }
    }

    /**
     * Validate a row before processing.
     *
     * @param  array  $row
     * @return array
     */
    public function validateRow(array $row)
    {
        $validatedData = $row;
        
        $validatedData = Validator::validate([
            'code' => $row[0],
            'name'  => $row[1],
            'type'  => $row[2],
            'phone' => $row[3],
            'address' => $row[4],
            'balance' => $row[5],
            'credit_limit' => $row[6],
            'governorate' => $row[7],
            'city' => $row[8],
            'opening_balance' => $row[9],
        ], [
            'name' => 'required',
            'type'  => 'required',
            'phone' => 'required',
            'address' => 'required',
            'balance' => 'required|numeric',
            'credit_limit' => 'required|numeric',
            'opening_balance' => 'required|numeric',
            'code' => 'required',
        ]);
    
    
        //matching for governorate and city
        $governorate = $row[7];
        $city = $row[8];
    
        // Find closest governorate by name
        $governorateRecord = Governorate::where('governorate_name_ar', 'like', "%{$governorate}%")->first();
        if (!$governorateRecord) {
            throw ValidationException::withMessages(['governorate' => "Governorate '{$governorate}' not found"]);
        }
    
        // Find closest city by name and governorate ID
        $cityRecord = City::where('city_name_ar', 'like', "%{$city}%")
                          ->where('governorate_id', $governorateRecord->id)
                          ->first();
        if (!$cityRecord) {
            throw ValidationException::withMessages(['city' => "City '{$city}' not found under governorate '{$governorate}'"]);
        }
    
        // Adding the IDs to the validated data
        $validatedData['governorate'] = $governorateRecord->id;
        $validatedData['city'] = $cityRecord->id;
        Log::info($validatedData['governorate']);
        Log::info($validatedData['city']);
        return $validatedData;
    }
    
}