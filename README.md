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

1. Download and extract to `system/modules` manually or using composer `composer require gplcart/twig`. IMPORTANT: If you downloaded the module manually, be sure that the name of extracted module folder doesn't contain a branch/version suffix, e.g `-master`. Rename if needed.
2. Go to `admin/module/list` end enable the module
3. Optionally adjust settings on `admin/module/settings/twig`