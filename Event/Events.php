<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Event;

final class Events
{
    const PRE_CREATE = 'ongr.settings.pre_create';
    const POST_CREATE = 'ongr.settings.post_create';
    const PRE_GET = 'ongr.settings.pre_get';
    const POST_GET = 'ongr.settings.post_get';
    const PRE_UPDATE = 'ongr.settings.pre_update';
    const POST_UPDATE = 'ongr.settings.post_update';
    const PRE_DELETE = 'ongr.settings.pre_delete';
    const POST_DELETE = 'ongr.settings.post_delete';
}
