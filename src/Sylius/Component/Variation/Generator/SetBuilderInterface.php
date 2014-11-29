<?php

namespace Sylius\Component\Variation\Generator;

interface SetBuilderInterface
{
    /**
     * @param array   $setTuples
     * @param boolean $isRecursiveStep
     *
     * @return array
     */
    public function build(array $setTuples, $isRecursiveStep = false);
}
