<?php

namespace App\Http\Controllers\Admin;

use App\CPU\BackEndHelper;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\BaseController;
use App\Model\Brand;
use App\Model\BusinessSetting;
use App\Model\Category;
use App\Model\Color;
use App\Model\DealOfTheDay;
use App\Model\FlashDealProduct;
use App\Model\Product;
use App\Model\ProductStock;
use App\Model\ConnectedDevice;
use App\Model\Review;
use App\Model\Translation;
use App\Model\Wishlist;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;
use function App\CPU\translate;
use App\Model\Cart;

class ProductController extends BaseController
{
    public function add_new()
    {
        $cat = Category::where(['parent_id' => 0])->get();
        $br = Brand::orderBY('name', 'ASC')->get();
        $colors = Color::get();
        
        $brand_setting = BusinessSetting::where('type', 'product_brand')->first()->value;
        $digital_product_setting = BusinessSetting::where('type', 'digital_product')->first()->value;
        return view('admin-views.product.add-new', compact('cat','colors', 'br', 'brand_setting', 'digital_product_setting'));
    }

    public function add_new_stock()
    {
        $products = Product::select('id','name','colors')->where('status', 1)->orderBy('id','desc')->get();
        //$colors = Color::get();
        $product_options = [];
        if(!empty($products)){
            $i = 0;
            foreach($products as $k => $pro){
                $stockcount = ProductStock::where('product_id',$pro->id)->count();
                if($stockcount == 0){
                    $product_options[$pro->id]['product'] = '<option value="'.$pro->id.'">'.$pro->name.'</option>';
                    if(!empty($pro->colors) && $i == 0){
                        $productcolors = explode(",",$pro->colors);
                        $pro_color = Color::select('id','name')->whereIn('id',$productcolors)->get();
                        if(!empty($pro_color)){
                            foreach($pro_color as $col){
                                $product_options[$pro->id]['colors'][] = '<option value="'.$col->id.'">'.$col->name.'</option>';
                            }
                        }
                        $i++;
                    }
                }
            }
        }
        //echo "<pre>"; print_r($product_options); die;
        return view('admin-views.product.add-new-stock', compact('products','product_options'));
    }

    public function get_product_colors(Request $request)
    {
        $products = Product::find($request->product_id);
        $product_options['colors'] = '';
        if(!empty($products)){
            if(!empty($products->colors)){
                $productcolors = explode(",",$products->colors);
                $pro_color = Color::select('id','name')->whereIn('id',$productcolors)->get();
                if(!empty($pro_color)){
                    foreach($pro_color as $col){
                        $product_options['colors'] .= '<option value="'.$col->id.'">'.$col->name.'</option>';
                    }
                }
            }
        }
        return response()->json($product_options);
    }

    public function featured_status(Request $request)
    {
        $product = Product::find($request->id);
        $product->featured = ($product['featured'] == 0 || $product['featured'] == null) ? 1 : 0;
        $product->save();
        $data = $request->status;
        return response()->json($data);
    }

    public function approve_status(Request $request)
    {
        $product = Product::find($request->id);
        $product->request_status = ($product['request_status'] == 0) ? 1 : 0;
        $product->save();

        return redirect()->route('admin.product.list', ['seller', 'status' => $product['request_status']]);
    }

    public function deny(Request $request)
    {
        $product = Product::find($request->id);
        $product->request_status = 2;
        $product->denied_note = $request->denied_note;
        $product->save();

        return redirect()->route('admin.product.list', ['seller', 'status' => 2]);
    }

    public function view($id)
    {
        $product = Product::with(['reviews'])->where(['id' => $id])->first();
        $reviews = Review::where(['product_id' => $id])->whereNull('delivery_man_id')->paginate(Helpers::pagination_limit());
        return view('admin-views.product.view', compact('product', 'reviews'));
    }

