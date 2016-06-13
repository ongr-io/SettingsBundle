<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Document;

use JsonSerializable;
use ONGR\ElasticsearchBundle\Annotation as ES;

/**
 * Stores admin settings.
 *
 * @ES\Document(type="profile")
 */
class Profile implements JsonSerializable
{
    /**
     * @var string
     *
     * @ES\Id()
     */
    private $id;

    /**
     * @var string
     *
     * @ES\Property(type="string", options={"analyzer"="standard"})
     */
    private $key;

    /**
     * @var string
     *
     * @ES\Property(type="string", options={"analyzer"="standard"})
     */
    private $description;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'description' => $this->description,
        ];
    }
}
