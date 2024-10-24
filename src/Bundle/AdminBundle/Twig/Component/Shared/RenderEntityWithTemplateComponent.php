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

namespace Sylius\Bundle\AdminBundle\Twig\Component\Shared;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

class RenderEntityWithTemplateComponent
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public ?string $entity = null;

    public ?string $template = null;

    public mixed $identifier = null;

    #[ExposeInTemplate(name: 'entity')]
    public function getEntity(): object
    {
        /** @phpstan-ignore-next-line */
        return $this->entityManager->find($this->entity, $this->identifier);
    }
}
