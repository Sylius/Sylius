<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 30/01/18
 * Time: 13:01
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class ActiveRedirectRoutes
 *
 * Constraint for checking a active redirects
 *
 * @package Sylius\Bundle\CoreBundle\Validator\Constraints
 */
final class ActiveRedirectRoutes extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.url_redirects.multiple_active_routes';

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
        return 'sylius_url_active_route_validator';
    }
}