    public function store(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'name'                 => 'required',
            'purchase_price'       => 'required|numeric|min:1',
            'images'               => 'required',
            'image'                => 'required',
            'code'                 => 'required|numeric|min:1|digits_between:6,20|unique:products',
            'rssi'                 => 'required|unique:products',
            'uuid'                 => 'required|unique:products',
        ], [
            'images.required'                  => 'Product images is required!',
            'image.required'                   => 'Product thumbnail is required!',
            'code.min'                         => 'Code must be positive!',
            'code.digits_between'              => 'Code must be minimum 6 digits!',
        ]);

        if (is_null($request->name[array_search('en', $request->lang)])) {
            $validator->after(function ($validator) {
                $validator->errors()->add(
                    'name', 'Name field is required!'
                );
            });
        }

        if ($validator->errors()->count() > 0) {
            //echo "<pre>"; print_r(); die;
            $errors = Helpers::error_processor($validator);
            if(!empty($errors)){
                
                foreach($errors as $k => $val){
                    //$val = 'Product updated successfully.';
                    // if($val['code'] == 'images'){
                    //     return 
                    // }
                    // $err_array .= $val['message'] . ",";
                    Toastr::error($val['message']); return back();
                }
                
            }
        }

        $p = new Product();
        $p->user_id  = auth('admin')->id();
        $p->added_by = "admin";
        $p->name     = $request->name[array_search('en', $request->lang)];
        $p->code     = $request->code;
        $p->rssi                  = $request->rssi;
        $p->uuid                  = $request->uuid;
        $p->slug     = Str::slug($request->name[array_search('en', $request->lang)], '-') . '-' . Str::random(6);
        $p->details              = $request['description'] ?? '';
        $p->purchase_price     = BackEndHelper::currency_to_usd($request->purchase_price);

        $colors = "";
        $specifications = [];
        $faqs = [];

        //echo "<pre>"; print_r($request->colors); die;

        if(isset($request->colors) && count($request->colors) > 0){
            $colors = implode(",",$request->colors);
        }
        if(isset($request->spec) && count($request->spec) > 0){
            foreach ($request->spec['key'] as $k => $key) {
                if($key != '' && $request->spec['value'][$k] != ''){
                    $specifications[] = ['key'=>$key, 'value'=>$request->spec['value'][$k]];
                }
            }
        }
        if(isset($request->faq) && count($request->faq) > 0){
            foreach ($request->faq['question'] as $k => $question) {
                if ($question != '' && $request->faq['answer'][$k] != '') {
                    $faqs[] = ['question'=> $question, 'answer'=>$request->faq['answer'][$k]];
                }
            }
        }

        $p->colors = $colors;
        $p->specification = json_encode($specifications);
        $p->faq = json_encode($faqs);
		

        if ($request->ajax()) {
            return response()->json([], 200);
        } else {
            if ($request->file('images')) {
                foreach ($request->file('images') as $img) {
                    $product_images[] = ImageManager::upload('product/', 'png', $img);
                }
                $p->images = json_encode($product_images);
            }
            $p->thumbnail = ImageManager::upload('product/thumbnail/', 'png', $request->image);

            if($request->product_type == 'digital' && $request->digital_product_type == 'ready_product') {
                $p->digital_file_ready = ImageManager::upload('product/digital-product/', $request->digital_file_ready->getClientOriginalExtension(), $request->digital_file_ready);
            }
            // $p->meta_title       = $request->meta_title;
            // $p->meta_description = $request->meta_description;
            // $p->meta_image       = ImageManager::upload('product/meta/', 'png', $request->meta_image);
            $p->save();
            Toastr::success(translate('Product added successfully!'));
            return redirect()->route('admin.product.list');
        }
    }

    function store_stock(Request $request){
        
        $validator = Validator::make($request->all(), [
            'product_id'                 => 'required',
            'device_id'                 => 'required',
            'uuid'                 => 'required',
            'major'                 => 'required',
            'minor'                 => 'required'
        ]);

        if (is_null($request->product_id)) {
            $validator->after(function ($validator) {
                $validator->errors()->add(
                    'product_id', 'Product field is required!'
                );
            });
        }
        // echo "<pre>"; print_r($request->all()); die;
        $colors = $request->colors;
        $uuid = $request->uuid;
        $major = $request->major;
        $minor = $request->minor;
        if(!empty($request->device_id)){
            foreach($request->device_id as $k => $mac_id){
              
                $check = ProductStock::where(['product_id'=>$request->product_id,'mac_id'=>$mac_id,'uuid'=>$uuid[$k],'major'=>$major[$k],'minor'=>$minor[$k]])->count();
             
                if($check == 0){
                    ProductStock::insert(['product_id'=>$request->product_id,'mac_id'=>$mac_id,'color'=>$colors[$k] ?? NULL,'uuid'=>$uuid[$k],'major'=>$major[$k],'minor'=>$minor[$k]]);
                }
            }
        }
    

        Toastr::success(translate('Product Stocks added successfully!'));
        return redirect()->route('admin.product.stocks.list');
        
    }

    function update_stock(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'product_id'                 => 'required',
            'device_id'                 => 'required',
            'uuid'                 => 'required',
            'major'                 => 'required',
            'minor'                 => 'required'
        ]);

        if (is_null($request->product_id)) {
            $validator->after(function ($validator) {
                $validator->errors()->add(
                    'product_id', 'Product field is required!'
                );
            });
        }

        $product_stock = $request->device_id;
        ProductStock::where(['is_purchased'=>0,'product_id'=>$id])->delete();
        $colors = $request->colors;
        $uuid = $request->uuid;
        $major = $request->major;
        $minor = $request->minor;
        if(!empty($product_stock)){
            foreach($product_stock as $k => $mac_id){
                $check = ProductStock::where(['is_purchased'=>0,'product_id'=>$id,'mac_id'=>$mac_id,'uuid'=>$uuid[$k],'major'=>$major[$k],'minor'=>$minor[$k]])->count();
                if($check == 0){
                    ProductStock::insert(['product_id'=>$id,'mac_id'=>$mac_id,'color'=>$colors[$k][0] ?? NULL,'uuid'=>$uuid[$k],'major'=>$major[$k],'minor'=>$minor[$k]]);
                }
            }
        }
        
        Toastr::success(translate('Product Stocks added successfully!'));
        return redirect()->route('admin.product.stocks.list');
        
    }

    function list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        $pro = Product::where(['added_by' => 'admin']);
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $pro = $pro->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }
        $request_status = $request['status'];
        $pro = $pro->orderBy('id', 'DESC')->paginate(Helpers::pagination_limit())->appends(['status' => $request['status']])->appends($query_param);
        return view('admin-views.product.list', compact('pro', 'search', 'request_status'));
    }

    function stocks_list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        $pro = Product::select('products.name as product_name','products.id as product_id')->join('product_stocks','product_stocks.product_id','products.id');
        $pro = $pro->groupBy('product_id')->orderBy('product_stocks.id', 'DESC')->paginate(Helpers::pagination_limit())->appends($query_param);

        // echo "<pre>"; print_r($pro); die;

        return view('admin-views.product.stock-list', compact('pro', 'search'));
    }

    function current_active_device(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        $pro = ConnectedDevice::where(['status'=>1]);
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $pro = $pro->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('mac_id', 'like', "%{$value}%")
                      ->orWhere('device_name', 'like', "%{$value}%")
                      ->orWhere('device_uuid', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }
        $request_status = $request['status'];
        $pro = $pro->orderBy('id', 'DESC')->paginate(Helpers::pagination_limit())->appends(['status' => $request['status']])->appends($query_param);
        return view('admin-views.product.current-active-device', compact('pro', 'search', 'request_status'));

    }

    /**
     * Export product list by excel
     * @param Request $request
     * @param $type
     */
    public function export_excel(Request $request, $type){
        $products = Product::when($type == 'in_house', function ($q){
            $q->where(['added_by' => 'admin']);
        })->when($type != 'in_house',function ($q) use($request){
            $q->where(['added_by' => 'seller'])->where('request_status', $request->status);
        })->latest()->get();
        //export from product
        $data = [];
        foreach ($products as $item) {
            $category_id = 0;
            $sub_category_id = 0;
            $sub_sub_category_id = 0;
            foreach (json_decode($item->category_ids, true) as $category) {
                if ($category['position'] == 1) {
                    $category_id = $category['id'];
                } else if ($category['position'] == 2) {
                    $sub_category_id = $category['id'];
                } else if ($category['position'] == 3) {
                    $sub_sub_category_id = $category['id'];
                }
            }
            $data[] = [
                'name' => $item->name,
                'Product Type'          => $item->product_type,
                'category_id'           => $category_id,
                'sub_category_id'       => $sub_category_id,
                'sub_sub_category_id'   => $sub_sub_category_id,
                'brand_id'              => $item->brand_id,
                'unit'                  => $item->unit,
                'min_qty'               => $item->min_qty,
                'refundable'            => $item->refundable,
                'youtube_video_url'     => $item->video_url,
                'unit_price'            => $item->unit_price,
                'purchase_price'        => $item->purchase_price,
                'tax'                   => $item->tax,
                'discount'              => $item->discount,
                'discount_type'         => $item->discount_type,
                'current_stock'         => $item->product_type == 'physical' ? $item->current_stock : null,
                'details'               => $item->details,
                'thumbnail'             => 'thumbnail/' . $item->thumbnail,
                'Status'                => $item->status==1 ? 'Active':'Inactive',
            ];
        }

        return (new FastExcel($data))->download('product_list.xlsx');
    }

    public function updated_product_list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $pro = Product::where(['added_by' => 'seller'])
                ->where('is_shipping_cost_updated',0)
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->Where('name', 'like', "%{$value}%");
                    }
                });
            $query_param = ['search' => $request['search']];
        } else {
            $pro = Product::where(['added_by' => 'seller'])->where('is_shipping_cost_updated',0);
        }
        $pro = $pro->orderBy('id', 'DESC')->paginate(Helpers::pagination_limit())->appends($query_param);

        return view('admin-views.product.updated-product-list', compact('pro', 'search'));
    }

    public function stock_limit_list(Request $request, $type)
    {
        $stock_limit = Helpers::get_business_settings('stock_limit');
        $sort_oqrderQty = $request['sort_oqrderQty'];
        $query_param = $request->all();
        $search = $request['search'];
        if ($type == 'in_house') {
            $pro = Product::where(['added_by' => 'admin', 'product_type'=>'physical']);
        } else {
            $pro = Product::where(['added_by' => 'seller', 'product_type'=>'physical'])->where('request_status', $request->status);
        }

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $pro = $pro->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }

        $request_status = $request['status'];

        $pro = $pro->withCount('order_details')->when($request->sort_oqrderQty == 'quantity_asc', function ($q) use ($request) {
            return $q->orderBy('current_stock', 'asc');
        })
            ->when($request->sort_oqrderQty == 'quantity_desc', function ($q) use ($request) {
                return $q->orderBy('current_stock', 'desc');
            })
            ->when($request->sort_oqrderQty == 'order_asc', function ($q) use ($request) {
                return $q->orderBy('order_details_count', 'asc');
            })
            ->when($request->sort_oqrderQty == 'order_desc', function ($q) use ($request) {
                return $q->orderBy('order_details_count', 'desc');
            })
            ->when($request->sort_oqrderQty == 'default', function ($q) use ($request) {
                return $q->orderBy('id');
            })->where('current_stock', '<', $stock_limit);

        $pro = $pro->orderBy('id', 'DESC')->paginate(Helpers::pagination_limit())->appends(['status' => $request['status']])->appends($query_param);
        return view('admin-views.product.stock-limit-list', compact('pro', 'search', 'request_status', 'sort_oqrderQty', 'stock_limit'));
    }

    public function update_quantity(Request $request)
    {
        $variations = [];
        $stock_count = $request['current_stock'];
        if ($request->has('type')) {
            foreach ($request['type'] as $key => $str) {
                $item = [];
                $item['type'] = $str;
                $item['price'] = BackEndHelper::currency_to_usd(abs($request['price_' . str_replace('.', '_', $str)]));
                $item['sku'] = $request['sku_' . str_replace('.', '_', $str)];
                $item['qty'] = abs($request['qty_' . str_replace('.', '_', $str)]);
                array_push($variations, $item);
            }
        }

        $product = Product::find($request['product_id']);
        if ($stock_count >= 0) {
            $product->current_stock = $stock_count;
            $product->variation = json_encode($variations);
            $product->save();
            Toastr::success(\App\CPU\translate('product_quantity_updated_successfully!'));
            return back();
        } else {
            Toastr::warning(\App\CPU\translate('product_quantity_can_not_be_less_than_0_!'));
            return back();
        }
    }

    public function status_update(Request $request)
    {

        $product = Product::where(['id' => $request['id']])->first();
        $success = 1;

        if ($request['status'] == 1) {
            if ($product->added_by == 'seller' && ($product->request_status == 0 || $product->request_status == 2)) {
                $success = 0;
            } else {
                $product->status = $request['status'];
            }
        } else {
            $product->status = $request['status'];
        }
        $product->save();
        return response()->json([
            'success' => $success,
        ], 200);
    }

    public function status_update_stock(Request $request)
    {

        $product = ProductStock::where(['id' => $request['id']])->first();
        $success = 1;
        $product->status = $request['status'];
        $product->save();
        return response()->json([
            'success' => $success,
        ], 200);
    }

    public function updated_shipping(Request $request)
    {

        $product = Product::where(['id' => $request['product_id']])->first();
        if($request->status == 1)
        {
            $product->shipping_cost = $product->temp_shipping_cost;
            $product->is_shipping_cost_updated = $request->status;
        }else{
            $product->is_shipping_cost_updated = $request->status;
        }

        $product->save();
        return response()->json([

        ], 200);
    }

    public function get_categories(Request $request)
    {
        $cat = Category::where(['parent_id' => $request->parent_id])->get();
        $res = '<option value="' . 0 . '" disabled selected>---Select---</option>';
        foreach ($cat as $row) {
            if ($row->id == $request->sub_category) {
                $res .= '<option value="' . $row->id . '" selected >' . $row->name . '</option>';
            } else {
                $res .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
        }
        return response()->json([
            'select_tag' => $res,
        ]);
    }

    public function sku_combination(Request $request)
    {
        $options = [];
        if ($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0) {
            $colors_active = 1;
            array_push($options, $request->colors);
        } else {
            $colors_active = 0;
        }

        $unit_price = $request->unit_price;
        $product_name = $request->name[array_search('en', $request->lang)];

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }

        $combinations = Helpers::combinations($options);
        return response()->json([
            'view' => view('admin-views.product.partials._sku_combinations', compact('combinations', 'unit_price', 'colors_active', 'product_name'))->render(),
        ]);
    }

    public function get_variations(Request $request)
    {
        $product = Product::find($request['id']);
        return response()->json([
            'view' => view('admin-views.product.partials._update_stock', compact('product'))->render()
        ]);
    }

    public function edit($id)
    {
        $product = Product::withoutGlobalScopes()->with('translations')->find($id);
        $product_category = json_decode($product->category_ids);
      
        //$product->colors = json_decode($product->colors);
        $categories = Category::where(['parent_id' => 0])->get();
        $br = Brand::orderBY('name', 'ASC')->get();
				$colors = Color::get();
        $brand_setting = BusinessSetting::where('type', 'product_brand')->first()->value;
        $digital_product_setting = BusinessSetting::where('type', 'digital_product')->first()->value;

        return view('admin-views.product.edit', compact('categories', 'br' , 'colors', 'product', 'product_category','brand_setting','digital_product_setting'));
    }

    public function edit_stock($id)
    {
		$colors = Color::get();
        $product = Product::select('id','name','colors')
									->where('status', 1)
									->where('id', $id)
									->get()[0] ?? [];
        if(!empty($product)){
            $product_stock = Product::select('product_stocks.*')
            ->join('product_stocks','product_stocks.product_id','products.id')
            ->where('product_stocks.product_id',$id)
            ->get();

            return view('admin-views.product.edit-stock', compact('product','product_stock', 'colors','id'));
        }else{
            
            return redirect()->back()->with('error','Product is Disabled!');
        }
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $validator = Validator::make($request->all(), [
            'name'                 => 'required',
            'purchase_price'       => 'required|numeric|min:1',
            'images'               => 'required',
            'image'                => 'required',
            'code'                 => 'required|numeric|min:1|digits_between:6,20|unique:products',
            'rssi'                 => 'required|unique:products',
            'uuid'                 => 'required|unique:products',
        ], [
            'images.required'                  => 'Product images is required!',
            'image.required'                   => 'Product thumbnail is required!',
            'code.min'                         => 'Code must be positive!',
            'code.digits_between'              => 'Code must be minimum 6 digits!',
        ]);

        if (is_null($request->name[array_search('en', $request->lang)])) {
            $validator->after(function ($validator) {
                $validator->errors()->add(
                    'name', 'Name field is required!'
                );
            });
        }

        // if ($validator->errors()->count() > 0) {
        //     //echo "<pre>"; print_r(); die;
        //     $errors = Helpers::error_processor($validator);
        //     if(!empty($errors)){
                
        //         foreach($errors as $k => $val){
        //             //$val = 'Product updated successfully.';
        //             // if($val['code'] == 'images'){
        //             //     return 
        //             // }
        //             // $err_array .= $val['message'] . ",";
        //             Toastr::error($val['message']); return back();
        //         }
                
        //     }
           
        // }

        //echo "<pre>"; print_r($validator->errors()); die;

        $product->name = $request->name[array_search('en', $request->lang)];
        $product->code                  = $request->code;
        $product->rssi                  = $request->rssi;
        $product->uuid                  = $request->uuid;
        $product_images                 = json_decode($product->images);
        $product->details              = $request['description'] ?? '';
        $product->purchase_price     = BackEndHelper::currency_to_usd($request->purchase_price);
				
        $colors = "";
        $specifications = [];
        $faqs = [];

				if(isset($request->colors) && count($request->colors) > 0){
            $colors = implode(",",$request->colors);
        }

        if(isset($request->spec) && count($request->spec) > 0){
            foreach ($request->spec['key'] as $k => $key) {
                if($key != '' && $request->spec['value'][$k] != ''){
                    $specifications[] = ['key'=>$key, 'value'=>$request->spec['value'][$k]];
                }
            }
        }
        if(isset($request->faq) && count($request->faq) > 0){
            foreach ($request->faq['question'] as $k => $question) {
                if ($question != '' && $request->faq['answer'][$k] != '') {
                    $faqs[] = ['question'=> $question, 'answer'=>$request->faq['answer'][$k]];
                }
            }
        }

        $product->colors = $colors;
        $product->specification = json_encode($specifications);
        $product->faq = json_encode($faqs);

        //echo "<pre>"; print_r($product->specification); die;
        
        if ($request->ajax()) {
            return response()->json([], 200);
        } else {
            if ($request->file('images')) {
                foreach ($request->file('images') as $img) {
                    $product_images[] = ImageManager::upload('product/', 'png', $img);
                }
                $product->images = json_encode($product_images);
            }

            if ($request->file('image')) {
                $product->thumbnail = ImageManager::update('product/thumbnail/', $product->thumbnail, 'png', $request->file('image'));
            }
            
            $product->save();
            Toastr::success('Product updated successfully.');
            return back();
        }
    }

    public function export_stocks_excel(Request $request){
        $products = ProductStock::latest()->get();
        //export from product
        $data = [];
        foreach ($products as $item) {
            $data[] = [
                'product_id' => $item->product_id,
                'device_id'        => $item->mac_id,
                'color'        => $item->color,
                'uuid'        => $item->uuid,
                'major'        => $item->major,
                'minor'        => $item->minor,
                'purchased'        => (($item->is_purchased == 1) ? 'Yes':'No')
            ];
        }

        return (new FastExcel($data))->download('product_stocks_list.xlsx');
    }

    public function remove_image($id,$name)
    {
        $product = Product::find($id);
        $array = [];
        if (count(json_decode($product['images'])) == 1) {
            Toastr::warning('You cannot delete all images!');
            return back();
        }else{
            ImageManager::delete('/product/' . $name);
            $images = json_decode($product['images']);
            if(count($images) > 0){
                foreach ($images as $image) {
                    if ($image != $name) {
                    array_push($array, $image);
                    }
                }
            }
            Product::where('id', $id)->update([
            'images' => json_encode($array),
            ]);
            Toastr::success('Product image removed successfully!');
            return back();
        }
    }

    public function delete($id)
    {
        $product = Product::find($id);
        $translation = Translation::where('translationable_type', 'App\Model\Product')
            ->where('translationable_id', $id);
        $translation->delete();
        Cart::where('product_id', $product->id)->delete();
        foreach (json_decode($product['images'], true) as $image) {
            ImageManager::delete('/product/' . $image);
        }
        ImageManager::delete('/product/thumbnail/' . $product['thumbnail']);
        $product->delete();
        ProductStock::where(['product_id' => $id])->delete();
        
        Toastr::success('Product removed successfully!');
        return back();
    }

    public function delete_stock($id)
    {
        $product = ProductStock::where('product_id',$id);
        $product->delete();

        // Cart::where('product_id', $product->id)->delete();
        // Wishlist::where('product_id', $product->id)->delete();

        // foreach (json_decode($product['images'], true) as $image) {
        //     ImageManager::delete('/product/' . $image);
        // }
        // ImageManager::delete('/product/thumbnail/' . $product['thumbnail']);

        // FlashDealProduct::where(['product_id' => $id])->delete();
        // DealOfTheDay::where(['product_id' => $id])->delete();

        Toastr::success('Product Stock removed successfully!');
        return back();
    }

    public function bulk_import_index()
    {
        return view('admin-views.product.bulk-import');
    }

    public function bulk_import_data(Request $request)
    {
        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            Toastr::error('You have uploaded a wrong format file, please upload the right file.');
            return back();
        }


        $data = [];
        $col_key = ['name', 'category_id', 'sub_category_id', 'sub_sub_category_id', 'brand_id', 'unit', 'min_qty', 'refundable', 'youtube_video_url', 'unit_price', 'purchase_price', 'tax', 'discount', 'discount_type', 'current_stock', 'details', 'thumbnail'];
        $skip = ['youtube_video_url', 'details', 'thumbnail'];

        foreach ($collections as $collection) {
            foreach ($collection as $key => $value) {
                if ($key!="" && !in_array($key, $col_key)) {
                    Toastr::error('Please upload the correct format file.');
                    return back();
                }

                if ($key!="" && $value === "" && !in_array($key, $skip)) {
                    Toastr::error('Please fill ' . $key . ' fields');
                    return back();
                }
            }

            $thumbnail = explode('/', $collection['thumbnail']);

            array_push($data, [
                'name' => $collection['name'],
                'slug' => Str::slug($collection['name'], '-') . '-' . Str::random(6),
                'category_ids' => json_encode([['id' => (string)$collection['category_id'], 'position' => 1], ['id' => (string)$collection['sub_category_id'], 'position' => 2], ['id' => (string)$collection['sub_sub_category_id'], 'position' => 3]]),
                'brand_id' => $collection['brand_id'],
                'unit' => $collection['unit'],
                'min_qty' => $collection['min_qty'],
                'refundable' => $collection['refundable'],
                'unit_price' => $collection['unit_price'],
                'purchase_price' => $collection['purchase_price'],
                'tax' => $collection['tax'],
                'discount' => $collection['discount'],
                'discount_type' => $collection['discount_type'],
                'current_stock' => $collection['current_stock'],
                'details' => $collection['details'],
                'video_provider' => 'youtube',
                'video_url' => $collection['youtube_video_url'],
                'images' => json_encode(['def.png']),
                'thumbnail' => $thumbnail[1]??$thumbnail[0],
                'status' => 1,
                'request_status' => 1,
                'colors' => json_encode([]),
                'attributes' => json_encode([]),
                'choice_options' => json_encode([]),
                'variation' => json_encode([]),
                'featured_status' => 1,
                'added_by' => 'admin',
                'user_id' => auth('admin')->id(),
            ]);
        }
        DB::table('products')->insert($data);
        Toastr::success(count($data) . ' - Products imported successfully!');
        return back();
    }

    public function bulk_import_stocks_data(Request $request)
    {
        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            Toastr::error('You have uploaded a wrong format file, please upload the right file.');
            return back();
        }

        $cnt = 0;
        $col_key = ['product_uuid', 'device_id','color','major','minor'];
        foreach ($collections as $collection) {
            foreach ($collection as $key => $value) {
                if ($key!="" && !in_array($key, $col_key)) {
                    Toastr::error('Please upload the correct format file.');
                    return back();
                }

                if ($key!="" && $value === "") {
                    Toastr::error('Please fill ' . $key . ' fields');
                    return back();
                }
            }

            $color = Color::select('id')->where('name',ucfirst($collection['color']))->first();
            $product = Product::where('uuid',$collection['product_uuid'])->first();
            if(!empty($product)){
                $check = ProductStock::where(['product_id'=>$product['id'],'mac_id'=>$collection['device_id'],'uuid'=>$collection['product_uuid'],'major'=>$collection['major'],'minor'=>$collection['minor']])->count();
                if($check == 0){ $cnt++;
                    ProductStock::insert(['product_id'=>$product['id'],'mac_id'=>$collection['device_id'],'color'=>$color->id ?? null,'uuid'=>$collection['product_uuid'],'major'=>$collection['major'],'minor'=>$collection['minor']]);
                }
            }
        }
        
        //echo "<pre>"; print_r($data); die;
        //DB::table('product_stocks')->insert($data);
        
        Toastr::success($cnt . ' - Product Stocks imported successfully!');
        return back();
    }

    public function bulk_export_data()
    {
        $products = Product::where(['added_by' => 'admin'])->get();
        //export from product
        $storage = [];
        foreach ($products as $item) {
            $category_id = 0;
            $sub_category_id = 0;
            $sub_sub_category_id = 0;
            foreach (json_decode($item->category_ids, true) as $category) {
                if ($category['position'] == 1) {
                    $category_id = $category['id'];
                } else if ($category['position'] == 2) {
                    $sub_category_id = $category['id'];
                } else if ($category['position'] == 3) {
                    $sub_sub_category_id = $category['id'];
                }
            }
            $storage[] = [
                'name' => $item->name,
                'category_id' => $category_id,
                'sub_category_id' => $sub_category_id,
                'sub_sub_category_id' => $sub_sub_category_id,
                'brand_id' => $item->brand_id,
                'unit' => $item->unit,
                'min_qty' => $item->min_qty,
                'refundable' => $item->refundable,
                'youtube_video_url' => $item->video_url,
                'unit_price' => $item->unit_price,
                'purchase_price' => $item->purchase_price,
                'tax' => $item->tax,
                'discount' => $item->discount,
                'discount_type' => $item->discount_type,
                'current_stock' => $item->current_stock,
                'details' => $item->details,
                'thumbnail' => 'thumbnail/' . $item->thumbnail,
            ];
        }
        return (new FastExcel($storage))->download('inhouse_products.xlsx');
    }

    public function barcode(Request $request, $id)
    {

        if ($request->limit > 270) {
            Toastr::warning(translate('You can not generate more than 270 barcode'));
             return back();
        }
        $product = Product::findOrFail($id);
        $limit =  $request->limit ?? 4;
        return view('admin-views.product.barcode', compact('product', 'limit'));
    }

}
