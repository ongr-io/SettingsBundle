<?php

/*
 *************************************************************************
 * NFQ eXtremes CONFIDENTIAL
 * [2013] - [2014] NFQ eXtremes UAB
 * All Rights Reserved.
 *************************************************************************
 * NOTICE:
 * All information contained herein is, and remains the property of NFQ eXtremes UAB.
 * Dissemination of this information or reproduction of this material is strictly forbidden
 * unless prior written permission is obtained from NFQ eXtremes UAB.
 *************************************************************************
 */

namespace ONGR\AdminBundle\Model;

use Fox\DDALBundle\Core\BaseModel;

/**
 * This class provides data structure for app setting
 */
class SettingModel extends BaseModel implements \JsonSerializable
{
    /**
     * @const TYPE_STRING setting model of string type
     */
    const TYPE_STRING = 'string';

    /**
     * @const TYPE_ARRAY setting model of array type
     */
    const TYPE_ARRAY  = 'array';

    /**
     * @const TYPE_BOOLEAN setting model of boolean type
     */
    const TYPE_BOOLEAN  = 'bool';

    /**
     * @const TYPE_OBJECT setting model of object type
     */
    const TYPE_OBJECT = 'object';

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $domain;

    /**
     * @var string
     */
    public $type;

    /**
     * @var object
     */
    public $data;

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return array_filter(
            array_merge(['_id' => $this->getDocumentId()], $this->dump()),
            function ($value) {
                return isset($value);
            }
        );
    }
}
