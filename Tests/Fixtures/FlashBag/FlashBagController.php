<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Fixtures\FlashBag;

use ONGR\AdminBundle\FlashBag\DirtyFlashBag;
use ONGR\AdminBundle\Utils\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for testing flash bag
 */
class FlashBagController
{
    use ContainerAwareTrait;

    /**
     * If POST request, sets flash. If GET, returns all flash messages.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request)
    {
        /** @var DirtyFlashBag $flashBag */
        $flashBag = $this->container->get('ongr_admin.flash_bag.flash_bag');

        if ($request->getMethod() == 'POST') {
            $flashBag->add('main', 'posted');

            return new JsonResponse();
        }

        $flashes = $flashBag->all();

        return new JsonResponse($flashes);
    }
}
