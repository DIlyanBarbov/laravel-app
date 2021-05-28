<?php


namespace App\Http\Controllers;


use App\BL\FileBL;
use App\Providers\GcsServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PicturesController extends Controller
{
    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deletePicture(Request $request)
    {
        $file = FileBL::query()->where('id', '=', $request['id'])->first();
        if ($file->delete()) {
            Session::flash('success', 'Successfully deleted');
            return redirect()->route('viewPictures');
        }
        Session::flash('errors', 'Failed to delete');
        return redirect()->route('viewPictures');
    }
}
