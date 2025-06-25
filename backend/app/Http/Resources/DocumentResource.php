<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class documentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'file_path' => $this->file_path,
            'gemini_id' => $this->gemini_id,
            'upload_version' => $this->upload_version,
            'expires_at' => $this->expires_at,
            'description' => $this->description,
            'uri' => $this->uri,
            'tags' => $this->tags,
            'created_at' => $this->created_at,
        ];
    }
}
