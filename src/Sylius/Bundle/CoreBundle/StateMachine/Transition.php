<?php

namespace Sylius\Bundle\CoreBundle\StateMachine;

final class Transition implements TransitionInterface
{
    public function __construct (
        private string $name,
        private ?array $froms,
        private ?array $tos,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFroms(): ?array
    {
        return $this->froms;
    }

    public function getTos(): ?array
    {
        return $this->tos;
    }
}
