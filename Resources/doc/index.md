Documentation
=============

- [Installation](#installation)

- [Usage](#usage)

Installation
------------

1. Download XabbuhPandaBundle using Composer

    Add ``xabbuh/panda-bundle`` as a dependency of your project:

    ```bash
    $ composer require xabbuh/panda-bundle "~1.0"
    ```

1. Enable the bundle

    Register the bundle in the kernel:

    ```php
    // app/AppKernel.php
    // ...

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Xabbuh\PandaBundle\XabbuhPandaBundle(),
        );

        return $bundles;
    }
    ```

1. Configure your clouds:

    XabbuhPandaBundle manages all clouds that you configure in the ``xabbuh_panda``
    section of your application configuration. The configuration for a cloud
    consists of its Panda id and a reference to an account. In account holds
    the authentication information needed to sign requests to the Panda REST
    API.

    Your configuration should look something like this:

    ```yaml
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

    ```yaml
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

Usage
-----

After having configured your clouds you can retrieve a
[CloudManager](https://github.com/xabbuh/panda-client/blob/master/src/Api/CloudManagerInterface.php)
is available from the service container via the ``xabbuh_panda.cloud_manager``
key. The manager can then be used to get access to one of your configured clouds.

For example, for the ``first-cloud`` from the configuration above this would
look like this:

```php
$cloudManager = $container->get('xabbuh_panda.cloud_manager');
$cloud = $cloudManager->getCloud('first-cloud');
```

Each cloud is also available as a service directly from the service container:

```php
$cloud = $container->get('xabbuh_panda.first_cloud_cloud');
```

Read the [Panda client documentation](https://github.com/xabbuh/panda-client/blob/master/doc/usage.md#api-methods)
to find out how to work with a so retrieved cloud.
