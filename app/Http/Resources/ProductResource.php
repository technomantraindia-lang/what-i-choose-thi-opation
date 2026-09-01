<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $images = [];
        if ($this->main_image) {
            $images[] = asset('storage/' . $this->main_image);
        }
        if ($this->additional_images) {
            $addImgs = is_array($this->additional_images) ? $this->additional_images : json_decode($this->additional_images, true);
            if (is_array($addImgs)) {
                foreach ($addImgs as $img) {
                    $images[] = asset('storage/' . $img);
                }
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,
            'short_description' => $this->short_desc ?? $this->short_description ?? null,
            'price' => (float) $this->price,
            'sale_price' => $this->sale_price ? (float) $this->sale_price : null,
            'stock_status' => $this->available_stock > 0 ? 'in_stock' : 'out_of_stock',
            'available_stock' => (int) $this->available_stock,
            'featured' => (bool) ($this->featured ?? $this->is_featured ?? false),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'images' => array_values(array_unique($images)),
            'variations' => VariationResource::collection($this->whenLoaded('variations')),
        ];
    }
}
