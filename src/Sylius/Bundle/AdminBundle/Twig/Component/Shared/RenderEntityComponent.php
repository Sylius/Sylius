<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Twig\Component\Shared;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

class RenderEntityComponent
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public ?string $entity = null;

    public ?string $template = null;

    public mixed $identifier = null;

    #[ExposeInTemplate(name: 'entity')]
    public function getEntity()
    {
//        dd($this->identifier);
        return $this->entityManager->find($this->entity, $this->identifier);
    }
}
