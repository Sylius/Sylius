<?php

namespace Sylius\Component\ImportExport\Logger\Factory;

use Monolog\Handler\StreamHandler;
use Sylius\Component\ImportExport\Provider\CurrentDateProviderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class StreamHandlerFactory implements StreamHandlerFactoryInterface
{
    /**
     * @var string
     */
    private $filePath;
    /**
     * @var CurrentDateProviderInterface
     */
    private $currentDateProvider;

    /**
     * StreamHandlerFactory constructor.
     *
     * @param string                       $filePath
     * @param CurrentDateProviderInterface $currentDateProvider
     */
    public function __construct($filePath, CurrentDateProviderInterface $currentDateProvider)
    {
        $this->filePath = $filePath;
        $this->currentDateProvider = $currentDateProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function create($profileName)
    {
        $streamHandler = new StreamHandler(sprintf(
            '%s/logs/%s_%s.log',
            $this->filePath,
            $profileName,
            $this->currentDateProvider->getCurrentDate()->format('Y_m_d_H_i_s')
        ));

        return $streamHandler;
    }
}
