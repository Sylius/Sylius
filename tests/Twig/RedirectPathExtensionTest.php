<?php

declare(strict_types=1);

namespace Sylius\Tests\Twig;

use Sylius\Bundle\AdminBundle\Twig\RedirectPathExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class RedirectPathExtensionTest extends KernelTestCase
{
    private RedirectPathExtension $redirectPathExtension;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->redirectPathExtension = $this->getContainer()->get('Sylius\Bundle\AdminBundle\Twig\RedirectPathExtension');
        $this->getContainer()->get('Sylius\Bundle\AdminBundle\Storage\FilterStorage')->set(['criteria' => ['enabled' => true]]);
    }

    /** @test */
    public function it_returns_redirect_path_with_filters_from_storage_applied(): void
    {
        $redirectPath = $this->redirectPathExtension->generateRedirectPath('/admin/shipping-categories/');

        $this->assertSame('/admin/shipping-categories/?criteria%5Benabled%5D=1', $redirectPath);
    }

    /** @test */
    public function it_returns_given_path_if_route_has_some_more_configuration(): void
    {
        $redirectPath = $this->redirectPathExtension->generateRedirectPath('/admin/ajax/products/search');

        $this->assertSame('/admin/ajax/products/search', $redirectPath);
    }

    /** @test */
    public function it_returns_given_path_if_route_already_has_query_parameters(): void
    {
        $redirectPath = $this->redirectPathExtension->generateRedirectPath('/admin/shipping-categories/?foo=bar');

        $this->assertSame('/admin/shipping-categories/?foo=bar', $redirectPath);
    }

    /** @test */
    public function it_returns_given_path_if_route_is_not_matched(): void
    {
        $redirectPath = $this->redirectPathExtension->generateRedirectPath('/admin/invalid-path');

        $this->assertSame('/admin/invalid-path', $redirectPath);
    }

    /** @test */
    public function it_returns_null_if_the_path_is_null_as_well(): void
    {
        $redirectPath = $this->redirectPathExtension->generateRedirectPath(null);

        $this->assertNull($redirectPath);
    }
}
