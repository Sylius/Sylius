<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 30/01/18
 * Time: 13:01
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

class URLRedirectConstraint extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.url_redirect.redirect_loop';

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'sylius_url_redirect_validator';
    }
}