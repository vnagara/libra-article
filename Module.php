<?php

namespace LibraArticle;

use Zend\ModuleManager\ModuleManager;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    protected static $options;

    public function init(ModuleManager $moduleManager)
    {
        $moduleManager->getEventManager()->attach('loadModules.post', array($this, 'setOptions'));
    }

    public function getSitemapConfig($sl)
    {
        $em = $sl->get('Doctrine\ORM\EntityManager');
        $model = new Model\ArticleModel($em);
        $urlHelper = $sl->get('ViewRenderer')->plugin('url');
        $urlsets = $model->getSitemap($urlHelper);
        return $urlsets;
    }

    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'formCkeditor' => 'LibraArticle\View\Helper\FormCkeditor',
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'LibraArticle\ServiceArticle' => function($sl) {
                    $em = $sl->get('Doctrine\ORM\EntityManager');
                    $instance = new Service\Article();
                    $instance->setEntityManager($em);
                    return $instance;
                },
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                array(
                    //'CKEditor' => __DIR__ . '/src/ckeditor_php5.php',
                    //'CKEditor' => 'public/vendor/ckeditor/ckeditor.php',
                    //'CKFinder' => __DIR__ . '/src/ckfinder_php5.php',
                    //'CKFinder' => 'public/vendor/ckfinder/ckfinder.php',
                ),
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function setOptions(ModuleEvent $e)
    {
        $config = $e->getConfigListener()->getMergedConfig(false);
        static::$options = $config['libra_article'];
    }

    public static function getOption($option)
    {
        if (!isset(static::$options[$option])) {
            return null;
        }
        return static::$options[$option];
    }

    /**
     * executes on boostrap
     * @param \Zend\Mvc\MvcEvent $e
     * @return null
     */
    public function onBootstrap(MvcEvent $e)
    {
    }

}
