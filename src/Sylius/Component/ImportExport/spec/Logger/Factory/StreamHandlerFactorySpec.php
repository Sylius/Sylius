<?php

namespace spec\Sylius\Component\ImportExport\Logger\Factory;

use Sylius\Component\ImportExport\Logger\Model\StreamHandler;
use PhpSpec\ObjectBehavior;
use Sylius\Component\ImportExport\Provider\CurrentDateProviderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class StreamHandlerFactorySpec extends ObjectBehavior
{
    function let(CurrentDateProviderInterface $dateProvider)
    {
        $this->beConstructedWith('/tmp', $dateProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Logger\Factory\StreamHandlerFactory');
    }

    function it_implements_stream_handler_factory()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Logger\Factory\StreamHandlerFactoryInterface');
    }
}