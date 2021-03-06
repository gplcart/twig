[![Build Status](https://scrutinizer-ci.com/g/gplcart/twig/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gplcart/twig/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gplcart/twig/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gplcart/twig/?branch=master)

Twig is a [GPL Cart](https://github.com/gplcart/gplcart) module that integrates [TWIG](https://twig.sensiolabs.org) template engine into your GPL Cart site. Essentially it tries to parse every template file with .twig extension

Custom TWIG functions added by this module:

- error()
- text()
- access()
- url()
- date()
- attributes()
- config()
- configTheme()
- teaser()
- filter()
- truncate()
- path()

See `\gplcart\core\Controller` for corresponding methods

**Installation**

This module requires 3-d party library which should be downloaded separately. You have to use [Composer](https://getcomposer.org) to download all the dependencies.

1. From your web root directory: `composer require gplcart/twig`. If the module was downloaded and placed into `system/modules` manually, run `composer update` to make sure that all 3-d party files are presented in the `vendor` directory.
2. Go to `admin/module/list` end enable the module
3. Optionally adjust settings on `admin/module/settings/twig`