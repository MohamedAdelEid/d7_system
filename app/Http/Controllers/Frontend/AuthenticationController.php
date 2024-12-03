<?php

namespace App\Http\Controllers\Frontend;

use App\Models\City;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Branch;
use App\Models\Contact;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Governorate;
use App\Models\SiteSetting;
use App\Models\Transaction;
use App\Models\ActivityType;
use Illuminate\Http\Request;
use App\Http\Requests\users\login;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function loginView()
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get();
        $setting = SiteSetting::get()->first();
        $brands  = Brand::all();

        return view('Frontend.auth.login', compact('categories', 'setting', 'brands'));
    }

    public function login(Request $request)
    {

        $credentials = ['email' => $request->email, 'password' => $request->password];

        if (Auth::guard('contact')->attempt($credentials)) {
            return redirect(route('index'));
        }

        return redirect(route('login'))->withErrors(['login_error' => trans('frontend.email_or_password_incorrect')]);
    }

    public function showRegistrationForm()
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get();
        $activityTypes = ActivityType::all();
        $cities = City::all();
        $governorates = Governorate::all();
        $setting = SiteSetting::get()->first();
        $brands  = Brand::all();


        return view('Frontend.auth.register', compact('cities', 'governorates', 'activityTypes', 'categories', 'setting', 'brands'));
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'activity_type_id' => 'required|integer',
            'city_id' => 'required|integer',
            'governorate_id' => 'required|integer',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $contact = Contact::create([
            'name' => $validatedData['name'],
            'type' => 'customer',
            'credit_limit' => 100000,
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'address' => $validatedData['address'],
            'activity_type_id' => $validatedData['activity_type_id'],
            'city_id' => $validatedData['city_id'],
            'governorate_id' => $validatedData['governorate_id'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $contact->contact_code = rand(1000, 9999);
        $contact->save();

        Auth::guard('contact')->login($contact);

        return  redirect()->route('index')->withErrors(['login_error' => trans('frontend.email_or_password_incorrect')]);
    }

    public function logout()
    {
        Auth::guard('contact')->logout();
        return redirect(route('index'));
    }

    public function profile()
    {
        $contact = Auth::guard('contact')->user();
        $categories = Category::with('subcategories')->whereNull('parent_id')->get();
        $setting = SiteSetting::get()->first();
        $brands  = Brand::all();


        $transactions = Transaction::where('contact_id', $contact->id)->where('type', 'sell')->get();

        $payments = Payment::where('contact_id', $contact->id)->get();
        return  view('Frontend.auth.profile', compact('contact', 'transactions', 'categories', 'setting', 'brands', 'payments'));
    }
    public function profileEdit()
    {
        $contact = Auth::guard('contact')->user();
        $categories = Category::with('subcategories')->whereNull('parent_id')->get();
        $setting = SiteSetting::get()->first();
        $brands  = Brand::all();

        $cities = City::all();
        $governorates = Governorate::all();
        $activityTypes = ActivityType::all();

        return  view('Frontend.auth.profile-edit', compact('contact', 'categories', 'setting', 'brands', 'governorates', 'cities', 'activityTypes'));
    }
    public function profileTransaction()
    {
        $contact = Auth::guard('contact')->user();
        $categories = Category::with('subcategories')->whereNull('parent_id')->get();
        $setting = SiteSetting::get()->first();
        $brands  = Brand::all();

        $transactions = Transaction::where('contact_id', $contact->id)->where('type', 'sell')->orderBy('created_at', 'desc')->get();
        return  view('Frontend.auth.profile-transactions', compact('contact', 'transactions', 'categories', 'setting', 'brands'));
    }
    public function profilePasswordEdit()
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get();
        $setting = SiteSetting::get()->first();
        $brands  = Brand::all();


        return  view('Frontend.auth.profile-password', compact('categories', 'setting', 'brands'));
    }

    public function profilePasswordUpdate(Request $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'current_password' => 'required|string|min:6',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            $contact = Auth::guard('contact')->user();

            if (!Hash::check($validatedData['current_password'], $contact->password)) {
                return back()->withErrors(['current_password' => 'كلمة السر الحالية غير صحيحة.']);
            }

            $contact->password = Hash::make($validatedData['new_password']);
            $contact->save();

            return back()->with('success', 'تم تحديث كلمة السر بنجاح.');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function profileUpdate(Request $request)
    {
        DB::beginTransaction();

        try {

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string|max:15',
                'address' => 'required|string|max:255',
                'activity_type_id' => 'required|integer',
                'city_id' => 'required|integer',
                'governorate_id' => 'required|integer',
            ]);

            $contact = Contact::find(auth('contact')->user()->id);
            $contact->name = $validatedData['name'];
            $contact->type = 'customer';  // Static value for type
            $contact->credit_limit = 100000;  // Static value for credit_limit
            $contact->email = $validatedData['email'];
            $contact->phone = $validatedData['phone'];
            $contact->address = $validatedData['address'];
            $contact->activity_type_id = $validatedData['activity_type_id'];
            $contact->city_id = $validatedData['city_id'];
            $contact->governorate_id = $validatedData['governorate_id'];

            $contact->save();

            DB::commit();

            return  redirect()->route('profile');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
