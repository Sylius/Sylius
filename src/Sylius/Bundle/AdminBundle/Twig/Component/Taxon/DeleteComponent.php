<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Twig\Component\Taxon;

use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
final class DeleteComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use ComponentToolsTrait;

    public const OPEN_DELETE_MODAL_EVENT = 'sylius_admin:taxon:open_delete_modal';

    #[ExposeInTemplate(name: 'taxon_id')]
    public string $taxonId = '';

    public function __construct(
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    #[LiveAction]
    public function delete(#[LiveArg] string $taxonId): void
    {
        $this->taxonId = $taxonId;
        $this->dispatchBrowserEvent(
            self::OPEN_DELETE_MODAL_EVENT,
            ['csrfToken' => $this->csrfTokenManager->getToken($taxonId)->getValue()],
        );
    }
}
