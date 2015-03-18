===========================================================
Enabling and using Sessionless authentication functionality
===========================================================

------------
Introduction
------------

ONGR Sessionless authentication is based on cookies.
As ONGR is created with systems using load balancers in mind, this bundle includes cookie based Sessionless authentication
and cookie based Flash bag.
Sessionless authentication can be configured to use symfony firewall and user providers, including provider chaining.

.. note::

    Sessionless authentication requires symfony 2.6


--------
Enabling
--------

Configure your ``security.yml`` as shown in example.

.. code-block:: yaml

    security:
        firewalls:
            sessionless_authentication_secured:
                pattern:   .*
                anonymous: ~
                ongr_sessionless_authentication: true
                form_login:
                    login_path: ongr_settings_sessionless_login #path where login form resides

                logout:
                    delete_cookies:
                        ongr_settings_user_auth: { path: null, domain: null } #will delete authentication cookie


        access_control:
            - { path: ^/settings_prefix/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/settings_prefix/logout, roles: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/settings_prefix/.* , roles: ROLE_ADMIN }

        providers:
            foo_chain_provider:
                chain:
                    providers: [in_memory, test_provider]
            in_memory:
                memory:
                    users:
                        admin: { password: admin, roles: 'ROLE_ADMIN' }
            test_provider:
                memory:
                    users:
                        test_user: { password: test_password, roles: 'ROLE_ADMIN' }

        encoders:
            Symfony\Component\Security\Core\User\User: plaintext

..

If you don't have any security settings, simply copy all security content.
Else, you need ``firewalls`` and  ``access_control``entries in your ``security.yml``, to be able to enable sessionless authentication.

You must set ``ongr_sessionless_authentication: true`` so symfony will be able to use ongr_sessionless_authentication.
All other settings (eg.: user providers) are configurable as described `in symfony documentation <http://symfony.com/doc/current/reference/configuration/security.html>`_.


.. important::

    Login form can be reached by ``/settings_prefix/login``. As told before ``settings_prefix``
    should be configured accordingly to your project needs. This can be done in your projects main ``routing.yml`` file,
    under ``ongr_settings_routing`` entry.

    Using these settings you can configure as many firewalls as you wish in your whole project.

Now you can try to login to your settings administration screen using http://your-project.url/settings_prefix/login

-----
Usage
-----

Login forms example:

.. code-block:: twig

    <div class="panel-body">
        {#<div id="login-alert" class="alert alert-danger col-sm-12"></div>#}
        {{ form_start(form) }}
        {{ form_errors(form, {'attr': {'class': 'alert alert-danger'} }) }}
        <div style="margin-bottom: 25px" class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            {{ form_errors(form.username) }}
            {{ form_widget(form.username, {'attr': {'class': 'form-control', 'placeholder': 'username'} }) }}
        </div>

        <div style="margin-bottom: 25px" class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
            {{ form_errors(form.password) }}
            {{ form_widget(form.password, {'attr': {'class': 'form-control', 'placeholder': 'password'} }) }}
        </div>

        <div class="form-group">
            {{ form_widget(form.submit, {'attr': {'class': 'btn btn-primary'} }) }}
        </div>
        {{ form_end(form) }}
    </div>

..

Controller login action example:

.. code-block:: php

    // ...

    public function loginAction(Request $request)
    {
        // Check if already logged in.
        $alreadyLoggedIn = $this->getSecurityContext()->getToken() instanceof SessionlessToken;

        // Handle form.
        $loginData = [];
        $form = $this->createForm(new LoginType(), $loginData);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $redirectResponse = $this->redirect($this->generateUrl('ongr_settings_sessionless_login'));
            $loginData = $form->getData();

            $username = $loginData['username'];
            $password = $loginData['password'];

            $ipAddress = $request->getClientIp();
            $cookieValue = $this->getAuthCookieService()->create($username, $password, $ipAddress);

            $cookie = $this->getAuthenticationCookie();
            $cookie->setValue($cookieValue);

            return $redirectResponse;
        }

        // Render.
        return $this->render(
            'ONGRSettingsBundle:User:login.html.twig',
            ['form' => $form->createView(), 'is_logged_in' => $alreadyLoggedIn]
        );
    }

    // ...

..


Authentication "session" is stored in cookies. Some of its' properties:

* Login credentials are stored in a signed tamper-proof authentication cookie that is **valid for X hours**.
* Authentication cookie's signature **contains username**, **IP address**, expiration **timestamp** and **password**. Therefore if any of the values change, the cookie becomes invalid.

Values can change in several places. Eg. IP address is dependent on the network, password can change in the configuration file and the expiration timestamp or the username can be modified in the cookie itself.

* Cookie **can be stolen** if sent over *http://*, so do not trust it's security absolutely.


~~~~~~~~~~
More about
~~~~~~~~~~

- `Personal settings usage </Resources/doc/personal_settings.rst>`_
- `General settings usage </Resources/doc/general_settings.rst>`_
- `Flash bag usage </Resources/doc/flash_bag.rst>`_
- `Environment variables usage </Resources/doc/env_variable.rst>`_
