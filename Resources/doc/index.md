Installation
------------

1. Download XabbuhPandaBundle using Composer

    Add ``xabbuh/panda-bundle`` to the ``require`` section of your project's
    ``composer.json``:

    ``` json
    {
        "require": {
            "xabbuh/panda-bundle": "dev-master"
        }
    }
    ```

    Tell Composer to download the bundle:

        php composer.phar update xabbuh/panda-bundle

    After Composer has installed XabbuhPandaBundle you will find it in your
    project's ``vendor/xabbuh/panda-bundle`` directory.

2. Enable the bundle

    Register the bundle in the kernel:

    ``` php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Xabbuh\PandaBundle\XabbuhPandaBundle(),
        );

        return $bundles;
    }
    ```

3. Configure your clouds:

    XabbuhPandaBundle manages all clouds which are provided by so called
    cloud providers. For each cloud you need to configure an account which
    provides the API credentials. The bundle ships with providers for both
    clouds and accounts. These providers parse the ``xabbuh_panda`` section
    of your application's configuration. Your configuration should look
    something like this:

    ``` yaml
    xabbuh_panda:
        accounts:
            default:
                access_key: your-access-key
                secret_key: your-secret-key
                api_host: api.pandastream.com
        clouds:
            default:
                id: your-first-cloud-id
            second-cloud:
                id: your-second-cloud-id
    ```

    **Note:** Use api-eu.pandastream.com if your Panda account is in the EU.

    If your clouds belong to different accounts use the ``account`` key in
    your cloud configuration. You can change the default account with the
    ``default_account`` key. The ``default_cloud`` works the same with your
    cloud configuration:

    ``` yaml
    xabbuh_panda:
        default_account: first-account
        accounts:
            first-account:
                access_key: your-first-account-access-key
                secret_key: your-first-account-secret-key
                api_host: api.pandastream.com
            second-account:
                access_key: your-second-account-access-key
                secret_key: your-second-account-secret-key
                api_host: api-eu.pandastream.com
        clouds:
            first-cloud:
                id: your-first-cloud-id
                account: first-account
            second-cloud:
                id: your-second-cloud-id
                account: second-account
             third-cloud:
                id: your-third-cloud-id
    ```

    Note that the `third-cloud` cloud is managed by the `first-account`
    account as there is no account specified for it in the configuration
    and `first-account` is the default account.
