<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanySettings extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'timezone' => $this['timezone'] ?? null,
            'language' => $this['language'] ?? null,
            'work_time' => $this['work_time'] ?? 0,
            'color' => $this['color'] ?? [],
            'internal_priorities' => $this['internal_priorities'] ?? [],
            'heartbeat_period' => config('app.user_activity.online_status_time'),
            'auto_thinning' => (bool) ($this['auto_thinning'] ?? false),
        ];
    }
}
