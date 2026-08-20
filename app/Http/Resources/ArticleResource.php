<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
            'title' => $this->title,
            'category_id' => $this->category_id ? (string) $this->category_id : null,
            'image' => $this->image,
            'images' => $this->images ? $this->images->map(function ($img) {
                return [
                    'id' => $img->id,
                    'image' => $img->image,
                ];
            }) : [],
            'document' => $this->document,
            'description' => $this->description,
            'created_at' => $this->created_at ? $this->created_at->toISOString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toISOString() : null,
            'category' => $this->whenLoaded('category', function () {
                return new CategoryResource($this->category);
            }, $this->category ? new CategoryResource($this->category) : null),
        ];
    }
}
