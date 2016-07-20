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
