<?php

namespace App\Service\Intent;

use App\Contracts\Intent\AdvancedIntendDetector;
use App\Enums\Chat\BasicIntendEnum;
use App\Service\Intent\AdvancedIntent\AiBasedAdvancedIntendService;
use App\Service\Intent\AdvancedIntent\RuleBasedAdvancedIntendService;

class AdvancedIntentManager
{
    public function getIntentDetector(BasicIntendEnum $basicIntendEnum, string $message, bool $aiMode): AdvancedIntendDetector
    {
        return match ($aiMode) {
            true  => new AiBasedAdvancedIntendService,
            false => new RuleBasedAdvancedIntendService,
        };
    }
}
