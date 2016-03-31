<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Behat;

use Behat\Behat\Context\Context;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class DefaultContext extends RawMinkContext implements Context, KernelAwareContext
{
    /**
     * @var string
     */
    protected $applicationName = 'sylius';

    /**
     * @var Generator
     */
    protected $faker;

    /**
     * @var array
     */
    protected $actions = [
        'viewing' => 'show',
        'creation' => 'create',
        'editing' => 'update',
        'building' => 'build',
        'customization' => 'customize',
    ];

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var KernelInterface
     */
    private static $sharedKernel;

    public function __construct($applicationName = null)
    {
        \Locale::setDefault('en');

        $this->faker = FakerFactory::create();

        if (null !== $applicationName) {
            $this->applicationName = $applicationName;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        if (null === self::$sharedKernel) {
            self::$sharedKernel = clone $kernel;
            self::$sharedKernel->boot();
        }
    }

    /**
     * @param string $type
     * @param string $name
     *
     * @return object
     */
    protected function findOneByName($type, $name)
    {
        $resource = $this->getRepository($type)->findOneByName(trim($name));

        if (null === $resource) {
            throw new \InvalidArgumentException(
                sprintf('%s with name "%s" was not found.', str_replace('_', ' ', ucfirst($type)), $name)
            );
        }

        return $resource;
    }

    /**
     * @param string $type
     * @param array  $criteria
     *
     * @return object
     *
     * @throws \InvalidArgumentException
     */
    protected function findOneBy($type, array $criteria)
    {
        $resource = $this
            ->getRepository($type)
            ->findOneBy($criteria)
        ;

        if (null === $resource) {
            throw new \InvalidArgumentException(
                sprintf('%s for criteria "%s" was not found.', str_replace('_', ' ', ucfirst($type)), serialize($criteria))
            );
        }

        return $resource;
    }

    /**
     * @param string $resourceName
     *
     * @return RepositoryInterface
     */
    protected function getRepository($resourceName)
    {
        return $this->getService($this->applicationName.'.repository.'.$resourceName);
    }

    /**
     * @param string $resourceName
     *
     * @return FactoryInterface
     */
    protected function getFactory($resourceName)
    {
        return $this->getService($this->applicationName.'.factory.'.$resourceName);
    }

    /**
     * @return ObjectManager
     */
    protected function getEntityManager()
    {
        return $this->getService('doctrine')->getManager();
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->kernel->getContainer();
    }

    /**
     * @param string $id
     *
     * @return object
     */
    protected function getService($id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * Configuration converter.
     *
     * @param string $configurationString
     *
     * @return array
     */
    protected function getConfiguration($configurationString)
    {
        $configuration = [];
        $list = explode(',', $configurationString);

        foreach ($list as $parameter) {
            list($key, $value) = explode(':', $parameter, 2);
            $key = strtolower(trim(str_replace(' ', '_', $key)));

            switch ($key) {
                case 'country':
                    $countryCode = $this->getCountryCodeByEnglishCountryName(trim($value));

                    $configuration[$key] = $this->getRepository('country')->findOneBy(['code' => $countryCode])->getId();
                    break;

                case 'taxons':
                    $configuration[$key] = [$this->getRepository('taxon')->findOneByName(trim($value))->getCode()];
                    break;

                case 'variant':
                    $configuration[$key] = $this->getRepository('product')->findOneByName($value)->getMasterVariant()->getId();
                    break;

                case 'amount':
                    $configuration[$key] = (int) $value;
                    break;

                default:
                    $configuration[$key] = trim($value);
                    break;
            }
        }

        return $configuration;
    }

    /**
     * Generate page url.
     * This method uses simple convention where page argument is prefixed
     * with the application name and used as route name passed to router generate method.
     *
     * @param object|string $page
     * @param array         $parameters
     *
     * @return string
     */
    protected function generatePageUrl($page, array $parameters = [])
    {
        if (is_object($page)) {
            return $this->generateUrl($page, $parameters);
        }

        $route = str_replace(' ', '_', trim($page));
        $routes = $this->getRouter()->getRouteCollection();

        if (null === $routes->get($route)) {
            $route = $this->applicationName.'_'.$route;
        }

        if (null === $routes->get($route)) {
            $route = str_replace($this->applicationName.'_', $this->applicationName.'_backend_', $route);
        }

        $route = str_replace(array_keys($this->actions), array_values($this->actions), $route);
        $route = str_replace(' ', '_', $route);

        return $this->generateUrl($route, $parameters);
    }

    /**
     * Get current user instance.
     *
     * @return UserInterface|null
     *
     * @throws \Exception
     */
    protected function getUser()
    {
        $token = $this->getTokenStorage()->getToken();

        if (null === $token) {
            throw new \Exception('No token found in security context.');
        }

        return $token->getUser();
    }

    /**
     * @return TokenStorageInterface
     */
    protected function getTokenStorage()
    {
        return $this->getContainer()->get('security.token_storage');
    }

    /**
     * @return AuthorizationCheckerInterface
     */
    protected function getAuthorizationChecker()
    {
        return $this->getContainer()->get('security.authorization_checker');
    }

    /**
     * @param string  $route
     * @param array   $parameters
     * @param bool $absolute
     *
     * @return string
     */
    protected function generateUrl($route, array $parameters = [], $absolute = false)
    {
        return $this->locatePath($this->getRouter()->generate($route, $parameters, $absolute));
    }

    /**
     * Presses button with specified id|name|title|alt|value.
     *
     * @param string $button
     *
     * @throws ElementNotFoundException
     */
    protected function pressButton($button)
    {
        $this->getSession()->getPage()->pressButton($this->fixStepArgument($button));
    }

    /**
     * Clicks link with specified id|title|alt|text.
     *
     * @param string $link
     *
     * @throws ElementNotFoundException
     */
    protected function clickLink($link)
    {
        $this->getSession()->getPage()->clickLink($this->fixStepArgument($link));
    }

    /**
     * Fills in form field with specified id|name|label|value.
     *
     * @param string $field
     * @param string $value
     *
     * @throws ElementNotFoundException
     */
    protected function fillField($field, $value)
    {
        $this->getSession()->getPage()->fillField(
            $this->fixStepArgument($field),
            $this->fixStepArgument($value)
        );
    }

    /**
     * Selects option in select field with specified id|name|label|value.
     *
     * @param string $select
     * @param string $option
     *
     * @throws ElementNotFoundException
     */
    protected function selectOption($select, $option)
    {
        $this->getSession()->getPage()->selectFieldOption(
            $this->fixStepArgument($select),
            $this->fixStepArgument($option)
        );
    }

    /**
     * Returns fixed step argument (with \\" replaced back to ").
     *
     * @param string $argument
     *
     * @return string
     */
    protected function fixStepArgument($argument)
    {
        return str_replace('\\"', '"', $argument);
    }

    /**
     * @param NodeElement $table
     * @param string $columnName
     *
     * @return int
     *
     * @throws \Exception If column was not found
     */
    protected function getColumnIndex(NodeElement $table, $columnName)
    {
        $rows = $table->findAll('css', 'tr');

        if (!isset($rows[0])) {
            throw new \Exception('There are no rows!');
        }

        /** @var NodeElement $firstRow */
        $firstRow = $rows[0];
        $columns = $firstRow->findAll('css', 'th,td');
        foreach ($columns as $index => $column) {
            /** @var NodeElement $column */
            if (0 === stripos($column->getText(), $columnName)) {
                return $index;
            }
        }

        throw new \Exception(sprintf('Column with name "%s" not found!', $columnName));
    }

    /**
     * @param NodeElement $table
     * @param array $fields
     *
     * @return NodeElement|null
     *
     * @throws \Exception If column was not found
     */
    protected function getRowWithFields(NodeElement $table, array $fields)
    {
        $foundRows = $this->getRowsWithFields($table, $fields, true);

        if (empty($foundRows)) {
            return null;
        }

        return current($foundRows);
    }

    /**
     * @param NodeElement $table
     * @param array $fields
     * @param bool $onlyFirstOccurence
     *
     * @return NodeElement[]
     *
     * @throws \Exception If columns or rows were not found
     */
    protected function getRowsWithFields(NodeElement $table, array $fields, $onlyFirstOccurence = false)
    {
        $rows = $table->findAll('css', 'tr');

        if (!isset($rows[0])) {
            throw new \Exception('There are no rows!');
        }

        $fields = $this->replaceColumnNamesWithColumnIds($table, $fields);

        $foundRows = [];

        /** @var NodeElement[] $rows */
        $rows = $table->findAll('css', 'tr');
        foreach ($rows as $row) {
            $found = true;

            /** @var NodeElement[] $columns */
            $columns = $row->findAll('css', 'th,td');
            foreach ($fields as $index => $searchedValue) {
                if (!isset($columns[$index])) {
                    throw new \InvalidArgumentException(sprintf('There is no column with index %d', $index));
                }

                $containing = false;
                $searchedValue = trim($searchedValue);
                if (0 === strpos($searchedValue, '%') && (strlen($searchedValue) - 1) === strrpos($searchedValue, '%')) {
                    $searchedValue = substr($searchedValue, 1, strlen($searchedValue) - 2);
                    $containing = true;
                }

                $position = stripos(trim($columns[$index]->getText()), $searchedValue);
                if (($containing && false === $position) || (!$containing && 0 !== $position)) {
                    $found = false;

                    break;
                }
            }

            if ($found) {
                $foundRows[] = $row;

                if ($onlyFirstOccurence) {
                    break;
                }
            }
        }

        return $foundRows;
    }

    /**
     * @param NodeElement $table
     * @param string[] $fields
     *
     * @return string[]
     *
     * @throws \Exception
     */
    protected function replaceColumnNamesWithColumnIds(NodeElement $table, array $fields)
    {
        $replacedFields = [];
        foreach ($fields as $columnName => $expectedValue) {
            $columnIndex = $this->getColumnIndex($table, $columnName);

            $replacedFields[$columnIndex] = $expectedValue;
        }

        return $replacedFields;
    }

    /**
     * @param callable $callback
     * @param int $limit
     * @param int $delay In miliseconds
     *
     * @return mixed
     *
     * @throws \RuntimeException If timeout was reached
     */
    protected function waitFor(callable $callback, $limit = 30, $delay = 100)
    {
        for ($i = 0; $i < $limit; ++$i) {
            $payload = $callback();

            if (!empty($payload)) {
                return $payload;
            }

            usleep($delay * 1000);
        }

        throw new \RuntimeException(sprintf('Timeout reached (%f seconds)!', round($limit * $delay / 1000, 1)));
    }

    /**
     * @param string $name
     *
     * @return string
     *
     * @throws \InvalidArgumentException If name is not found in country code registry.
     */
    protected function getCountryCodeByEnglishCountryName($name)
    {
        $names = Intl::getRegionBundle()->getCountryNames('en');
        $countryCode = array_search(trim($name), $names);

        if (null === $countryCode) {
            throw new \InvalidArgumentException(sprintf(
                'Country "%s" not found! Available names: %s.', $name, implode(', ', $names)
            ));
        }

        return $countryCode;
    }

    /**
     * @param string $name
     *
     * @return string
     *
     * @throws \InvalidArgumentException If name is not found in locale code registry.
     */
    protected function getLocaleCodeByEnglishLocaleName($name)
    {
        $localeNameConverter = $this->getService('sylius.converter.locale_name');

        return $localeNameConverter->convertToCode($name);
    }

    /**
     * @return RouterInterface
     */
    protected function getRouter()
    {
        return $this->getSharedService('router');
    }

    /**
     * @return KernelInterface
     */
    protected function getKernel()
    {
        return $this->kernel;
    }

    /**
     * @return KernelInterface
     */
    protected function getSharedKernel()
    {
        return self::$sharedKernel;
    }

    /**
     * @param string $id
     *
     * @return object
     */
    protected function getSharedService($id)
    {
        return self::$sharedKernel->getContainer()->get($id);
    }
}
