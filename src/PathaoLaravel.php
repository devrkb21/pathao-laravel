<?php

namespace devrkb21\PathaoLaravel;

use devrkb21\PathaoLaravel\APIBase\PathaoArea;
use devrkb21\PathaoLaravel\APIBase\PathaoAuth;
use devrkb21\PathaoLaravel\APIBase\PathaoMerchant;
use devrkb21\PathaoLaravel\APIBase\PathaoOrder;
use devrkb21\PathaoLaravel\APIBase\PathaoStore;
use devrkb21\PathaoLaravel\APIBase\PathaoUserSuccess;
use devrkb21\PathaoLaravel\DataDTO\PathaoOrderDTO;
use devrkb21\PathaoLaravel\DataDTO\PathaoStoreDataDTO;
use devrkb21\PathaoLaravel\Requests\PathaoOrderPriceCalculationRequest;
use devrkb21\PathaoLaravel\Requests\PathaoOrderRequest;
use devrkb21\PathaoLaravel\Requests\PathaoStoreRequest;
use devrkb21\PathaoLaravel\Requests\PathaoUserSuccessRateRequest;
use devrkb21\PathaoLaravel\Services\StandardResponseService;

class PathaoLaravel
{
    /**
     * Usage: PathaoLaravel::GET_ACCESS_TOKEN_EXPIRY_DAYS_LEFT()
     *
     * This will return the remaining days left for access token
     * And also return the expected last date of the access token expiration
     *
     * @return array
     */
    public function GET_ACCESS_TOKEN_EXPIRY_DAYS_LEFT()
    {
        return StandardResponseService::RESPONSE_OUTPUT((new PathaoAuth)->getAccessTokenExpiryDaysLeft());
    }

    /**
     * Usage: PathaoLaravel::GET_CITIES()
     *
     * This will return the city list
     *
     * @return array
     */
    public function GET_CITIES()
    {
        return StandardResponseService::RESPONSE_OUTPUT((new PathaoArea)->get_cities());
    }

    /**
     * Usage: PathaoLaravel::GET_ZONES($city_id)
     *
     * This will return the zone list under a city
     *
     * @return array
     */
    public function GET_ZONES(int $city_id)
    {
        return StandardResponseService::RESPONSE_OUTPUT((new PathaoArea)->get_zones($city_id));
    }

    /**
     * Usage: PathaoLaravel::GET_AREAS($zone_id)
     *
     * This will return the area list under a zone
     *
     * @return array
     */
    public function GET_AREAS(int $zone_id)
    {
        return StandardResponseService::RESPONSE_OUTPUT((new PathaoArea)->get_areas($zone_id));
    }

    /**
     * Usage: PathaoLaravel::GET_STORES()
     *
     * This will return the store list
     *
     * @return array
     */
    public function GET_STORES(int $page = 1)
    {
        return StandardResponseService::RESPONSE_OUTPUT((new PathaoStore)->get_stores($page));
    }

    /**
     * Usage: PathaoLaravel::CREATE_STORE($request)
     *
     * This will create a store in Pathao courier merchant
     *
     * @param  PathaoStoreRequest  $request
     *
     * Request parameters are below and will follow a validation
     * @param  $name  <required, string>
     * @param  $contact_name  <required, string>
     * @param  $contact_number  <required, numeric>
     * @param  $address  <required, string>
     * @param  $city_id  <required, numeric>
     * @param  $zone_id  <required, numeric>
     * @param  $area_id  <required, numeric>
     * @return array
     */
    public function CREATE_STORE(PathaoStoreRequest $request)
    {
        $pathaoOrderDto = ((new PathaoStoreDataDTO)->fromStoreRequest($request));

        return StandardResponseService::RESPONSE_OUTPUT((new PathaoStore)->create_store($pathaoOrderDto));
    }

    /**
     * Usage: PathaoLaravel::VIEW_ORDER($consignment_id)
     *
     * This will fetch the details of a order
     *
     * @return array
     */
    public function VIEW_ORDER(string $consignment_id)
    {
        return StandardResponseService::RESPONSE_OUTPUT((new PathaoOrder)->view_order($consignment_id));
    }

