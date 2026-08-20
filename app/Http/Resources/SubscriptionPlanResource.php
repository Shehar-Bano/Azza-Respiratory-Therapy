<?php

namespace App\Http\Resources;

use App\Models\SubscriptionFeature;
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
        $featureIds = is_array($this->feature_ids) ? array_map('intval', $this->feature_ids) : [];

        $featureObjects = collect();
        if (! empty($featureIds)) {
            $featureObjects = SubscriptionFeature::whereIn('id', $featureIds)->get();
        }

        $featureTitles = $featureObjects->pluck('title')->toArray();

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
            'features' => $featureTitles,
        ];
    }
}
