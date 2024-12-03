<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit(){

        // return view('Dashboard.settings.edit')->with('data', $setting);
    }

    // public function update(Request $request){
    //     $setting = Setting::first();

       
       
    //     $setting->save();

    //     //update sessaions
    //     session(['site_name'    => $setting->site_name]);
    //     session(['date_format'  => $setting->date_format]);
    //     session(['time_zone'    => $setting->time_zone]);
    //     session(['allow_unit_price_update'    => $setting->allow_unit_price_update]);
    //     session(['prevent_buy_below_purchase_price'    => $setting->prevent_buy_below_purchase_price]);
    //     session(['display_total_in_invoice'    => $setting->display_total_in_invoice]);
    //     session(['display_discount_in_invoice'    => $setting->display_discount_in_invoice]);
    //     session(['display_final_price_in_invoice'    => $setting->display_final_price_in_invoice]);
    //     session(['display_credit_details_in_invoice'    => $setting->display_credit_details_in_invoice]);
    //     session(['display_contact_info_in_invoice'    => $setting->display_contact_info_in_invoice]);
    //     session(['display_branch_info_in_invoice'    => $setting->display_branch_info_in_invoice]);
    //     session(['display_invoice_date_in_invoice'    => $setting->display_invoice_date_in_invoice]);
    //     session(['display_created_by_in_invoice'    => $setting->display_created_by_in_invoice]);
    //     session(['display_ref_no_in_invoice'    => $setting->display_ref_no_in_invoice]);
    //     session(['classic_printing'    => $setting->classic_printing]);
    //     session(['thermal_printing'    => $setting->thermal_printing]);

    //     return redirect(route('dashboard.settings.edit'))->with('success', 'success');
    // }
    public function site(){
        $setting = Setting::first();
        return view('Dashboard.settings.site-settings')->with('data', $setting);
    }
    public function updateSite(Request $request){

        $setting = Setting::first();
        $setting->site_name = $request->site_name;
        $setting->date_format = $request->date_format;
        $setting->time_zone = $request->time_zone;
        if ($request->hasFile('site_image')) {
            $image = $request->file('site_image');
            $image_name = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = 'uploads/settings/' . $image_name; 
            $image->move(public_path('uploads/settings'), $image_name);
            $setting->site_image = $imagePath; 
        }
        $setting->save();
        session(['site_name'    => $setting->site_name]);
        session(['date_format'  => $setting->date_format]);
        session(['time_zone'    => $setting->time_zone]);
        session(['site_image'    => $setting->site_image]);
        return redirect(route('dashboard.settings.site'))->with('success', 'تم تحديث بيانات الشركة');
    }

    public function sales(){
        $setting = Setting::first();
        return view('Dashboard.settings.sales-settings')->with('data', $setting);
    }
    public function updateSales(Request $request){
        $setting = Setting::first();
        $setting->allow_unit_price_update = $request->allow_unit_price_update ?? 0;
        $setting->prevent_buy_below_purchase_price = $request->prevent_buy_below_purchase_price ?? 0;
        $setting->save();
        session(['allow_unit_price_update'    => $setting->allow_unit_price_update]);
        session(['prevent_buy_below_purchase_price'    => $setting->prevent_buy_below_purchase_price]);
        return redirect(route('dashboard.settings.sales'))->with('success', 'تم تحديث بيانات المبيعات');
    }

    public function invoice(){
        $setting = Setting::first();
        return view('Dashboard.settings.invoice-settings')->with('data', $setting);
    }
    public function updateInvoice(Request $request){
        $setting = Setting::first();
        $setting->display_total_in_invoice = $request->display_total_in_invoice ?? 0;
        $setting->display_discount_in_invoice = $request->display_discount_in_invoice ?? 0;
        $setting->display_final_price_in_invoice = $request->display_final_price_in_invoice ?? 0;
        $setting->display_credit_details_in_invoice = $request->display_credit_details_in_invoice ?? 0;
        $setting->display_contact_info_in_invoice = $request->display_contact_info_in_invoice ?? 0;
        $setting->display_branch_info_in_invoice = $request->display_branch_info_in_invoice ?? 0;
        $setting->display_invoice_date_in_invoice = $request->display_invoice_date_in_invoice ?? 0;
        $setting->display_created_by_in_invoice = $request->display_created_by_in_invoice ?? 0;
        $setting->display_ref_no_in_invoice = $request->display_ref_no_in_invoice ?? 0;
        $setting->image_invoice = $request->image_invoice ?? null;
        
        // Handle printing option
        $printing_option = $request->printing_option;
        $setting->classic_printing = ($printing_option === 'classic') ? 1 : 0;
        $setting->thermal_printing = ($printing_option === 'thermal') ? 1 : 0;
     
        if ($request->hasFile('image_invoice')) {
            $image = $request->file('image_invoice');
            $image_name = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = 'uploads/settings/' . $image_name; 
            $image->move(public_path('uploads/settings'), $image_name);
            $setting->image_invoice = $imagePath; 
        }
        $setting->save();
        session(['display_total_in_invoice'    => $setting->display_total_in_invoice]);
        session(['display_discount_in_invoice'    => $setting->display_discount_in_invoice]);
        session(['display_final_price_in_invoice'    => $setting->display_final_price_in_invoice]);
        session(['display_credit_details_in_invoice'    => $setting->display_credit_details_in_invoice]);
        session(['display_contact_info_in_invoice'    => $setting->display_contact_info_in_invoice]);
        session(['display_branch_info_in_invoice'    => $setting->display_branch_info_in_invoice]);
        session(['display_invoice_date_in_invoice'    => $setting->display_invoice_date_in_invoice]);
        session(['display_created_by_in_invoice'    => $setting->display_created_by_in_invoice]);
        session(['display_ref_no_in_invoice'    => $setting->display_ref_no_in_invoice]);
        session(['classic_printing'    => $setting->classic_printing]);
        session(['thermal_printing'    => $setting->thermal_printing]);
        return redirect(route('dashboard.settings.invoice'))->with('success', 'تم تحديث بيانات الفواتير');
    }
    public function products(){
        $setting = Setting::first();
        return view('Dashboard.settings.products-settings')->with('data', $setting);
    }
    public function updateProducts(Request $request){
        $setting = Setting::first();
        $setting->display_brands = $request->display_brands ?? 0;
        $setting->display_main_category = $request->display_main_categories ?? 0;
        $setting->display_sub_category = $request->display_sub_category ?? 0;
        $setting->display_sub_units = $request->display_sub_units ?? 0;
        $setting->save();
        session(['display_brands'    => $setting->display_brands]);
        session(['display_main_category'    => $setting->display_main_category]);
        session(['display_sub_category'    => $setting->display_sub_category]);
        session(['display_sub_units'    => $setting->display_sub_units]);
        return redirect(route('dashboard.settings.products'))->with('success', 'تم تحديث بيانات المنتجات');
    }
    public function contacts(){
        $setting = Setting::first();
        return view('Dashboard.settings.contacts-settings')->with('data', $setting);
    }
    public function updateContacts(Request $request){
        $setting = Setting::first();
        $setting->default_credit_limit = $request->default_credit_limit ?? 0;
        $setting->save();
        session(['default_credit_limit'    => $setting->default_credit_limit]);
        return redirect(route('dashboard.settings.contacts'))->with('success', 'تم تحديث بيانات العملاء');
    }
}
