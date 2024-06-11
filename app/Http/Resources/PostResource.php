<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'staff_id'      => $this->staff_id,
            'title'         => $this->title,
            'content'       => $this->content,
            'image_url'     => $this->image_url,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'deleted'       => $this->deleted,
        ];
    }
}
