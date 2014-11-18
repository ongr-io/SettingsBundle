<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\Service;

use DateTime;
use Exception;
use ONGR\AdminBundle\Service\FileLockService;
use PHPUnit_Framework_TestCase;

/**
 * Integration tests for FileLockService.
 */
class FileLockServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testContruct.
     *
     * @return array
     */
    public function provideTestConstructData()
    {
        return [
            [
                'new-file.LOCK',
            ],
            [
                'new-2-file.LOCK',
                'Y-m-d h:i:s',
            ],
        ];
    }

    /**
     * Construct test.
     *
     * @param string    $filename
     * @param null      $format
     *
     * @dataProvider provideTestConstructData
     */
    public function testConstruct($filename, $format = null)
    {
        $lock = new FileLockService('../../cache', FileLockService::MODE_DELETE, $format);
        $lock->setFilename($filename);

        $this->assertEquals('../../cache/FileLocks/' . $filename, $lock->getFilename());

        if (!is_null($format)) {
            $this->assertEquals($format, $lock->getDateFormat());
        }
    }

    /**
     * Compare age test.
     *
     * test for compareAge()
     */
    public function testCompareAge()
    {
        $lock = new FileLockService();
        $lock->setCacheDir(__DIR__ . '/../fixtures/');
        $lock->setFilename('existing-file.LOCK');

        $this->assertEquals(-1, $lock->compareAge(new DateTime('Monday, 15-Aug-05 15:52:00 UTC')));
        $this->assertEquals(0, $lock->compareAge(new DateTime('Monday, 15-Aug-05 15:52:01 UTC')));
        $this->assertEquals(1, $lock->compareAge(new DateTime('Monday, 15-Aug-05 15:52:02 UTC')));
    }

    /**
     * Data provider for testAcquire.
     *
     * @return array
     */
    public function provideTestAcquireData()
    {
        return [
            [
                'new-file.LOCK'
            ],
            [
                'existing-file.LOCK'
            ],
        ];
    }

    /**
     * Test acquire.
     *
     * @param string $filename
     *
     * @dataProvider provideTestAcquireData
     */
    public function testAcquire($filename)
    {
        $fixturesDir = __DIR__ . '/../fixtures/';

        $lock = new FileLockService($fixturesDir);
        $lock->setFilename($filename);
        $lock->acquire(false);

        $this->assertTrue(file_exists($lock->getFilename()));

        $lock2 = new FileLockService($fixturesDir);
        $lock2->setFilename($filename);
        $this->assertFalse($lock2->acquire(false));

        $lock->release();
        $this->assertFalse(file_exists($lock->getFilename()));

        $lock3 = new FileLockService($fixturesDir);
        $lock3->setFilename($filename);
        $lock3->acquire(false);
        $this->assertTrue(file_exists($lock->getFilename()));
        $lock3->release();
        $this->assertFalse(file_exists($lock->getFilename()));
    }

    /**
     * Data provider for testAcquireWait().
     *
     * @return array
     */
    public function provideTestAcquireWaitData()
    {
        return [
            [
                'new-file.LOCK',
            ],
        ];
    }

    /**
     * Test acquire wait.
     *
     * @param string $filename
     *
     * @dataProvider provideTestAcquireWaitData
     *
     * @throws Exception
     */
    public function testAcquireWait($filename)
    {
        $fixturesDir = __DIR__ . '/../fixtures/';

        $lock = new FileLockService($fixturesDir, FileLockService::MODE_TRUNCATE);
        $lock->setFilename($filename);
        $lock->acquire();

        $pid = pcntl_fork();
        if ($pid == -1) {
            throw new Exception('could not fork');
        }

        if ($pid) {
            // Parent thread.
            $start = time();
            $lock2 = new FileLockService($fixturesDir, FileLockService::MODE_TRUNCATE);
            $lock2->setFilename($filename);

            $lock2->acquire(true);
            $end = time();
            $this->assertEquals(1, $end - $start);
            $lock2->release();
            unlink($lock2->getFilename());
        } else {
            sleep(1);
            $lock->release();
            exit(0);
        }
    }

    /**
     * Invalid cache dir set, should thrown an exception.
     *
     * @expectedException \Symfony\Component\Filesystem\Exception\IOException
     * @expectedExceptionMessage An error occured while creating your directoryFailed to create
     */
    public function testMkDirFail()
    {
        $fixturesDir = '';

        $lock = new FileLockService($fixturesDir, FileLockService::MODE_TRUNCATE);
        $lock->compareAge(new DateTime());
    }

    /**
     * Invalid cache dir set, should thrown an exception.
     *
     * @expectedException \ONGR\AdminBundle\Exception\FileLockException
     * @expectedExceptionMessage Could not read lock file
     */
    public function testFileOpenFail()
    {
        $fixturesDir = __DIR__ . '/../fixtures/';

        $lock = new FileLockService($fixturesDir, FileLockService::MODE_TRUNCATE);
        $lock->setFilename('');
        $lock->compareAge(new DateTime());
    }

    /**
     * File lock should fail since fileHandle is invalid.
     */
    public function testFileLockFail()
    {
        /** @var FileLockService|\PHPUnit_Framework_MockObject_MockObject $lock */
        $lock = $this->getMockBuilder('ONGR\AdminBundle\Service\FileLockService')
            ->disableOriginalConstructor()
            ->setMethods(['initHandle'])
            ->getMock();

        $this->assertFalse($lock->acquire(false));
    }

    /**
     * Since we're trying to release a file which does not exist, exception should be thrown.
     *
     * @expectedException \ONGR\AdminBundle\Exception\FileLockException
     * @expectedExceptionMessage file does not exists
     */
    public function testReleaseNonExistentFile()
    {
        $fixturesDir = __DIR__ . '/../fixtures/';

        $lock = new FileLockService($fixturesDir, FileLockService::MODE_TRUNCATE);
        $lock->setFilename('ethereal');
        $lock->release();
    }

    /**
     * Handle was never initiated, an exception should be thrown.
     *
     * @expectedException \ONGR\AdminBundle\Exception\FileLockException
     * @expectedExceptionMessage flock() expects parameter 1 to be resource, null given
     */
    public function testReleaseInvalidHandle()
    {
        $fixturesDir = __DIR__ . '/../fixtures';
        $lock = new FileLockService($fixturesDir, FileLockService::MODE_TRUNCATE);
        $lock->setFilename('../existing-file.LOCK');
        $lock->release();
    }

    /**
     * Unable to delete a non existent file.
     *
     * @expectedException \ONGR\AdminBundle\Exception\FileLockException
     * @expectedExceptionMessage PHP unlink (delete) of lock file failed
     */
    public function testReleaseFailedUnlink()
    {
        /** @var FileLockService|\PHPUnit_Framework_MockObject_MockObject $lock */
        $lock = $this->getMockBuilder('ONGR\AdminBundle\Service\FileLockService')
            ->disableOriginalConstructor()
            ->setMethods(['initHandle', 'checkIfFileExists', 'unlock'])
            ->getMock();

        $lock->setFilename('/tmp/unknownFile.fileLockTest');
        $lock->acquire();
        $lock->setMode(FileLockService::MODE_DELETE);
        $lock->release();
    }

    /**
     * Check if date format setter and getter works as expected.
     */
    public function testSetDateFormat()
    {
        $dateFormat = 'F j, Y, g:i a';
        $lock = new FileLockService('', FileLockService::MODE_TRUNCATE);
        $this->assertEquals(DateTime::RFC850, $lock->getDateFormat());

        $lock->setDateFormat($dateFormat);
        $this->assertEquals($dateFormat, $lock->getDateFormat());
    }

    /**
     * Check if cache directory setter and getter works as expected.
     */
    public function testSetCacheDir()
    {
        $lock = new FileLockService('constructorCheck', FileLockService::MODE_TRUNCATE);
        $this->assertEquals('constructorCheck/' .  FileLockService::LOCK_FOLDER . '/', $lock->getCacheDir());

        $lock->setCacheDir('setterCheck');
        $this->assertEquals('setterCheck', $lock->getCacheDir());
    }
}
