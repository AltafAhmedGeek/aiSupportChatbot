<?php

namespace App\Contracts\Intent;

use App\Enums\Chat\BasicIntendEnum;

interface AdvancedIntendDetector
{
    public function detectAdvancedintend(BasicIntendEnum $basicIntendEnum, string $message): ?string;
}
