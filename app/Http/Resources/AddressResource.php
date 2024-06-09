<?php

namespace App\Http\Resources;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'unit_number' => $this->unit_number,
            'street_number' => $this->street_number,
            'address_line' => $this->address_line,
            'ward' => $this->ward,
            'district' => $this->district,
            'city' => $this->city,
            'province' => $this->province,
            'country_name' => $this->country_name,
            'location' => new LocationResource(Location::findOrFail($this->id)),

        ];
    }
}
