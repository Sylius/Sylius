<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Checker\VariantInScopeCheckerInterface;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;

final class ForVariantInCatalogPromotionScopeCheckerProviderSpec extends ObjectBehavior
{
    public function let(
        VariantInScopeCheckerInterface $firstChecker,
        VariantInScopeCheckerInterface $secondChecker,
    ): void {
        $this->beConstructedWith([$firstChecker, $secondChecker]);
    }

    public function it_returns_checker_for_scope(
        CatalogPromotionScopeInterface $scope,
        VariantInScopeCheckerInterface $firstChecker,
    ): void {
        $firstChecker->supports($scope)->willReturn(true);

        $this->provide($scope)->shouldReturn($firstChecker);
    }

    public function it_throws_exception_if_there_is_no_checker_valid_for_scope(
        CatalogPromotionScopeInterface $scope,
        VariantInScopeCheckerInterface $firstChecker,
        VariantInScopeCheckerInterface $secondChecker,
    ): void
    {
        $firstChecker->supports($scope)->willReturn(false);
        $secondChecker->supports($scope)->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$scope]);
    }
}
