<?php

namespace devrkb21\PathaoLaravel\DataDTO;

use devrkb21\PathaoLaravel\Requests\PathaoStoreRequest;

class PathaoStoreDataDTO
{
    public function fromStoreRequest(PathaoStoreRequest $request)
    {
        return [
            'name' => $request['name'],
            'contact_name' => $request['contact_name'],
            'contact_number' => $request['contact_number'],
            'address' => $request['address'],
            'city_id' => $request['city_id'],
            'zone_id' => $request['zone_id'],
            'area_id' => $request['area_id'],
        ];
    }
}
