======================================
Enabling and using Cookie flash bag
======================================

Introduction
----------------

Flashbag is a service that registers front-end messages for the user. E.g.:

    > Your post have been successfully saved!

It is not possible to used `default Symfony flash bag <http://symfony.com/doc/current/components/http_foundation/sessions.html#flash-messages>`_ ], because ONGR does not support PHP sessions. Therefore, flash bag that stores messages in a cookie is needed.

Enabling
---------

    This functionality is enabled by default.

Usage
--------

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
            }

            return ['flash_bag' => $flashBag];
        }
    }

..

.. code-block:: twig

    {% for message in flash_bag.get('success') %}
        <div class="alert alert-success" role="alert">
            {{ message }}
        </div>
    {% endfor %}

..

More about
~~~~~~~
- `Personal functionality </Resources/doc/personal_settings.rst>`_
- `General functionality </Resources/doc/general_settings.rst>`_
- `Environment variables usage </Resources/doc/env_variable.rst>`_