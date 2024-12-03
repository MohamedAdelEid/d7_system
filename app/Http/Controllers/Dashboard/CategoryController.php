<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CategoryTranslation;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogsService;
use App\Notifications\CategoryNotification;
use Illuminate\Support\Facades\Notification;

class CategoryController extends Controller
{
    public $activityLogsService;
    public function __construct(ActivityLogsService $activityLogsService)
    {

        $this->activityLogsService = $activityLogsService;
    }
    public function index()
    {
        $categories = Category::tree()->get();
        return view('Dashboard.categories.index')->with([
            'categories'    => $categories,
        ]);
    }

    public function create()
    {
        $categories = Category::tree(0)->get();

        return view('Dashboard.categories.create')->with([
            'categories'    => $categories,
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $input = $request->only('parent_id', 'name');

            $category =  Category::create($input);
            $this->activityLogsService->insert([
                'subject'     => $category,
                'title' => 'Category Created : ' . $category->name,
                'user_id'     => auth()->id(),
            ]);
            DB::commit();
            if ($request->hasFile('cover')) {
                $category->addMedia($request->file('cover'))->toMediaCollection('category_cover');
            } 
            Notification::send(auth()->user(), new CategoryNotification($category, 'create', 'تم اضافة الفئة ' . $category->name . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($category->created_at)->format('Y-m-d')));
            $this->activityLogsService->insert([
                'subject'     => $category,
                'title' => 'تم إضافة فئة',
                'description' => 'تم إضافة فئة ' . $category->name,
                'proccess_type' => 'create',
                'user_id'     => auth()->id(),
            ]);
            return redirect(route('dashboard.categories.index'))->with('success', 'success');
        } catch (\Exception $ex) {
            return $ex;
            return redirect(route('dashboard.categories.index'))->with('error', 'faild');
        }
    }

    public function edit($id)
    {
        $data = Category::findOrFail($id);
        $categories = Category::tree(0)->where('id', '!=', $id)->get();

        return view('Dashboard.categories.edit')->with([
            'categories'    => $categories,
            'data'  => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $input = $request->only('parent_id', 'name');
        $category = Category::findOrFail($id);

        try {
            DB::beginTransaction();
            $category->update($input);
            $this->activityLogsService->insert([
                'subject'     => $category,
                'title' => 'Category Updated : ' . $category->name,
                'user_id'     => auth()->id(),
            ]);
            DB::commit();
            if ($request->hasFile('cover')) {
                $category->clearMediaCollection('category_cover'); // Clear the existing logo
                $category->addMedia($request->file('cover'))->toMediaCollection('category_cover');
            }
            Notification::send(auth()->user(), new CategoryNotification($category, 'update', 'تم تعديل الفئة ' . $category->name . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($category->updated_at)->format('Y-m-d')));
            $this->activityLogsService->insert([
                'subject'     => $category,
                'title' => 'تم تعديل الفئة',
                'description' => 'تم تعديل الفئة ' . $category->name,
                'proccess_type' => 'update',
                'user_id'     => auth()->id(),
            ]);
            return redirect(route('dashboard.categories.index'))->with('success', 'success');
        } catch (\Exception $ex) {
            return redirect(route('dashboard.categories.index'))->with('error', 'faild');
        }
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        $this->activityLogsService->insert([
            'subject'     => $category,
            'title' => 'Category deleted  : ' . $category->name,
            'description' => 'تم حذف الفئة ' . $category->name,
            'proccess_type' => 'delete',
            'user_id'     => auth()->id(),
        ]);
        Notification::send(auth()->user(), new CategoryNotification($category, 'delete', 'تم حذف الفئة ' . $category->name . ' بواسطة ' . auth()->user()->name . ' في تاريخ ' . \Carbon\Carbon::parse($category->deleted_at)->format('Y-m-d')));
        return redirect(route('dashboard.categories.index'))->with('success', 'success');
    }

    public function subCategories(Request $request)
    {
        $categories = Category::where('parent_id', $request->category_id)->get();
    
        return response()->json($categories);
    }
    
}
