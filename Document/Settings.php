<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;
use ONGR\ElasticsearchBundle\Document\DocumentInterface;
use ONGR\ElasticsearchBundle\Document\DocumentTrait;

/**
 * Stores admin settings.
 *
 * @ES\Document(type="settings")
 */

class Settings implements DocumentInterface
{
    use DocumentTrait;

    /**
     * @var string
     *
     * @ES\Property(name="name", type="string", search_analyzer="standard")
     */
    public $name;

    /**
     * @var string
     *
     * @ES\Property(name="description", type="string", search_analyzer="standard")
     */
    public $description;

    /**
     * @var string
     *
     * @ES\Property(name="domain", type="string", search_analyzer="standard")
     */
    public $domain;

    /**
     * @var string
     *
     * @ES\Property(name="type", type="string", search_analyzer="standard")
     */
    public $type;

    /**
     * @var string
     *
     * @ES\Property(name="data", type="string", search_analyzer="standard")
     */
    public $data;

} 