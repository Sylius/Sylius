<?php

declare(strict_types=1);

namespace Sylius\Bundle\UserBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;

final class UserVerifiedAtToBooleanTransformer implements DataTransformerInterface
{
    public function transform($data)
    {
        return (bool) $data;
    }

    public function reverseTransform($data)
    {
        return $data ? new \DateTime() : null;
    }
}
