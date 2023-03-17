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

    Route::group(['middleware' => ['api_auth']], function () {

        //BANNERS
        Route::get('get-banners', 'BannerController@get_banners');
        //

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
        //

        //USER
        Route::post('delete-user-account', 'GeneralController@delete_user_account');
        Route::post('user-profile', 'GeneralController@user_profile');
        Route::post('order-detail', 'GeneralController@order_detail');
        //

        //CART
        Route::post('add-cart', 'CartController@add_to_cart');
        Route::put('update-cart', 'CartController@update_cart');
        Route::delete('remove-cart', 'CartController@remove_from_cart');
        Route::delete('remove-all-cart','CartController@remove_all_from_cart');
        //

    });

    //#KEEPR END

    Route::group(['prefix' => 'config'], function () {
        Route::get('/', 'ConfigController@configuration');
    });

    // Route::group(['prefix' => 'products'], function () {
    //     Route::get('latest', 'ProductController@get_latest_products');
    //     Route::get('featured', 'ProductController@get_featured_products');
    //     Route::get('top-rated', 'ProductController@get_top_rated_products');
    //     Route::any('search', 'ProductController@get_searched_products');
    //     Route::get('details/{slug}', 'ProductController@get_product');
    //     Route::get('related-products/{product_id}', 'ProductController@get_related_products');
    //     Route::get('reviews/{product_id}', 'ProductController@get_product_reviews');
    //     Route::get('rating/{product_id}', 'ProductController@get_product_rating');
    //     Route::get('counter/{product_id}', 'ProductController@counter');
    //     Route::get('shipping-methods', 'ProductController@get_shipping_methods');
    //     Route::get('social-share-link/{product_id}', 'ProductController@social_share_link');
    //     Route::post('reviews/submit', 'ProductController@submit_product_review')->middleware('auth:api');
    //     Route::get('best-sellings', 'ProductController@get_best_sellings');
    //     Route::get('home-categories', 'ProductController@get_home_categories');
    //     ROute::get('discounted-product', 'ProductController@get_discounted_product');
    // });

    // Route::group(['prefix' => 'notifications'], function () {
    //     Route::get('/', 'NotificationController@get_notifications');
    // });

    // Route::group(['prefix' => 'brands'], function () {
    //     Route::get('/', 'BrandController@get_brands');
    //     Route::get('products/{brand_id}', 'BrandController@get_products');
    // });

    // Route::group(['prefix' => 'attributes'], function () {
    //     Route::get('/', 'AttributeController@get_attributes');
    // });

    // Route::group(['prefix' => 'flash-deals'], function () {
    //     Route::get('/', 'FlashDealController@get_flash_deal');
    //     Route::get('products/{deal_id}', 'FlashDealController@get_products');
    // });

    // Route::group(['prefix' => 'deals'], function () {
    //     Route::get('featured', 'DealController@get_featured_deal');
    // });

    // Route::group(['prefix' => 'dealsoftheday'], function () {
    //     Route::get('deal-of-the-day', 'DealOfTheDayController@get_deal_of_the_day_product');
    // });

    // Route::group(['prefix' => 'categories'], function () {
    //     Route::get('/', 'CategoryController@get_categories');
    //     Route::get('products/{category_id}', 'CategoryController@get_products');
    // });

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
