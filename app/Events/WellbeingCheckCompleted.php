<?php

namespace App\Events;

use App\Models\WellbeingCheck;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WellbeingCheckCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly WellbeingCheck $check,
        public readonly array $summary,
    ) {}
}
