<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Crud;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Sylius\Behat\Page\SymfonyPage;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class IndexPage extends SymfonyPage implements IndexPageInterface
{
    /**
     * @var string
     */
    protected $resourceName;

    /**
     * @var array
     */
    protected  $elements = [
        'message' => '.message',
        'messageContent' => '.message > .content',
    ];

    /**
     * @param Session $session
     * @param array $parameters
     * @param RouterInterface $router
     * @param string $resourceName
     */
    public function __construct(Session $session, array $parameters, RouterInterface $router, $resourceName)
    {
        parent::__construct($session, $parameters, $router);

        $this->resourceName = $resourceName;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessfulMessage()
    {
        return $this->getElement('message')->hasClass('positive');
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessfullyCreated()
    {
        return $this->hasMessage(sprintf('Success %s has been successfully created.', ucfirst($this->resourceName)));
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessfullyUpdated()
    {
        return $this->hasMessage(sprintf('Success %s has been successfully updated.', ucfirst($this->resourceName)));
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessfullyDeleted()
    {
        return $this->hasMessage(sprintf('Success %s has been successfully deleted.', ucfirst($this->resourceName)));
    }

    /**
     * {@inheritdoc}
     */
    public function isResourceAppearInTheStoreBy(array $parameters)
    {
        foreach ($parameters as $key => $value)
        {
            if ($this->isTableContainHeader($key) && $this->isTableContainText($value))
            {
                return true;
            }

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasMessage($message)
    {
        if ($message === $this->getElement('messageContent')->getText()) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getRouteName()
    {
        return 'sylius_admin_' . strtolower($this->resourceName) . '_index';
    }

    /**
     * @return NodeElement[]
     */
    private function getAllTableRecords()
    {
        return $this->getDocument()->findAll('css', 'tbody > tr');
    }

    /**
     * @return NodeElement
     */
    private function getAllTableHeaders()
    {
        return $this->getDocument()->find('css', 'thead > tr');
    }

    /**
     * @param string $header
     *
     * @return bool
     */
    private function isTableContainHeader($header)
    {
        $tableHeaders = $this->getAllTableHeaders()->getText();

        if (false !== strpos($tableHeaders, ucfirst($header))) {
            return true;
        }

        return false;
    }

    /**
     * @param string $text
     *
     * @return bool
     */
    private function isTableContainText($text)
    {
        foreach ($this->getAllTableRecords() as $row)
        {
            $rowText = $row->getText();

            if (false !== strpos($rowText, $text)) {
                return true;
            }
        }

        return false;
    }
}
