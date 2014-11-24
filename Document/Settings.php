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
 * Stores admin settings key - value data.
 *
 * @ES\Document(type="settings")
 */

class Settings implements DocumentInterface
{
    use DocumentTrait;

    /**
     * @var string
     *
     * @ES\Property(name="key",  type="string", index="not_analyzed")
     */
    public $key;

    /**
     * @var string
     *
     * @ES\Property(name="value", type="string", search_analyzer="standard")
     */
    public $value;

} 