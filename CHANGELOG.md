CHANGELOG
=========

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
