<?php
namespace OCFram;

abstract class BackController extends ApplicationComponent
{
  protected $action = '';
  protected $module = '';
  protected $page = null;
  protected $view = '';
  protected $managers = null;
  // ajout du tableau de gestion des caches
  protected $caches = [];

  public function __construct(Application $app, $module, $action)
  {
    parent::__construct($app);

    $this->managers = new Managers('PDO', PDOFactory::getMysqlConnexion());
    $this->page = new Page($app);

    $this->setModule($module);
    $this->setAction($action);
    $this->setView($action);

    // ajout du tableau des durées de cache
    $this->createCache();
  }

  /*
   * Fonction à ajouter dans tous les controllers afin de répondre aux problématiques de cache
  */
  abstract function createCache();
  /*
   * Fonction qui vérifiera si on doit chercher en cache pour telle action dans ce controller
   * Rappel : Nom de la vue = nom de l'action Voir ci dessus $this->setView($action);
   */
  public function getUseCache(string $actionName)
  {
    if (isset($this->caches[(string) $actionName])){
      return $this->caches[(string) $actionName];
    }
    return false;
  }

  public function execute()
  {
    $method = 'execute'.ucfirst($this->action);

    if (!is_callable([$this, $method]))
    {
      throw new \RuntimeException('L\'action "'.$this->action.'" n\'est pas définie sur ce module');
    }

    $this->$method($this->app->httpRequest());
  }

  public function page()
  {
    return $this->page;
  }

  public function setModule($module)
  {
    if (!is_string($module) || empty($module))
    {
      throw new \InvalidArgumentException('Le module doit être une chaine de caractères valide');
    }

    $this->module = $module;
  }

  public function setAction($action)
  {
    if (!is_string($action) || empty($action))
    {
      throw new \InvalidArgumentException('L\'action doit être une chaine de caractères valide');
    }

    $this->action = $action;
  }

  public function setView($view)
  {
    if (!is_string($view) || empty($view))
    {
      throw new \InvalidArgumentException('La vue doit être une chaine de caractères valide');
    }

    $this->view = $view;

    $this->page->setContentFile(__DIR__.'/../../App/'.$this->app->name().'/Modules/'.$this->module.'/Views/'.$this->view.'.php');
  }

  /*
   * Ajouts nécessaires à la construction du cache
   */
  /**
   * @return string
   */
  public function getModule()
  {
    return $this->module;
  }

  /**
   * @return string
   */
  public function getView()
  {
    return $this->view;
  }


}