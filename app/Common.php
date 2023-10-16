<?php

namespace App;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Common extends Model
{
	public static function addLog($appResponse)
	{
		$post_body  = file_get_contents('php://input');
		$reqData = json_decode($post_body, true);
		$appRequestData = addslashes(json_encode($reqData));
		$header = getallheaders();
		$headerData['x-platform']         = !empty($header['x-platform']) ? $header['x-platform'] : '';
		$headerData['x-app-version']             = !empty($header['x-app-version']) ? $header['x-app-version'] : '';
		$headerData['X-Access-Token']             = !empty($header['X-Access-Token']) ? @$header['X-Access-Token'] : '';
		if(!empty($headerData['X-Access-Token'])){
			$user_data = \DB::table('users')->select('id')->where('auth_access_token',$headerData['X-Access-Token'])->first();
		}
		$headerData = addslashes(json_encode($headerData));
		$appService =  \Request::segment(2);
		\DB::table('api_logs')->whereRaw('appCreatedDate < NOW() - INTERVAL ? DAY', 20)->delete();
		$queryParamData = array(
			'appRequestData' => $appRequestData,
			'user_id'=> $user_data->id ?? '',
			'appDeviceData' => $headerData,
			'appResponse' => json_encode($appResponse),
			'appService' => $appService,
		);
		$result = \DB::table('api_logs')->insert($queryParamData);
		return true;
	}

}
