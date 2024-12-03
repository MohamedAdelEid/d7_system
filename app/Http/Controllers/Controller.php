<?php

namespace App\Http\Controllers;

use App\Traits\response;
use App\Traits\Upload;
use Exception;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use Upload;
    use response;

    public function summernote_upload_image(Request $request){
        $path = null;
        if($request->has('file')){
            $path = $this->uploadImage($request->file('file'), 'uploads/images');
        }

        return url('uploads/images/' . $path);
    }

    public function testPopUp(){
        return [
            'title' => 'title',
            'body'  => 'test',
        ];
    }

    public function DeletePopup(Request $request){
        return [
            'title' => trans('admin.Alert'),
            'body'  => view('Dashboard.partials.delete_confirmation')->with([
                'url' => $request->delete_url,
            ])->render(),
        ];
    }
}
