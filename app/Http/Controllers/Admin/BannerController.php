<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Banner;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    function list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $banners = Banner::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('banner_type', 'like', "%{$value}%");
                }
            })->orderBy('id', 'desc');
            $query_param = ['search' => $request['search']];
        } else {
            $banners = Banner::orderBy('id', 'desc');
        }
        $banners = $banners->paginate(Helpers::pagination_limit())->appends($query_param);

        return view('admin-views.banner.view', compact('banners', 'search'));
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'url' => 'required',
            'image' => 'required|mimes:jpg,png,jpeg,svg,bmp,tif,tiff',
        ], [
            'url.required' => 'URL is required!',
            'image.required' => 'Image is required!',
            'image.mimes' => 'Invalid file type. Only jpg, png, jpeg, svg, bmp files are allowed.',
        ]);

        if ($validator->fails()) {
            Toastr::error($validator->errors()->first());
            return back();
        }
        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();

        $banner = new Banner;
        $banner->banner_type = $request->banner_type;
        $banner->url = $request->url;
        if ($extension === 'svg') {
            $banner->photo = ImageManager::upload('banner/', 'svg', $request->file('image'));
        } else {
            $banner->photo = ImageManager::upload('banner/', 'png', $request->file('image'));
        }
        $banner->save();
        Toastr::success('Banner added successfully!');
        return back();

    }

    public function status(Request $request)
    {
        if ($request->ajax()) {
            $banner = Banner::find($request->id);
            $banner->published = $request->status;
            $banner->save();
            $data = $request->status;
            return response()->json($data);
        }
    }

    public function edit($id)
    {
        $banner = Banner::where('id', $id)->first();
        return view('admin-views.banner.edit', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'url' => 'required',
        ], [
            'url.required' => 'url is required!',
        ]);

        $file = $request->file('image');
        $extension = !empty($file) ? $file->getClientOriginalExtension() : "";

        $banner = Banner::find($id);
        $banner->banner_type = $request->banner_type;
        $banner->resource_type = $request->resource_type;
        $banner->resource_id = $request[$request->resource_type . '_id'];
        $banner->url = $request->url;
        if ($request->file('image')) {
            if ($extension === 'svg') {
                $banner->photo = ImageManager::upload('banner/', 'svg', $request->file('image'));
            } else {
                $banner->photo = ImageManager::upload('banner/', 'png', $request->file('image'));
            }
        }
        $banner->save();

        Toastr::success('Banner updated successfully!');
        return back();
    }

    public function delete(Request $request)
    {
        $br = Banner::find($request->id);
        ImageManager::delete('/banner/' . $br['photo']);
        Banner::where('id', $request->id)->delete();
        return response()->json();
    }
}