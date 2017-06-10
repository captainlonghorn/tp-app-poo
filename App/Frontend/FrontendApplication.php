<?php
namespace App\Frontend;

use \OCFram\Application;
use \OCFram\AppCacheView;

class FrontendApplication extends Application
{
  public function __construct()
  {
    parent::__construct();

    $this->name = 'Frontend';
  }

  /*
   * Dans le run on va tester la présence d'un cache.
   * Si ce cache existe et qu'il est valide, on l'affiche.
   * Sinon, on rebatit le cache, et on l'affiche.
   */
  public function run()
  {
    $controller = $this->getController();

    // test du cache
    $appName = $this->name();
    $moduleName = $controller->getModule();
    $viewName = $controller->getView();
    $cacheView = new AppCacheView($this, $moduleName, $viewName);


    var_dump($cacheView);






    $controller->execute();

    $this->httpResponse->setPage($controller->page());
    $this->httpResponse->send();
  }
}