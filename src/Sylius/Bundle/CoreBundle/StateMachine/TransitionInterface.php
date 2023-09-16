<?php

namespace Sylius\Bundle\CoreBundle\StateMachine;

interface TransitionInterface
{
    public function getName(): string;

    public function getFroms(): ?array;

    public function getTos(): ?array;
}
