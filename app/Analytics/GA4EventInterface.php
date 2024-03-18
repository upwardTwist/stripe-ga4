<?php

namespace App\Analytics;

interface GA4EventInterface
{
    public function getPayload(): array;
}
