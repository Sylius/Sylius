<?php

declare(strict_types=1);

namespace Sylius\Bundle\UserBundle\Form;

use Sylius\Bundle\UserBundle\Form\Type\UserType;
use Sylius\Component\User\Model\User;
use Symfony\Component\Form\DataTransformerInterface;

class UserVerificationTransformer implements DataTransformerInterface
{
    public function transform($data)
    {
        return (bool) $data;
    }

    public function reverseTransform($data)
    {
        if ($data){
            return new \DateTime();
        }
    }

}
