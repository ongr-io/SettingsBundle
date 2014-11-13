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

namespace Fox\AdminBundle\Service\Filter;

use Fox\ProductBundle\Service\Filter\TermFilter;

class DomainFilter extends TermFilter
{
    /**
     * {@inheritdoc}
     */
    public function getFilterType()
    {
        return 'domain';
    }
}
