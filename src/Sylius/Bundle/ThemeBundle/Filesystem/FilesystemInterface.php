<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\Filesystem;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface FilesystemInterface
{
    /**
     * Copies a file.
     *
     * This method only copies the file if the origin file is newer than the target file.
     *
     * By default, if the target already exists, it is not overridden.
     *
     * @param string $originFile The original filename
     * @param string $targetFile The target filename
     * @param bool   $override   Whether to override an existing file or not
     *
     * @throws FileNotFoundException When originFile doesn't exist
     * @throws IOException           When copy fails
     */
    public function copy($originFile, $targetFile, $override = false);

    /**
     * Creates a directory recursively.
     *
     * @param string|array|\Traversable $dirs The directory path
     * @param int                       $mode The directory mode
     *
     * @throws IOException On any directory creation failure
     */
    public function mkdir($dirs, $mode = 0777);

    /**
     * Checks the existence of files or directories.
     *
     * @param string|array|\Traversable $files A filename, an array of files, or a \Traversable instance to check
     *
     * @return bool true if the file exists, false otherwise
     */
    public function exists($files);

    /**
     * Sets access and modification time of file.
     *
     * @param string|array|\Traversable $files A filename, an array of files, or a \Traversable instance to create
     * @param int                       $time  The touch time as a Unix timestamp
     * @param int                       $atime The access time as a Unix timestamp
     *
     * @throws IOException When touch fails
     */
    public function touch($files, $time = null, $atime = null);

    /**
     * Removes files or directories.
     *
     * @param string|array|\Traversable $files A filename, an array of files, or a \Traversable instance to remove
     *
     * @throws IOException When removal fails
     */
    public function remove($files);

    /**
     * Change mode for an array of files or directories.
     *
     * @param string|array|\Traversable $files     A filename, an array of files, or a \Traversable instance to change mode
     * @param int                       $mode      The new mode (octal)
     * @param int                       $umask     The mode mask (octal)
     * @param bool                      $recursive Whether change the mod recursively or not
     *
     * @throws IOException When the change fail
     */
    public function chmod($files, $mode, $umask = 0000, $recursive = false);

    /**
     * Change the owner of an array of files or directories.
     *
     * @param string|array|\Traversable $files     A filename, an array of files, or a \Traversable instance to change owner
     * @param string                    $user      The new owner user name
     * @param bool                      $recursive Whether change the owner recursively or not
     *
     * @throws IOException When the change fail
     */
    public function chown($files, $user, $recursive = false);

    /**
     * Change the group of an array of files or directories.
     *
     * @param string|array|\Traversable $files     A filename, an array of files, or a \Traversable instance to change group
     * @param string                    $group     The group name
     * @param bool                      $recursive Whether change the group recursively or not
     *
     * @throws IOException When the change fail
     */
    public function chgrp($files, $group, $recursive = false);

    /**
     * Renames a file or a directory.
     *
     * @param string $origin    The origin filename or directory
     * @param string $target    The new filename or directory
     * @param bool   $overwrite Whether to overwrite the target if it already exists
     *
     * @throws IOException When target file or directory already exists
     * @throws IOException When origin cannot be renamed
     */
    public function rename($origin, $target, $overwrite = false);

    /**
     * Creates a symbolic link or copy a directory.
     *
     * @param string $originDir     The origin directory path
     * @param string $targetDir     The symbolic link name
     * @param bool   $copyOnWindows Whether to copy files if on Windows
     *
     * @throws IOException When symlink fails
     */
    public function symlink($originDir, $targetDir, $copyOnWindows = false);

    /**
     * Mirrors a directory to another.
     *
     * @param string       $originDir The origin directory
     * @param string       $targetDir The target directory
     * @param \Traversable $iterator  A Traversable instance
     * @param array        $options   An array of boolean options
     *                                Valid options are:
     *                                - $options['override'] Whether to override an existing file on copy or not (see copy())
     *                                - $options['copy_on_windows'] Whether to copy files instead of links on Windows (see symlink())
     *                                - $options['delete'] Whether to delete files that are not in the source directory (defaults to false)
     *
     * @throws IOException When file type is unknown
     */
    public function mirror($originDir, $targetDir, \Traversable $iterator = null, $options = []);

    /**
     * Given an existing path, convert it to a path relative to a given starting path.
     *
     * @param string $endPath   Absolute path of target
     * @param string $startPath Absolute path where traversal begins
     *
     * @return string Path of target relative to starting path
     */
    public function makePathRelative($endPath, $startPath);

    /**
     * Returns whether the file path is an absolute path.
     *
     * @param string $file A file path
     *
     * @return bool
     */
    public function isAbsolutePath($file);

    /**
     * @param string $file
     *
     * @return string
     */
    public function getFileContents($file);
}
