<?php

namespace App\Http\Controllers\Admin;

use App\CPU\BackEndHelper;
use App\Http\Controllers\Controller;
use App\Model\ShippingMethod;
use App\Model\TaxCalculation;
use App\Model\ShippingMethodRates;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\BusinessSetting;
use App\Model\Category;
use App\Model\CategoryShippingCost;

class ShippingMethodController extends Controller
{
    public function index_admin()
    {
        $shipping_methods = ShippingMethod::where(['creator_type' => 'admin'])->get();

        return view('admin-views.shipping-method.add-new', compact('shipping_methods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|max:200',
            'duration' => 'required',
            'cost'     => 'numeric',
        ]);

        DB::table('shipping_methods')->insert([
            'creator_id'   => auth('admin')->id(),
            'creator_type' => 'admin',
            'title'        => $request['title'],
            'duration'     => $request['duration'],
            'cost'         => BackEndHelper::currency_to_usd($request['cost']),
            'status'       => 1,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        Toastr::success('Successfully added.');
        return back();
    }

    public function status_update(Request $request)
    {
        ShippingMethod::where(['id' => $request['id']])->update([
            'status' => $request['status'],
        ]);
        return response()->json([
            'success' => 1,
        ], 200);
    }

    public function edit($id)
    {
        if ($id != 1) {
            //$countries_list = DB::table('countries')->get();
            $method = ShippingMethod::where(['id' => $id])->first();
            $countries_list = ShippingMethodRates::where('shipping_id',$id)->get();
            //echo "<pre>"; print_r($countries_list); die;
            return view('admin-views.shipping-method.edit', compact('method','countries_list'));
        }
        return back();
    }

    public function update(Request $request, $id)
    {
        $countries = $request->input('country');
        //echo "<pre>"; print_r($countries); die;
        foreach($countries as $k => $val){
           $status = $val['status'] ?? 0;
           if(!empty($val['name'])){
                ShippingMethodRates::where(['shipping_id'=>$id,'country_code'=>$val['name']])->update(['normal_rate'=>$val['normal_rate'] ?? '','express_rate'=>$val['express_rate'] ?? '','status'=>$status]);
           }
        }
        ShippingMethod::where('id',$id)->update(['title'=>$request->title,'normal_duration'=>$request->normal_duration,'express_duration'=>$request->express_duration]);
        Toastr::success('Successfully updated.');
        return redirect()->back();
    }

    public function setting()
    {
        $shipping_methods = ShippingMethod::where(['creator_type' => 'admin'])->get();
        $all_category_ids = Category::where(['position' => 0])->pluck('id')->toArray();
        $category_shipping_cost_ids = CategoryShippingCost::where('seller_id',0)->pluck('category_id')->toArray();
        
        foreach($all_category_ids as $id)
        {
            if(!in_array($id,$category_shipping_cost_ids))
            {
                $new_category_shipping_cost = new CategoryShippingCost;
                $new_category_shipping_cost->seller_id = 0;
                $new_category_shipping_cost->category_id = $id;
                $new_category_shipping_cost->cost = 0;
                $new_category_shipping_cost->save();
            }
        }
        $all_category_shipping_cost = CategoryShippingCost::where('seller_id',0)->get();
        return view('admin-views.shipping-method.setting',compact('all_category_shipping_cost','shipping_methods'));
    }

    public function tax_calculation()
    {
        $tax_data =   TaxCalculation::select('id','country','type')->get();
        return view('admin-views.shipping-method.tax-setting',compact('tax_data'));
    }
    public function edit_tax($id)
    {
        $tax_data =   TaxCalculation::select('id','country','type','tax_amt')->where('id',$id)->first();
        $tx_amt = json_decode($tax_data->tax_amt,true);
        $country = \DB::table('country')->select('id')->where('name',$tax_data->country)->first();
        $states = \DB::table('states')->select('id','name')->where('country_id',$country->id)->get();
        //echo "<pre>"; print_r($tx_amt);  die;
        return view('admin-views.shipping-method.edit-tax', compact('tax_data','tx_amt','states'));
    }

    public function tax_calculation_update(Request $request, $id)
    {
        $tax = [];
        if($request->tax){
            foreach($request->tax as $key => $val){
                foreach($val as $k => $v){
                    if(!empty($v)){
                        $tax[$k][$key] = $v;
                    }
                    if($key == "state" && empty($val[$k])){
                        $tax[$k][$key] = "";
                    }
                }
            }

            $tax_calculation = json_encode($tax);
            TaxCalculation::where('id',$id)->update(['tax_amt'=>$tax_calculation]);
            Toastr::success('Successfully updated.');
        }else{
            Toastr::error('Error Occured.');
        }
        
        return redirect()->back();
    }

    public function shippingStore(Request $request)
    {
        DB::table('business_settings')->updateOrInsert(['type' => 'shipping_method'], [
            'value' => $request['shippingMethod']
        ]);
        //Toastr::success('Shipping Method Added Successfully!');
        //return back();
        return response()->json();
    }
    public function delete(Request $request)
    {

        $shipping = ShippingMethod::find($request->id);

        $shipping->delete();
        return response()->json();
    }

}
