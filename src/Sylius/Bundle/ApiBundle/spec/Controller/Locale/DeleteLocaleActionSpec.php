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

namespace spec\Sylius\Bundle\ApiBundle\Controller\Locale;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\LocaleBundle\Remover\LocaleRemoverInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class DeleteLocaleActionSpec extends ObjectBehavior
{
    function let(LocaleRemoverInterface $localeRemover): void
    {
        $this->beConstructedWith($localeRemover);
    }

    function it_removes_a_locale(
        LocaleRemoverInterface $localeRemover
    ): void {
        $localeRemover->removeByCode('en_US')->shouldBeCalled();

        $this('en_US')->shouldBeLike(new JsonResponse(null, Response::HTTP_NO_CONTENT));
    }
}
