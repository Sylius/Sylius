<?php

namespace Sylius\Bundle\UiBundle\Twig\Component;

use Symfony\UX\LiveComponent\Attribute\LiveProp;

trait TemplateLivePropTrait
{
    #[LiveProp]
    public ?string $template = null;
}
