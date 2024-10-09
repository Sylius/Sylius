<?php

namespace Sylius\Bundle\UiBundle\Twig\Component;

use Symfony\UX\LiveComponent\Attribute\LiveProp;

trait TemplatePropTrait
{
    #[LiveProp]
    public ?string $template = null;
}
