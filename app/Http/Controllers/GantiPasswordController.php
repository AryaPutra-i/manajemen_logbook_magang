<?php

namespace App\Http\Controllers;

use App\Http\Requests\GantiPasswordRequest;
use Hash;

class GantiPasswordController extends Controller
{
    public function index()
    {
        return view('ganti-password');
    }

    public function changePassword(GantiPasswordRequest $request)
    {
        if (! Hash::check($request->validated()['passwordLama'], $request->user()->password)) {
            return back()->withErrors(['passwordLama' => 'Password lama tidak sesuai']);
        }

        $request->user()->update([
            'password' => Hash::make($request->validated()['passwordBaru']),
        ]);

        return back()->with('status', 'Password berhasil diperbarui');
    }
}
