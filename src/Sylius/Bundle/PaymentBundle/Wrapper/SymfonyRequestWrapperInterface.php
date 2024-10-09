<?php

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Wrapper;

use Symfony\Component\HttpFoundation\Request;

interface SymfonyRequestWrapperInterface {
    public function wrap(Request $request): array;
}
