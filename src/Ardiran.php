<?php

namespace Ardiran\Core;

use Ardiran\Core\Application\Container;
use Ardiran\Core\Traits\Singleton;

class Ardiran{

    use Singleton;

    /**
     * Instance of Laravel Container.
     *
     * @var Container
     */
    private $container;

    /**
     * List with the providers to load in the Laravel container.
     *
     * @var array
     */
    private $providers = [
        \Ardiran\Core\Facades\FacadeProvider::class,
        \Ardiran\Core\Config\ConfigProvider::class,
        \Ardiran\Core\View\Blade\BladeProvider::class,
        \Ardiran\Core\Routing\RouterProvider::class,
        \Ardiran\Core\Wp\Routing\RouterProvider::class,
    ];

    /**
     * List facades.
     *
     * @var array
     */
    private $aliases = [
        'Config' => \Ardiran\Core\Facades\Config::class,
        'Request' => \Ardiran\Core\Facades\Request::class,
        'Blade' => \Ardiran\Core\Facades\Blade::class,
        'ServiceManager' => \Ardiran\Core\Facades\ServiceManager::class,
        'WpRoute' => \Ardiran\Core\Wp\Facades\Route::class,
    ];

    /**
     * List with the aliases that have been loaded.
     *
     * @var array
     */
    protected $loadedAliases = [];
    
    /**
     * Constructor
     */
    public function __construct(){

        $this->container = new Container();

        $this->register();

    }

    /**
     * Register all principal elements in Laravel Container.
     * Register all principal elements in Ardiran App context.
     */
    private function register(){

        $this->container->registerProviders($this->providers);
        $this->container->registerAliases($this->aliases);

        $this->registerAliases($this->aliases);

    }

    /**
     * Register all aliases (Facades).
     *
     * @param array $aliases
     */
    public function registerAliases(array $aliases){

        if (!empty($aliases) && is_array($aliases)) {

            foreach ($aliases as $alias => $fullname) {
                $this->registerAlias($fullname, $alias);
            }

        }

    }

    /**
     * Register alias class.
     *
     * @param $fullname
     * @param $alias
     */
    public function registerAlias($fullname, $alias){

        if (array_key_exists($aliaslwc = strtolower($alias), $this->loadedAliases)) {
            return;
        }

        $this->loadedAliases[$aliaslwc] = true;

        class_alias($fullname, $alias);

    }

    /**
     * Allows access to container functionalities or obtain the container.
     *
     * @param string $abstract
     * @param array $parameters
     * @return void
     */
    public function container($abstract = null, $parameters = []){
       
        if (!$abstract) {
            return $this->container;
        }

        return $this->container->bound($abstract)
            ? $this->container->make($abstract, $parameters)
            : $this->container->make("ardiran.{$abstract}", $parameters);

    }

}