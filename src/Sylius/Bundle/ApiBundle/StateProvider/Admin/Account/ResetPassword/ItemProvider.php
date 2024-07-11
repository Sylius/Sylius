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

namespace Sylius\Bundle\ApiBundle\StateProvider\Admin\Account\ResetPassword;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProviderInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\CoreBundle\Message\Admin\Account\ResetPassword;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Webmozart\Assert\Assert;

/** @implements ProviderInterface<ResetPassword> */
final readonly class ItemProvider implements ProviderInterface
{
    public function __construct(private SectionProviderInterface $sectionProvider)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ResetPassword
    {
        Assert::true(is_a($operation->getClass(), ResetPassword::class, true));
        Assert::isInstanceOf($operation, Patch::class);
        Assert::isInstanceOf($this->sectionProvider->getSection(), AdminApiSection::class);
        Assert::stringNotEmpty($uriVariables['token'] ?? null, 'Token is required.');

        return new ResetPassword($uriVariables['token']);
    }
}
