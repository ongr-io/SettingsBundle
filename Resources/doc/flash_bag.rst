================
Cookie flash bag
================

Introduction
------------

Flashbag is a service that registers front-end messages for the user. E.g.:

    > Your post have been successfully saved!

This functionality comes in hand when it is not possible to use
`default Symfony flash bag <http://symfony.com/doc/current/components/http_foundation/sessions.html#flash-messages>`_ ,
because your production environment can't support PHP sessions. Therefore, flash bag that stores messages in a cookie is needed.

Enabling
--------

    This functionality is enabled by default, after SettingsBundle is enabled.

Usage
-----

ONGR flash_bag service can be accessed and used like this:

.. code-block:: php

    class FlashBagController
    {
        use ContainerAwareTrait;

        public function indexAction()
        {
            /** @var FlashBagInterface $flashBag */
            $flashBag = $this->container->get('ongr_settings.flash_bag.flash_bag');

            if ($request->getMethod() == 'POST') {
                $flashBag->add(
                    'success',
                    'Your post have been successfully saved!'
                );

                return new JsonResponse();
            }

            $flashes = $flashBag->all();

            return new JsonResponse($flashes);
        }
    }

..

Example above returns empty JsonResponse if POST method is used and response with all saved messages otherwise.

More about
~~~~~~~~~~

- `Sessionless authentication usage <ongr_sessionless_authentication.rst>`_
- `Personal settings usage <personal_settings.rst>`_
- `General settings usage <general_settings.rst>`_
- `Environment variables usage <env_variable.rst>`_
