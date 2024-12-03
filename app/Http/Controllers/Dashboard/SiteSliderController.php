<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SiteSlider;
use Illuminate\Http\Request;

class SiteSliderController extends Controller
{
    public function index()
    {
        $data = SiteSlider::all();
        return view('Dashboard.site.sliders', compact('data'));
    }
    public function createForm()
    {
        return view('Dashboard.site.slider-create');
    }
    public function showSlider($id)
    {
        $data = SiteSlider::find($id);
        return view('Dashboard.site.slider-edit', compact('data'));
    }

    public function createSlider(Request $request)
    {
        try {
            $request->validate([
       
                'title'   => 'nullable|string|max:255',
                'sub_title'   => 'nullable|string|max:255',
       
            ]);

            $slider = new SiteSlider;
            $slider->title         = $request->title ;
            $slider->sub_title     = $request->sub_title ;
            if ($request->hasFile('slider')) {
                $slider->addMedia($request->file('slider'))->toMediaCollection('sliders');
            }
            $slider->save();

            return back()->with('success', 'تم تحديث بيانات الشركة');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()->with('error', 'حدث خطأ أثناء التحقق من البيانات.');
        }
    }

    public function updateSlider(Request $request)
    {


        try {
            $request->validate([
       
                'title'   => 'nullable|string|max:255',
                'sub_title'   => 'nullable|string|max:255',
       
            ]);
            $slider = SiteSlider::find($request->id);
            $slider->title       = $request->title;
            $slider->sub_title     = $request->sub_title;
            if ($request->hasFile('slider')) {
                $slider->clearMediaCollection('sliders'); // Clear the existing logo
                $slider->addMedia($request->file('slider'))->toMediaCollection('sliders');
            }
            $slider->save();
            
            return back()->with('success', 'تم تحديث بيانات الشركة');

          
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()->with('error', 'حدث خطأ أثناء التحقق من البيانات.');
        }
    }

    public function destroySlider($id){
        $slider = SiteSlider::find($id);
        $slider->delete();
        return back()->with('success', 'تم الحذف بنجاح');
    }

}
