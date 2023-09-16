<?php

namespace Sylius\Bundle\CoreBundle\StateMachine;

interface TransitionInterface
{
    public function getName(): string;

    /**
     * @return array<string>|null
     */
    public function getFroms(): ?array;

    /**
     * @return array<string>|null
     */
    public function getTos(): ?array;
}
