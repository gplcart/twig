<?php

/**
 * @package Twig
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\twig;

use Exception;
use gplcart\core\Controller;
use gplcart\core\Library;
use gplcart\core\Module;
use InvalidArgumentException;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;
use Twig_Source;

/**
 * Main class for Twig module
 */
class Main
{

    /**
     * An array of TWIG instances keyed by a file directory
     * @var array
     */
    protected $twig = array();

    /**
     * Module class instance
     * @var \gplcart\core\Module $module
     */
    protected $module;

    /**
     * Library class instance
     * @var \gplcart\core\Library $library
     */
    protected $library;

    /**
     * @param Module $module
     * @param Library $library
     */
    public function __construct(Module $module, Library $library)
    {
        $this->module = $module;
        $this->library = $library;
    }

    /**
     * Implements hook "library.list"
     * @param array $libraries
     */
    public function hookLibraryList(array &$libraries)
    {
        $libraries['twig'] = array(
            'name' => 'Twig',
            'description' => 'Twig is a template engine for PHP',
            'url' => 'https://github.com/twigphp/Twig',
            'download' => 'https://github.com/twigphp/Twig/archive/v1.33.0.zip',
            'type' => 'php',
            'module' => 'twig',
            'version' => '1.33.0',
            'vendor' => 'twig/twig'
        );
    }

    /**
     * Implements hook "route.list"
     * @param array $routes
     */
    public function hookRouteList(array &$routes)
    {
        $routes['admin/module/settings/twig'] = array(
            'access' => 'module_edit',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\twig\\controllers\\Settings', 'editSettings')
            )
        );
    }

    /**
     * Implements hook "template.render"
     * @param array $templates
     * @param array $data
     * @param null|string $rendered
     * @param \gplcart\core\Controller $controller
     */
    public function hookTemplateRender($templates, $data, &$rendered, $controller)
    {
        $this->setRenderedTemplate($templates, $data, $rendered, $controller);
    }

    /**
     * Returns a TWIG instance for the given file directory
     * @param string $path
     * @param Controller $controller
     * @return Twig_Environment
     * @throws InvalidArgumentException
     */
    public function getTwigInstance($path, $controller)
    {
        if (!$controller instanceof Controller) {
            throw new InvalidArgumentException('Second argument must be instance of \gplcart\core\Controller');
        }

        $options = array();

        if (empty($this->twig)) {
            $this->library->load('twig');
            $options = $this->module->getSettings('twig');
        }

        if (isset($this->twig[$path])) {
            return $this->twig[$path];
        }

        if (!empty($options['cache'])) {
            $options['cache'] = __DIR__ . '/cache';
        }

        $twig = new Twig_Environment(new Twig_Loader_Filesystem($path), $options);

        if (!empty($options['debug'])) {
            $twig->addExtension(new Twig_Extension_Debug());
        }

        foreach ($this->getDefaultFunctions($controller) as $function) {
            $twig->addFunction($function);
        }

        return $this->twig[$path] = $twig;
    }

    /**
     * Renders a .twig template
     * @param string $template
     * @param array $data
     * @param Controller $controller
     * @return string
     */
    public function render($template, $data, Controller $controller)
    {
        try {
            $parts = explode('/', $template);
            $file = array_pop($parts);
            $twig = $this->getTwigInstance(implode('/', $parts), $controller);
            $controller_data = $controller->getData();
            return $twig->loadTemplate($file)->render(array_merge($controller_data, $data));
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * Validate a TWIG template syntax
     * @param string $file
     * @param Controller $controller
     * @return boolean|string
     */
    public function validate($file, Controller $controller)
    {
        try {
            $pathinfo = pathinfo($file);
            $twig = $this->getTwigInstance($pathinfo['dirname'], $controller);
            $content = file_get_contents($file);
            $twig->parse($twig->tokenize(new Twig_Source($content, $pathinfo['basename'])));
            return true;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * Sets rendered .twig template
     * @param array $templates
     * @param array $data
     * @param null|string $rendered
     * @param Controller $controller
     */
    protected function setRenderedTemplate($templates, $data, &$rendered, Controller $controller)
    {
        list($original, $overridden) = $templates;

        if (is_file("$overridden.twig")) {
            $rendered = $this->render("$overridden.twig", $data, $controller);
        } else if (is_file("$original.twig")) {
            $rendered = $this->render("$original.twig", $data, $controller);
        }
    }

    /**
     * Adds custom functions and returns an array of Twig_SimpleFunction objects
     * @param Controller $controller
     * @return array
     */
    protected function getDefaultFunctions(Controller $controller)
    {
        $functions = array();

        $functions[] = new Twig_SimpleFunction('error', function ($key = null, $has_error = null, $no_error = '') use ($controller) {
            return $controller->error($key, $has_error, $no_error);
        }, array('is_safe' => array('all')));

        $functions[] = new Twig_SimpleFunction('text', function ($text, $arguments = array()) use ($controller) {
            return $controller->text($text, $arguments);
        }, array('is_safe' => array('all')));

        $functions[] = new Twig_SimpleFunction('access', function ($permission) use ($controller) {
            return $controller->access($permission);
        });

        $functions[] = new Twig_SimpleFunction('url', function ($path = '', array $query = array(), $absolute = false) use ($controller) {
            return $controller->url($path, $query, $absolute);
        });

        $functions[] = new Twig_SimpleFunction('date', function ($timestamp = null, $full = true) use ($controller) {
            return $controller->date($timestamp, $full);
        });

        $functions[] = new Twig_SimpleFunction('attributes', function ($attributes) use ($controller) {
            return $controller->attributes($attributes);
        }, array('is_safe' => array('all')));

        $functions[] = new Twig_SimpleFunction('config', function ($key = null, $default = null) use ($controller) {
            return $controller->config($key, $default);
        });

        $functions[] = new Twig_SimpleFunction('configTheme', function ($key = null, $default = null) use ($controller) {
            return $controller->configTheme($key, $default);
        });

        $functions[] = new Twig_SimpleFunction('teaser', function ($text, $xss = false, $filter = null) use ($controller) {
            return $controller->teaser($text, $xss, $filter);
        }, array('is_safe' => array('all')));

        $functions[] = new Twig_SimpleFunction('filter', function ($text, $filter = null) use ($controller) {
            return $controller->filter($text, $filter);
        }, array('is_safe' => array('all')));

        $functions[] = new Twig_SimpleFunction('truncate', function ($string, $length = 100, $trimmarker = '...') use ($controller) {
            return $controller->truncate($string, $length, $trimmarker);
        });

        $functions[] = new Twig_SimpleFunction('path', function ($path = null) use ($controller) {
            return $controller->path($path);
        });

        return $functions;
    }

}
