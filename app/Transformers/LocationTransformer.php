<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Location;

class LocationTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Location $location)
    {
        return [
            'id' => $location->id,
            'user_id' => $location->user_id,
            'postcode' => $location->postcode
        ];
    }
}