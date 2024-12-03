<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function showSettings()
    {
        $data = SiteSetting::first();
        return view('Dashboard.site.setting', compact('data'));
    }

    public function updateSettings(Request $request)
    {


        $siteSetting = SiteSetting::first();
        try {
            $request->validate([
                'tax'       => 'nullable|numeric|min:0|max:100',
                'email'     => 'nullable|email|max:255',
                'phone'     => 'nullable|regex:/^\+?[0-9\s\-]{7,15}$/',
                'about_us'  => 'nullable|string|max:500',
                'address'   => 'nullable|string|max:255',
                'facebook'  => 'nullable|url|max:255',
                'instagram' => 'nullable|url|max:255',
                'twitter'   => 'nullable|url|max:255',
                'linkedin'  => 'nullable|url|max:255',
            ]);

            if ($siteSetting) {
                $siteSetting->tax       = $request->tax;
                $siteSetting->email     = $request->email;
                $siteSetting->phone     = $request->phone;
                $siteSetting->about_us  = $request->about_us;
                $siteSetting->address   = $request->address;
                $siteSetting->facebook  = $request->facebook;
                $siteSetting->instagram = $request->instagram;
                $siteSetting->twitter   = $request->twitter;
                $siteSetting->linkedin  = $request->linkedin;
                if ($request->hasFile('logo')) {
                    $siteSetting->clearMediaCollection('logo'); // Clear the existing logo
                    $siteSetting->addMedia($request->file('logo'))->toMediaCollection('logo');
                }
                $siteSetting->save();
                return back()->with('success', 'تم تحديث بيانات الشركة');
            }
            $siteSetting = new SiteSetting;
            $siteSetting->tax       = $request->tax;
            $siteSetting->email     = $request->email;
            $siteSetting->phone     = $request->phone;
            $siteSetting->about_us  = $request->about_us;
            $siteSetting->address   = $request->address;
            $siteSetting->facebook  = $request->facebook;
            $siteSetting->instagram = $request->instagram;
            $siteSetting->twitter   = $request->twitter;
            $siteSetting->linkedin  = $request->linkedin;
            if ($request->hasFile('logo')) {
                $siteSetting->addMedia($request->file('logo'))->toMediaCollection('logo');
            }
            $siteSetting->save();
            return back()->with('success', 'تم تحديث بيانات الشركة');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()->with('error', 'حدث خطأ أثناء التحقق من البيانات.');
        }
    }
}
