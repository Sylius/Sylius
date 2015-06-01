<?php

namespace Sylius\Bundle\ThemeBundle\Locator;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class RecursiveFileLocator implements FileLocatorInterface
{
    /**
     * @var array
     */
    protected $paths;

    /**
     * @param string|array $paths A path or an array of paths where to look for resources
     */
    public function __construct($paths = [])
    {
        $this->paths = (array) $paths;
    }

    /**
     * {@inheritdoc}
     */
    public function locate($name, $currentPath = null, $first = true)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('An empty file name is not valid to be located.');
        }

        if ($this->isAbsolutePath($name)) {
            if (!file_exists($name)) {
                throw new \InvalidArgumentException(sprintf('The file "%s" does not exist.', $name));
            }

            return $name;
        }

        $directories = $this->paths;
        if (null !== $currentPath) {
            $directories[] = $currentPath;

            $directories = array_values(array_unique($directories));
        }

        $filepaths = [];

        $finder = new Finder();
        $finder
            ->files()
            ->name($name)
            ->ignoreUnreadableDirs()
            ->in($directories)
        ;

        /** @var SplFileInfo $file */
        if (true === $first) {
            if (null !== $file = $finder->getIterator()->current()) {
                return $file->getPathname();
            }
        } else {
            foreach ($finder as $file) {
                $filepaths[] = $file->getPathname();
            }
        }

        if (!$filepaths) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not exist (in: %s).', $name, implode(', ', $directories)));
        }

        return array_values(array_unique($filepaths));
    }

    /**
     * Returns whether the file path is an absolute path.
     *
     * @param string $file A file path
     *
     * @return bool
     */
    private function isAbsolutePath($file)
    {
        if ($file[0] === '/' || $file[0] === '\\'
            || (strlen($file) > 3 && ctype_alpha($file[0])
                && $file[1] === ':'
                && ($file[2] === '\\' || $file[2] === '/')
            )
            || null !== parse_url($file, PHP_URL_SCHEME)
        ) {
            return true;
        }

        return false;
    }
}