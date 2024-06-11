<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $idField = $this->type === 'user' ? 'user_id' : 'device_id';
        return [
            'id' => $this->id,
            'type' => $this->type,
            $idField => $this->{$idField},
            'description' => $this->description,
            'vote' => $this->vote,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'role_of_creator' => $this->role_of_creator,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted' => $this->deleted,
        ];
    }
}
