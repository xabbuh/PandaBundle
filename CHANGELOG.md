CHANGELOG
=========

2.0.0
-----

* removed `CloudFactoryInterface` and `CloudFactory`
* dropped support for Symfony 3.4

1.5.0
-----

* not injecting the required dependencies through the constructor of the
  command classes is deprecated
* the `setContainer()` and `getContainer()` methods of the command classes
  are deprecated
* marked all command and event classes as `final`, extending them will not
  be supported as of 2.0
* added support for Symfony 5 components
* dropped support for PHP 5 and 7.0
* dropped support for unmaintained Symfony versions

1.4.1
-----

* register commands as services for compatibility with recent Symfony
  versions

1.4.0
-----

* made the bundle compatible with Symfony 4
* explicitly marked the `xabbuh_panda.controller` service as public
* deprecated the `cloud_factory` service
* added autowiring support by adding aliases for `CloudInterface` (if at
  least one cloud is configured) and `CloudManagerInterface`

1.3.0
-----

* make use of HTTPlug to perform HTTP requests
* made the bundle compatible with Symfony 3.x and PHP 7.1
* dropped support for Symfony versions before 2.8

1.2.1
-----

The `VideoUploaderExtension` is now properly registered when using Symfony
3.0 or higher.

1.2.0
-----

This release does not ship with any (real) new features. It comes with a lot
of improvements to not use deprecated features when being used with Symfony
2.7 and (the yet to be released) Symfony 2.8.

Some other improvements that have been made:

* switch to PSR-4 autoloading
* improve the handling of configured options in the video uploader form
  extension class (it will now trigger meaningful error messages when invalid
  values were used)

**CAUTION**: Before 1.2, the video uploader form extension was able to work
with custom implementations of the `OptionsResolverInterface`. This does not
work anymore. Instances of the `OptionsResolver` class must always be used.

1.1.1
-----

* update dependencies to work with Symfony 2.5.4

1.1.0
-----

* move event name constants to the event classes

* extend the panda:video:upload command to be able to path profiles used to
  encode the video, a custom path format and an arbitrary payload

* less restrictive type hint in the ``VideoUploaderExtension`` (rely on the
  ``UrlGeneratorInterface`` instead of requiring the complete ``Router`` class
  (by @stof in #1)

* extract event names into their own class constants (by @tgallice in #2)

* reflect the extended abilities from the 1.1.0 release of xabbuh/panda-client
  in the commands

* full support for HHVM

* fix tests to use the `UrlGeneratorInterface` instead of the complete `Router`
  (this makes them pass again with Symfony 2.6 or higher)

1.0.4
-----

* update dependencies to work with Symfony 2.5.4

1.0.3
-----

* add tests for the ``EventFactory`` class

1.0.2
-----

* ensure that a form id is set when enabling the upload form widget

* add tests for the ``VideoUploaderExtension`` class

* commands don't terminate unexpectedly when the client library throws an
  exception

1.0.1
-----

* service container extension test improvements (by using test case classes
  from the ``matthiasnoback/symfony-dependency-injection-test`` package)

* do not display misleading pagination information with empty result sets in
  the ``panda:video:list`` command
