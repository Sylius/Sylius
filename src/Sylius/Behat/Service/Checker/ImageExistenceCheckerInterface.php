<?php

declare(strict_types=1);

namespace Sylius\Behat\Service\Checker;

interface ImageExistenceCheckerInterface
{
    public function doesImageWithUrlExist(string $imageUrl, string $liipImagineImagineFilter): bool;
}
