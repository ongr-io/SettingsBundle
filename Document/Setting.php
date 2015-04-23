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
use ONGR\ElasticsearchBundle\Document\DocumentInterface;
use ONGR\ElasticsearchBundle\Document\DocumentTrait;

/**
 * Stores admin settings.
 *
 * @ES\Document(type="setting")
 */
class Setting implements DocumentInterface, JsonSerializable
{
    use DocumentTrait;

    /**
     * @const TYPE_STRING setting model of string type
     */
    const TYPE_STRING = 'string';

    /**
     * @const TYPE_ARRAY setting model of array type
     */
    const TYPE_ARRAY = 'array';

    /**
     * @const TYPE_BOOLEAN setting model of boolean type
     */
    const TYPE_BOOLEAN = 'bool';

    /**
     * @const TYPE_OBJECT setting model of object type
     */
    const TYPE_OBJECT = 'object';

    /**
     * @var string
     *
     * @ES\Property(name="name", type="string", searchAnalyzer="standard")
     */
    protected $name;

    /**
     * @var string
     *
     * @ES\Property(name="description", type="string", searchAnalyzer="standard")
     */
    protected $description;

    /**
     * @var string
     *
     * @ES\Property(name="profile", type="string", searchAnalyzer="standard")
     */
    protected $profile;

    /**
     * @var string
     *
     * @ES\Property(name="type", type="string", searchAnalyzer="standard")
     */
    protected $type;

    /**
     * @var string
     *
     * @ES\Property(name="data", type="string", searchAnalyzer="standard")
     */
    protected $data;

    /**
     * Get data.
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set data.
     *
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type.
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get profile.
     *
     * @return string
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set profile.
     *
     * @param string $profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return mixed Data which can be serialized by json_encode.
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'profile' => $this->getProfile(),
            'type' => $this->getType(),
            'data' => $this->getData(),
            'id' => $this->getId(),
            'score' => $this->getScore(),
            'parent' => $this->getParent(),
            'ttl' => $this->getTtl(),
            'highlight' => $this->highlight,
        ];
    }
}
