<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\UtilsBundle\Service;

use DateTime;
use Exception;
use ONGR\AdminBundle\Exception\FileLockException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Class FileLockService.
 *
 * @package ONGR\UtilsBundle\Service
 */
class FileLockService
{
    /** string Deletion mode for file */
    const MODE_DELETE = 'DELETE';

    /** Truncate mode for file  */
    const MODE_TRUNCATE = 'TRUNCATE';

    /** Lock folder path */
    const LOCK_FOLDER = 'FileLocks';

    /**
     * @var string filename
     */
    protected $filename;

    /**
     * @var string cacheDir Cache directory.
     */
    protected $cacheDir;

    /**
     * @var string mode
     */
    protected $mode;

    /**
     * @var string date format
     */
    protected $dateFormat;

    /**
     * @var resource
     */
    protected $fileHandle;

    /**
     * @param string $cacheDir
     * @param string $mode
     * @param string $dateFormat
     */
    public function __construct($cacheDir = './', $mode = self::MODE_DELETE, $dateFormat = DateTime::RFC850)
    {
        $this->cacheDir = $cacheDir . '/' . self::LOCK_FOLDER . '/';
        $this->mode = $mode;
        $this->dateFormat = $dateFormat;
    }

    /**
     * Create a directory and open up the file.
     *
     * @throws IOException
     * @throws FileLockException
     */
    protected function initHandle()
    {
        $fs = new Filesystem();
        try {
            $fs->mkdir(dirname($this->filename));
        } catch (IOException $e) {
            throw new IOException('An error occured while creating your directory' . $e->getMessage());
        }

        $mode = (file_exists($this->filename)) ? 'r+' : 'x+';
        $this->fileHandle = @fopen($this->filename, $mode);
        if (!$this->fileHandle) {
            throw new FileLockException(sprintf("Could not read lock file '%s'", $this->filename));
        }
    }

    /**
     * Acquires a lock on file.
     *
     * @param bool $wait
     *
     * @return bool
     */
    public function acquire($wait = true)
    {
        // In delete mode, file indicates lock is already acquired.
        if (!$wait && self::MODE_DELETE === $this->mode && file_exists($this->filename)) {
            return false;
        }

        $this->initHandle();

        /*
         * LOCK_BN is a bitmask to change behavior of flock
         * see: http://php.net/manual/en/function.flock.php
         */

        $operation = ($wait) ? LOCK_EX : LOCK_EX | LOCK_NB;
        if (!@flock($this->fileHandle, $operation)) {
            return false;
        }

        ftruncate($this->fileHandle, 0);
        $time = new DateTime();
        fwrite($this->fileHandle, $time->format($this->dateFormat));
        fflush($this->fileHandle);

        return true;
    }

    /**
     * Releases a file lock.
     *
     * @throws FileLockException
     */
    public function release()
    {
        $this->checkIfFileExists();
        $this->unlock();

        if (self::MODE_DELETE === $this->mode) {
            $this->delete();
        } else {
            ftruncate($this->fileHandle, 0);
        }
        fclose($this->fileHandle);
    }

    /**
     * Checks if files exists.
     *
     * @throws FileLockException
     */
    protected function checkIfFileExists()
    {
        if (!file_exists($this->filename)) {
            throw new FileLockException(
                sprintf("Could not release lock for file '%s': file does not exists", $this->filename)
            );
        }
    }

    /**
     * Try to unlock a file.
     *
     * @throws FileLockException
     */
    protected function unlock()
    {
        try {
            flock($this->fileHandle, LOCK_UN);
        } catch (Exception $e) {
            throw new FileLockException(
                sprintf("Could not release lock for file '%s': " . $e->getMessage(), $this->filename)
            );
        }
    }

    /**
     * Try to delete a file.
     *
     * @throws FileLockException
     */
    protected function delete()
    {
        if (!@unlink($this->filename)) {
            throw new FileLockException(
                sprintf(
                    "Could not release lock for file '%s': PHP unlink (delete) of lock file failed",
                    $this->filename
                )
            );
        }
    }

    /**
     * Compares file time to the given time.
     *
     * @param DateTime $compareToDateTime
     *
     * @return int returns 1 if greater, 0 if equal, -1 if less
     */
    public function compareAge(DateTime $compareToDateTime)
    {
        if (!$this->fileHandle) {
            $this->initHandle();
        }

        $compareToTimestamp = $compareToDateTime->getTimestamp();
        rewind($this->fileHandle);
        $lockFileDateTime = new DateTime(fread($this->fileHandle, filesize($this->filename) + 1));
        $lockFileTimestamp = $lockFileDateTime->getTimestamp();

        // Less thanve.
        if ($compareToTimestamp < $lockFileTimestamp) {
            return -1;
        }

        // Equal.
        if ($compareToTimestamp === $lockFileTimestamp) {
            return 0;
        }

        // Greater than $compareToTimestamp > $lockFileTimestamp.
        return 1;
    }

    /**
     * Sets filename.
     *
     * @param string $filename
     *
     * @return FileLockService $this
     */
    public function setFilename($filename)
    {
        $this->filename = $this->cacheDir . $filename;

        return $this;
    }

    /**
     * Returns filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Sets date format.
     *
     * @param string $dateFormat
     *
     * @return FileLockService $this
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    /**
     * Returns date format.
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * Sets mode.
     *
     * @param string $mode
     *
     * @return FileLockService $this
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Gets mode.
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Sets cache dir.
     *
     * @param string $cacheDir
     *
     * @return FileLockService $this
     */
    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;

        return $this;
    }

    /**
     * Gets cache dir.
     *
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }
}
