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

namespace Fox\AdminBundle\Tests\Functional\Service\Filter;

use Fox\AdminBundle\Service\Filter\DomainFilter;

class DomainFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * tests filter type
     */
    public function testFilterType()
    {
        $filter = new DomainFilter('default');
        $this->assertEquals('domain', $filter->getFilterType());
    }
}
