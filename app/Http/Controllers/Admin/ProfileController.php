<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Admin;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    public function view()
    {
        $data = Admin::where('id', auth('admin')->id())->first();
        return view('admin-views.profile.view', compact('data'));
    }

    public function edit($id)
    {
        $data = Admin::where('id', $id)->first();
        $shop_banner = Helpers::get_business_settings('shop_banner');
        return view('admin-views.profile.edit', compact('data', 'shop_banner'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'phone' => 'required|numeric|digits:10',
            'email' => 'regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix'
        ]);

        $admin = Admin::find($id);
        $admin->name = $request->name;
        $admin->phone = $request->phone;
        $admin->email = trim($request->email);
        if ($request->image) {
            $admin->image = ImageManager::update('admin/', $admin->image, 'png', $request->file('image'));
        }
        $admin->save();
        Toastr::info('Profile updated successfully!');
        return back();
    }

    public function settings_password_update(Request $request)
    {
        $request->validate([
            'password' => 'required|same:confirm_password|min:8|regex:/[0-9]/|regex:/[A-Z]/',
            'confirm_password' => 'required',
        ],[
            'password.regex'=>"Must contain at least one digit and one uppercase letter",
        ]);

        $admin = Admin::find(auth('admin')->id());
        $admin->password = bcrypt($request['password']);
        $admin->save();
        Toastr::success('Admin password updated successfully!');
        return back();
    }

}
