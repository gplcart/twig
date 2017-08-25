<?php

/**
 * @package Twig
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\twig;

use gplcart\core\Module;

/**
 * Main class for Twig module
 */
class Twig extends Module
{

    /**
     * An array of TWIG instances keyed by file directory
     * @var array
     */
    protected $twig = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
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
            'version_source' => array(
                'lines' => 100,
                'pattern' => '/.*VERSION.*(\\d+\\.+\\d+\\.+\\d+)/',
                'file' => 'vendor/twig/twig/lib/Twig/Environment.php'
            ),
            'files' => array(
                'vendor/autoload.php'
            )
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
     * @param \gplcart\core\Controller $object
     */
    public function hookTemplateRender($templates, $data, &$rendered, $object)
    {
        list($original, $overridden) = $templates;

        if (is_file("$overridden.twig")) {
            $rendered = $this->render("$overridden.twig", $data, $object);
        } else if (is_file("$original.twig")) {
            $rendered = $this->render("$original.twig", $data, $object);
        }
    }

    /**
     * Returns a TWIG instance for the given file directory
     * @param string $path
     * @param \gplcart\core\Controller $object
     */
    public function getTwigInstance($path, $object)
    {
        $options = array();

        if (empty($this->twig)) {
            $this->getLibrary()->load('twig');
            $options = $this->config->module('twig');
        }

        if (isset($this->twig[$path])) {
            return $this->twig[$path];
        }

        if (!empty($options['cache'])) {
            $options['cache'] = __DIR__ . '/cache';
        }

        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem($path), $options);

        if (!empty($options['debug'])) {
            $twig->addExtension(new \Twig_Extension_Debug());
        }

        foreach ($this->getDefaultFunctions($object) as $function) {
            $twig->addFunction($function);
        }

        return $this->twig[$path] = $twig;
    }

    /**
     * Renders a .twig template
     * @param string $template
     * @param array $data
     * @param \gplcart\core\Controller $object
     * @return string
     */
    public function render($template, $data, $object)
    {
        $parts = explode('/', $template);
        $file = array_pop($parts);

        $twig = $this->getTwigInstance(implode('/', $parts), $object);

        $controller_data = $object->getData();
        return $twig->loadTemplate($file)->render(array_merge($controller_data, $data));
    }

    /**
     * Validate a TWIG template syntax
     * @param string $file
     * @param \gplcart\core\Controller $controller
     * @return boolean|string
     */
    public function validate($file, $controller)
    {
        if (!$controller instanceof \gplcart\core\Controller) {
            throw new \InvalidArgumentException('Second argument must be instance of \gplcart\core\Controller');
        }

        $info = pathinfo($file);
        $twig = $this->getTwigInstance($info['dirname'], $controller);

        try {
            $content = file_get_contents($file);
            $twig->parse($twig->tokenize(new \Twig_Source($content, $info['basename'])));
            return true;
        } catch (\Twig_Error_Syntax $e) {
            return $e->getMessage();
        }
    }

    /**
     * Adds custom functions and returns an array of Twig_SimpleFunction objects
     * @param \gplcart\core\Controller $controller
     * @return array
     */
    protected function getDefaultFunctions($controller)
    {
        if (!$controller instanceof \gplcart\core\Controller) {
            throw new \InvalidArgumentException('Argument must be instance of \gplcart\core\Controller');
        }

        $functions = array();

        $functions[] = new \Twig_SimpleFunction('error', function ($key = null, $has_error = null, $no_error = '') use ($controller) {
            return $controller->error($key, $has_error, $no_error);
        }, array('is_safe' => array('all')));

        $functions[] = new \Twig_SimpleFunction('text', function ($text, $arguments = array()) use ($controller) {
            return $controller->text($text, $arguments);
        }, array('is_safe' => array('all')));

        $functions[] = new \Twig_SimpleFunction('access', function ($permission) use ($controller) {
            return $controller->access($permission);
        });

        $functions[] = new \Twig_SimpleFunction('url', function ($path = '', array $query = array(), $absolute = false) use ($controller) {
            return $controller->url($path, $query, $absolute);
        });

        $functions[] = new \Twig_SimpleFunction('date', function ($timestamp = null, $full = true, $unix_format = '') use ($controller) {
            return $controller->date($timestamp, $full, $unix_format);
        });

        $functions[] = new \Twig_SimpleFunction('attributes', function ($attributes) use ($controller) {
            return $controller->attributes($attributes);
        }, array('is_safe' => array('all')));

        $functions[] = new \Twig_SimpleFunction('config', function ($key = null, $default = null) use ($controller) {
            return $controller->config($key, $default);
        });

        $functions[] = new \Twig_SimpleFunction('configTheme', function ($key = null, $default = null) use ($controller) {
            return $controller->configTheme($key, $default);
        });

        $functions[] = new \Twig_SimpleFunction('summary', function ($text, $xss = false, $filter = null) use ($controller) {
            return $controller->summary($text, $xss, $filter);
        }, array('is_safe' => array('all')));

        $functions[] = new \Twig_SimpleFunction('filter', function ($text, $filter = null) use ($controller) {
            return $controller->filter($text, $filter);
        }, array('is_safe' => array('all')));

        $functions[] = new \Twig_SimpleFunction('truncate', function ($string, $length = 100, $trimmarker = '...') use ($controller) {
            return $controller->truncate($string, $length, $trimmarker);
        });

        $functions[] = new \Twig_SimpleFunction('path', function ($path = null) use ($controller) {
            return $controller->path($path);
        });

        return $functions;
    }

    /**
     * Implements hook "module.enable.after"
     */
    public function hookModuleEnableAfter()
    {
        $this->getLibrary()->clearCache();
    }

    /**
     * Implements hook "module.disable.after"
     */
    public function hookModuleDisableAfter()
    {
        $this->getLibrary()->clearCache();
    }

    /**
     * Implements hook "module.install.after"
     */
    public function hookModuleInstallAfter()
    {
        $this->getLibrary()->clearCache();
    }

    /**
     * Implements hook "module.uninstall.after"
     */
    public function hookModuleUninstallAfter()
    {
        $this->getLibrary()->clearCache();
    }

}
