<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::group(['namespace' => 'api'], function () {

    //#KEEPR START

    //VERIFY USER
    Route::post('verify-user', 'GeneralController@verify_user');
    Route::post('user-authentication', 'GeneralController@user_authentication');
    Route::post('logout', 'GeneralController@logout');
    //

    //PAGES
    Route::post('get-pages', 'GeneralController@get_pages');

    Route::post('force_update', 'GeneralController@force_update');

    Route::group(['middleware' => ['api_auth']], function () {

        //BANNERS
        Route::get('get-banners', 'BannerController@get_banners');
        //

        Route::get('get-countries', 'GeneralController@get_countries');
        Route::post('get-states', 'GeneralController@get_states');

        Route::get('sendNotification', 'GeneralController@sendNotification');
        Route::post('changeOrderStatus', 'CartController@changeOrderStatus');
        
        //DEVICE
        Route::post('connect-device', 'ProductController@connect_device');
        Route::post('edit-device', 'ProductController@edit_device');
        Route::post('delete-device', 'ProductController@delete_device');
        Route::post('get-connected-device', 'ProductController@get_connected_device');
        Route::get('all-available-devices', 'ProductController@all_available_devices');
        Route::get('devices-type-list', 'ProductController@devices_type_list');
        Route::post('search-device', 'ProductController@search_device');
        Route::post('get-device-detail', 'ProductController@get_device_detail');
        Route::post('device-tracking', 'ProductController@device_tracking');
        Route::post('request-device', 'ProductController@request_device');
        //

        //USER
        Route::post('delete-user-account', 'GeneralController@delete_user_account');
        Route::post('user-profile', 'GeneralController@user_profile');
        Route::post('order-detail', 'GeneralController@order_detail');
        Route::post('set-address', 'GeneralController@set_address');
        Route::post('get-address', 'GeneralController@get_address');
        Route::post('order-list', 'GeneralController@order_history');
        //

        //CART
        Route::get('get-cart', 'CartController@get_cart');
        Route::post('add-cart', 'CartController@add_to_cart');
        Route::post('remove-cart', 'CartController@remove_from_cart');
        Route::post('remove-all-cart','CartController@remove_all_from_cart');
        //

        //CHECKOUT
        Route::post('checkout', 'CartController@checkout');
        Route::post('place-order', 'CartController@place_order');
        Route::post('create-checkout', 'CartController@CreateCheckout');
        Route::post('confirm-order', 'CartController@confirm_order');
        Route::post('get-paymentIntent', 'CartController@getPaymentIntent');
        Route::post('get-confirmed_payment_intent', 'CartController@confirmed_payment_intent');
        
    });

    //#KEEPR END

    Route::group(['prefix' => 'config'], function () {
        Route::get('/', 'ConfigController@configuration');
    });

    Route::group(['prefix' => 'customer', 'middleware' => 'auth:api'], function () {
        Route::get('info', 'CustomerController@info');
        Route::put('update-profile', 'CustomerController@update_profile');
        Route::put('cm-firebase-token', 'CustomerController@update_cm_firebase_token');
        Route::get('account-delete/{id}','CustomerController@account_delete');

        Route::get('get-restricted-country-list','CustomerController@get_restricted_country_list');
        Route::get('get-restricted-zip-list','CustomerController@get_restricted_zip_list');

        Route::group(['prefix' => 'address'], function () {
            Route::get('list', 'CustomerController@address_list');
            Route::get('get/{id}', 'CustomerController@get_address');
            Route::post('add', 'CustomerController@add_new_address');
            Route::put('update', 'CustomerController@update_address');
            Route::delete('/', 'CustomerController@delete_address');
        });

        Route::group(['prefix' => 'support-ticket'], function () {
            Route::post('create', 'CustomerController@create_support_ticket');
            Route::get('get', 'CustomerController@get_support_tickets');
            Route::get('conv/{ticket_id}', 'CustomerController@get_support_ticket_conv');
            Route::post('reply/{ticket_id}', 'CustomerController@reply_support_ticket');
        });

        Route::group(['prefix' => 'wish-list'], function () {
            Route::get('/', 'CustomerController@wish_list');
            Route::post('add', 'CustomerController@add_to_wishlist');
            Route::delete('remove', 'CustomerController@remove_from_wishlist');
        });

        Route::group(['prefix' => 'order'], function () {
            Route::get('list', 'CustomerController@get_order_list');
            Route::get('details', 'CustomerController@get_order_details');
            Route::get('place', 'OrderController@place_order');
            Route::get('refund', 'OrderController@refund_request');
            Route::post('refund-store', 'OrderController@store_refund');
            Route::get('refund-details', 'OrderController@refund_details');
            Route::post('deliveryman-reviews/submit', 'ProductController@submit_deliveryman_review')->middleware('auth:api');
        });
        // Chatting
        Route::group(['prefix' => 'chat'], function () {
            Route::get('list/{type}', 'ChatController@list');
            Route::get('get-messages/{type}/{id}', 'ChatController@get_message');
            Route::post('send-message/{type}', 'ChatController@send_message');
        });

        //wallet
        Route::group(['prefix' => 'wallet'], function () {
            Route::get('list', 'UserWalletController@list');
        });
        //loyalty
        Route::group(['prefix' => 'loyalty'], function () {
            Route::get('list', 'UserLoyaltyController@list');
            Route::post('loyalty-exchange-currency', 'UserLoyaltyController@loyalty_exchange_currency');
        });
    });

    Route::group(['prefix' => 'order'], function () {
        Route::get('track', 'OrderController@track_order');
        Route::get('cancel-order','OrderController@order_cancel');
    });

    Route::group(['prefix' => 'seller'], function () {
        Route::get('/', 'SellerController@get_seller_info');
        Route::get('{seller_id}/products', 'SellerController@get_seller_products');
        Route::get('{seller_id}/all-products', 'SellerController@get_seller_all_products');
        Route::get('top', 'SellerController@get_top_sellers');
        Route::get('all', 'SellerController@get_all_sellers');
    });

    Route::group(['prefix' => 'coupon','middleware' => 'auth:api'], function () {
        Route::get('apply', 'CouponController@apply');
    });

    //map api
    Route::group(['prefix' => 'mapapi'], function () {
        Route::get('place-api-autocomplete', 'MapApiController@place_api_autocomplete');
        Route::get('distance-api', 'MapApiController@distance_api');
        Route::get('place-api-details', 'MapApiController@place_api_details');
        Route::get('geocode-api', 'MapApiController@geocode_api');
    });

});
