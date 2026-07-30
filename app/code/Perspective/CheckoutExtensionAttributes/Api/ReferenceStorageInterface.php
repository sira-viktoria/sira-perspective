<?php

declare(strict_types=1);

namespace Perspective\CheckoutExtensionAttributes\Api;

interface ReferenceStorageInterface
{
    public function save(
        int $quoteId,
        string $reference
    ): void;

    public function get(
        int $quoteId
    ): ?string;

}
