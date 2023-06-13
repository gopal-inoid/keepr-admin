<?php

namespace App\Http\Middleware;

use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Closure;

class ModulePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $module)
    {
        if (Helpers::module_permission_check($module)) {

            if(!empty($request->query())){
                foreach($request->query() as $k => $val){
                    $pages = explode('?',$k);
                    if(!empty($pages[1]) && $pages[1] == 'page'){
                        $request->merge(['page' => $val]);
                    }elseif(!empty($pages[1]) && $pages[1] == 'search'){
                        $request->merge(['search' => $val]);
                    }
                }
            }

            return $next($request);
        }

        Toastr::error('Access Denied !');
        return back();
    }
}