    /**
     * Usage: PathaoLaravel::CREATE_ORDER($request)
     *
     * This will create a order in Pathao courier merchant
     *
     * @param  PathaoOrderRequest  $request
     *
     * Request parameters are below and will follow a validation
     * @param  $store_id  <required, numeric>
     * @param  $merchant_order_id  <nullable, string>
     * @param  $sender_name  <required, numeric>
     * @param  $sender_phone  <required, string/>
     * @param  $recipient_name  <required, string>
     * @param  $recipient_phone  <required, string>
     * @param  $recipient_address  <required, string, Min:10>
     * @param  $recipient_city  <required, numeric>
     * @param  $recipient_zone  <required, numeric>
     * @param  $recipient_area  <required, numeric>
     * @param  $delivery_type  <required, numeric> is provided by the merchant and not changeable. 48 for Normal Delivery, 12 for On Demand Delivery"
     * @param  $item_type  <required, numeric> is provided by the merchant and not changeable. 1 for Document, 2 for Parcel"
     * @param  $special_instruction  <nullable, string>
     * @param  $item_quantity  <required, numeric>
     * @param  $item_weight  <required, numeric>
     * @param  $amount_to_collect  <required, numeric>
     * @param  $item_description  <nullable, string>
     * @return array
     */
    public function CREATE_ORDER(PathaoOrderRequest $request)
    {
        $pathaoOrderDto = ((new PathaoOrderDTO)->fromOrderRequest($request));

        return StandardResponseService::RESPONSE_OUTPUT((new PathaoOrder)->create_order($pathaoOrderDto));
    }

    /**
     * Usage: PathaoLaravel::GET_PRICE_CALCULATION($request)
     *
     * This will return the calculated price for a order
     *
     * @param  PathaoOrderPriceCalculationRequest  $request
     *
     * Request parameters are below and will follow a validation
     * @param  $delivery_type  <required, numeric>
     * @param  $item_type  <required, numeric>
     * @param  $item_weight  <required, numeric>
     * @param  $recipient_city  <required, numeric>
     * @param  $recipient_zone  <required, numeric>
     * @param  $store_id  <required, numeric>
     * @return array
     */
    public function GET_PRICE_CALCULATION(PathaoOrderPriceCalculationRequest $request)
    {
        $priceCalculationDTO = (new PathaoOrderDTO)->fromPriceCalculationRequest($request);

        return StandardResponseService::RESPONSE_OUTPUT((new PathaoOrder)->price_calculation($priceCalculationDTO));
    }

    /**
     * Usage: PathaoLaravel::GET_USER_SUCCESS_RATE($request)
     *
     * This will return the users success rate using a phone number
     *
     * @param  PathaoUserSuccessRateRequest  $request
     *
     * Request parameters are below and will follow a validation
     * @param  $phone  <required, numeric>
     * @return array
     */
    public function GET_USER_SUCCESS_RATE(PathaoUserSuccessRateRequest $request)
    {
        $data = (new PathaoOrderDTO)->fromUserSuccessRate($request);

        return StandardResponseService::RESPONSE_OUTPUT((new PathaoUserSuccess)->get_user_success_rate($data));
    }

    /**
     * Usage: PathaoLaravel::CREATE_ORDERS_BULK($orders)
     *
     * This will create multiple orders in bulk
     *
     * @return array
     */
    public function CREATE_ORDERS_BULK(array $orders)
    {
        return StandardResponseService::RESPONSE_OUTPUT((new PathaoOrder)->create_orders_bulk($orders));
    }

    /**
     * Usage: PathaoLaravel::GET_ZONES_BULK()
     *
     * This will return all zones in bulk
     *
     * @return array
     */
    public function GET_ZONES_BULK()
    {
        return StandardResponseService::RESPONSE_OUTPUT((new PathaoArea)->get_zones_bulk());
    }

    /**
     * Usage: PathaoLaravel::GET_AREAS_BULK()
     *
     * This will return all areas in bulk
     *
     * @return array
     */
    public function GET_AREAS_BULK()
    {
        return StandardResponseService::RESPONSE_OUTPUT((new PathaoArea)->get_areas_bulk());
    }

    /**
     * Usage: PathaoLaravel::GET_MERCHANT_INFO()
     *
     * This will return the merchant user short profile info
     *
     * @return array
     */
    public function GET_MERCHANT_INFO()
    {
        return StandardResponseService::RESPONSE_OUTPUT((new PathaoMerchant)->get_merchant_info());
    }
}
