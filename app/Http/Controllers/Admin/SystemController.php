<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Model\OrderDetail;
use App\Model\SearchFunction;
use App\Model\ProductStock;
use App\Model\WithdrawRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class SystemController extends Controller
{
    public function search_function(Request $request)
    {
        $request->validate([
            'key' => 'required',
        ], [
            'key.required' => 'Product name is required!',
        ]);

        $key = explode(' ', $request->key);

        $items = SearchFunction::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('key', 'like', "%{$value}%");
            }
        })->get();

        return response()->json([
            'result' => view('admin-views.partials._search-result', compact('items'))->render(),
        ]);
    }

    //data import into search_function table
    public function importSearchFunctionData(){
        $jsonSidebarData = file_get_contents(storage_path('data/sidebar-search.json'));
        $datas = json_decode($jsonSidebarData, True);

        SearchFunction::truncate();
        foreach($datas as $data){
            SearchFunction::create($data);
        }

        dd('success');
    }

    public function maintenance_mode()
    {
        $maintenance_mode = BusinessSetting::where('type', 'maintenance_mode')->first();
        if (isset($maintenance_mode) == false) {
            DB::table('business_settings')->insert([
                'type' => 'maintenance_mode',
                'value' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('business_settings')->where(['type' => 'maintenance_mode'])->update([
                'type' => 'maintenance_mode',
                'value' => $maintenance_mode->value == 1 ? 0 : 1,
                'updated_at' => now(),
            ]);
        }

        if (isset($maintenance_mode) && $maintenance_mode->value){
            return response()->json(['message'=>'Maintenance is off.']);
        }
        return response()->json(['message'=>'Maintenance is on.']);
    }
    public function order_data()
    {
        $new_order = DB::table('orders')->where(['checked' => 0])->count();
        return response()->json([
            'success' => 1,
            'data' => ['new_order' => $new_order]
        ]);
    }

    public function update_pending_email(){
        //DB::table('cron_log')->insert(['cron type'=>'order_status','data'=>json_encode(['success' => 1,'total_updated' => 1])]);
        $new_order = DB::table('orders')->where(['order_status' => 'pending','payment_status'=>'unpaid'])
        ->whereRaw('DATE(created_at) < CURDATE() - INTERVAL 5 minutes')->get();
        $total_status = 0;
        if(!empty($new_order)){
            foreach($new_order as $val){
                $val->customer_id;
                $total_status++;
                $user_details = DB::table('users')->where('id',$val->customer_id)->first();
                if(!empty($user_details->id)){
                    $email_data = $this->getDataforEmail($val->id);
                    if(!empty($email_data)){
                        $email_data['email'] = $user_details->email ?? "";
                        $email_data['username'] = $user_details->name ?? "Keepr User";
                        $email_data['order_id'] = $val->id;
                        $this->sendKeeprEmail('order-pending-customer',$email_data);
                    }
                }
            }
        }
        return response()->json([
            'success' => 1,
            'total_email_sent' => $total_status
        ]);
    }

    public function update_place_order_status()
    {
        $new_order = DB::table('orders')->where(['order_status' => 'pending','payment_status'=>'unpaid'])
                            ->whereRaw('DATE(created_at) < CURDATE() - INTERVAL 2 hour')->get();
        $total_status = 0;
        if(!empty($new_order)){
            foreach($new_order as $val){
                $total_status++;
                DB::table('orders')->where('id',$val->id)->update(['order_status' => 'failed']);
                $user_details = DB::table('users')->where('id',$val->customer_id)->first();
                if(!empty($user_details->id)){

                    $email_data = $this->getDataforEmail($val->id);
                    if(!empty($email_data)){
                        $email_data['email'] = $user_details->email ?? "";
                        $email_data['username'] = $user_details->name ?? "Keepr User";
                        $email_data['order_id'] = $val->id;
                        $this->sendKeeprEmail('order-payment-failed-customer',$email_data);
                    }
                }
            }
        }
        DB::table('cron_log')->insert(['cron type'=>'order_status','data'=>json_encode(['success' => 1,'total_updated' => $total_status])]);
        return response()->json([
            'success' => 1,
            'total_updated' => $total_status
        ]);
    }

}
