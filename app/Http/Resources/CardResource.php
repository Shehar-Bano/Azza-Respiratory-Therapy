<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $imagesList = [];
        if ($this->images && $this->images->count() > 0) {
            foreach ($this->images as $img) {
                $imgPath = str_starts_with($img->image, 'uploads/') ? $img->image : 'uploads/cards/images/' . $img->image;
                $imagesList[] = [
                    'id' => $img->id,
                    'image' => asset($imgPath),
                ];
            }
        } elseif ($this->image) {
            $imgPath = str_starts_with($this->image, 'uploads/') ? $this->image : 'uploads/cards/images/' . $this->image;
            $imagesList[] = [
                'id' => 0,
                'image' => asset($imgPath),
            ];
        }

        $primaryImgPath = null;
        if ($this->image) {
            $imgPath = str_starts_with($this->image, 'uploads/') ? $this->image : 'uploads/cards/images/' . $this->image;
            $primaryImgPath = asset($imgPath);
        } elseif (count($imagesList) > 0) {
            $primaryImgPath = $imagesList[0]['image'];
        }

        $docPath = null;
        if ($this->document) {
            $imgPath = str_starts_with($this->document, 'uploads/') ? $this->document : 'uploads/cards/documents/' . $this->document;
            $docPath = asset($imgPath);
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image' => $primaryImgPath,
            'images' => $imagesList,
            'document' => $docPath,
            'created_at' => $this->created_at ? $this->created_at->toISOString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toISOString() : null,
        ];
    }
}
