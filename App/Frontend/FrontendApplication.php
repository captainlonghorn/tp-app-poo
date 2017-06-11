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

        // utilisation de marqueur pour indiquer :
        $buildView = true; // si on doit reconstruire la vue

        /*
         * 1. le controller doit nous dire si la vue est à cacher et nous donner la durée de cache
         * (COURS : " chaque contrôleur pourra implémenter une méthode createCache() renvoyant un tableau de la forme ['nomdelavue' => 'duree']. ")
         * A noter : la méthode createCache est appelée dans le constructeur de BackController
         */
        $useCache = $controller->getUseCache($controller->getView());

        if ($useCache) {

            /*
             * 2. On construit l'appel à la méthode de la classe cache qui testera :
             * - si le fichier de cache existe
             * - s'il n'est pas expiré
             * On a besoin de connaitre :
             * - le nom de l'appli
             * - le nom du module
             * - le nom de la vue
             * - un id >> attention : certaines vues ne pourront pas être mises en cache telles quelle.
             * si il y a un id, on l'isole pour compléter le nom de cache (sinon on aurait toujours le même cache pour différentes news
             */

            // 2a. construction de l'objet cache avec l'adresse complète
            $moduleName = $controller->getModule();
            $viewName = $controller->getView();
            $id = htmlspecialchars(parent::httpRequest()->getData('id'));
            if ($id) {
                $cachename = $viewName . '-' . $id;
            } else {
                $cachename = $viewName;
            }
            $cache = new AppCacheView($this->name, $moduleName, $cachename);

            // 2b . test l'existence et la validité du cache. A noter > cacheExist effectue les vérifs de fichier et de timestamp
            if ($cache->cacheExists($duration = $useCache)) {
                // récupération de la page en cache (elle existe, a une durée valide et est débarrassée de la ligne du timestamp)
                $pageContent = $cache->getCache();

                // Modification du marqueur pour indiquer qu'on ne reconstruira pas la vue
                $buildView = false;
            }
        }

        /*
         * 3. Execute ou pas ?
         * A ce point on doit ou non construire la vue > si pas de cache à utiliser ou cache obsolète (selon builView)
         * Et on sait si on doit la mettre en cache après constructuin (selon useCache)
        */

        if ($buildView) {
            // si pas de cache valide on execute le controller
            $controller->execute();
            $page = $controller->page();
            // génération de la page construite
            $pageContent = $page->getGeneratedPage();
            if ($useCache) {
                // puis on cache le résultat
                $cache->cacheWrite($pageContent);
            }
        }

        /*
         * 4. Finallement, envoi de la réponse au client
         */

        // et finalement envoi de la réponse
        $this->httpResponse->send($pageContent);
    }
}