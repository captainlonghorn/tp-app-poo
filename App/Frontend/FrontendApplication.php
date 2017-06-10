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
     * Sinon, on bâtit le cache, et on l'affiche.
     */
    public function run()
    {
        $controller = $this->getController();
        // Infos nécessaires au test du cache
        $moduleName = $controller->getModule();
        $viewName = $controller->getView();
        /*
         * attention : certaines vues ne pourraont pas être mises en cache telles quelle.
         * si il y a un id, on l'isole pour compléter le nom de cache (sinon on aurait toujours le même
         * cache pour différentes news
         */
        $id = htmlspecialchars(parent::httpRequest()->getData('id'));
        if ($id) {
            $cachename = $viewName . '-' . $id;
        } else {
            $cachename = $viewName;
        }
        // construction du cache avec l'adresse complète
        $cacheView = new AppCacheView($this->name, $moduleName, $cachename);

        // test l'existence et la validité du cache
        if ($cacheView->cacheExists()) {
            // récupération de la page en cache
            $buffer = $cacheView->getCache();
        } else {
            // si pas de cache valide on execute le controller
            $controller->execute();
            $page = $controller->page();
            // génération de la page construite
            $buffer = $page->getGeneratedPage();
            // puis on cache le résultat
            $cacheView->cacheWrite($buffer);
        }

        // et finalement envoi de la réponse
        $this->httpResponse->send($buffer);
    }
}