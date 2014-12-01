<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\FlashBag;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

/**
 * Implementation of flash-bag using cookies instead of session.
 */
class DirtyFlashBag extends FlashBag
{
    /**
     * Flag.
     *
     * Flag indicating if $messages array was changed or not.
     *
     * @var bool
     */
    protected $isDirty;

    /**
     * Check if container is dirty (has been modified).
     *
     * @return bool
     */
    public function isDirty()
    {
        return (bool)$this->isDirty;
    }

    /**
     * Mark container as dirty.
     *
     * @return $this
     */
    public function setDirty()
    {
        $this->isDirty = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $flashes
     */
    public function initialize(array &$flashes)
    {
        parent::initialize($flashes);

        $this->setDirty();
    }

    /**
     * {@inheritdoc}
     *
     * @param string $type
     * @param string $message
     */
    public function add($type, $message)
    {
        parent::add($type, $message);

        $this->setDirty();
    }

    /**
     * {@inheritdoc}
     *
     * @param string $type
     * @param array $default
     */
    public function get($type, array $default = [])
    {
        $markAsDirty = false;

        if ($this->has($type)) {
            $markAsDirty = true;
        }

        $result = parent::get($type, $default);

        if ($markAsDirty) {
            $this->setDirty();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $result = parent::all();

        $this->setDirty();

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $type
     * @param array $messages
     */
    public function set($type, $messages)
    {
        parent::set($type, $messages);

        $this->setDirty();
    }

    /**
     * {@inheritdoc}
     *
     * @param array $messages
     */
    public function setAll(array $messages)
    {
        parent::setAll($messages);

        $this->setDirty();
    }
}
