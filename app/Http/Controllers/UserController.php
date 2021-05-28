<?php

namespace App\Http\Controllers;

use App\BL\FileBL;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Factory|View|RedirectResponse|Redirector
     */
    public function edit(Request $request)
    {
        if ($request->method() === 'GET') {
            return view('edit');
        }

        $user = User::query()->find(Auth::user()->getAuthIdentifier());
        if ($request->method() === 'POST' && $newUsername = $request->post('name')) {
            $validator = Validator::make($request->all(), [
                'name' => 'unique:users|max:255',
            ]);

            if ($validator->fails()) {
                return redirect('/edit')->withErrors($validator)->withInput();
            }

            $user->update(['name' => $newUsername]);
            $user->save();
            Session::flash('success', 'Successfully edited username.');
            return view('edit');
        }
        if ($request->method() === 'POST' && $newEmail = $request->post('email')) {
            $validator = Validator::make($request->all(), [
                'email' => 'email',
            ]);

            if ($validator->fails()) {
                return redirect('/edit')->withErrors($validator)->withInput();
            }

            $user->update(['email' => $newEmail]);
            $user->save();
            Session::flash('success', 'Successfully edited email.');
            return view('edit');
        }
    }

    /**
     * @param Request $request
     *
     * @return Factory|View
     * @throws \Exception
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:jpg,png',
        ]);

        $file = new FileBL();
        if ($request->file()) {
            $fileToUpload = $request->file('file');
            $fileName     = $fileToUpload->getFilename();
            $mimeType     = $fileToUpload->getMimeType();
            $file->setExt($mimeType);

            $user_id = Auth::user()->getAuthIdentifier();
            if ($file->upload($fileName, $fileToUpload)) {
                $file->fill([
                    'file_name' => $fileName,
                    'path'      => 'uploads/',
                    'mime_type' => $file->getExt(),
                    'user_id'   => $user_id,
                ]);
                $file->save();
                Session::flash('success', 'File Uploaded Successfully.');
                return view('edit', ['pictures' => $this->pictures()]);
            }
        }
        return view('edit');
    }

    /**
     * @param Request $request
     *
     * @return Factory|View
     * @throws \Exception
     */
    public function viewPictures(Request $request)
    {
        return view('viewPictures', ['pictures' => $this->pictures()]);
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function pictures()
    {
        $user_id = Auth::user()->getAuthIdentifier();

        $files = FileBL::all()->where('user_id', '=', $user_id);
        $urls  = [];
        foreach ($files as $file) {
            $url    = $file->getUrl();
            $urls[] = [
                'id'  => $file['id'],
                'url' => $url,
            ];
        }

        return $urls;
    }
}
