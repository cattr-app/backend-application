<?php

namespace App\Http\Transformers;

use Flugg\Responder\Transformers\Transformer;

class CompanySettingsTransformer extends Transformer
{
    public function transform(array $input): array
    {
        return [
            'timezone' => $input['timezone'] ?? null,
            'language' => $input['language'] ?? null,
            'work_time' => $input['work_time'] ?? 0,
            'color' => $input['color'] ?? [],
            'internal_priorities' => $input['internal_priorities'] ?? [],
            'heartbeat_period' => config('app.user_activity.online_status_time'),
            'auto_thinning' => (bool) ($input['auto_thinning'] ?? false),
            'default_priority_id' => (int) ($input['default_priority_id'] ?? 2),
        ];
    }
}