<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
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
            'plan_id' => (string) $this->plan_id,
            'title' => $this->title,
            'price' => $this->price,
            'price_usd' => $this->price_usd,
            'price_sar' => $this->price_sar,
            'currency' => $this->currency,
            'currency_usd' => $this->currency_usd,
            'currency_sar' => $this->currency_sar,
            'duration_days' => $this->duration_days,
            'access' => $this->access,
            'features' => $this->features ?? [],
        ];
    }
}
