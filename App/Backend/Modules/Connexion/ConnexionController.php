<?php
namespace App\Backend\Modules\Connexion;

use \OCFram\BackController;
use \OCFram\HTTPRequest;

class ConnexionController extends BackController
{

    /*
       * La fonction demandée pour stocker les vues à cacher et leurs valeurs
       * Le tableau est de la forme nom-de-la-vue => duree-en-secondes
       */
    public function createCache()
    {
        $this->caches = array(
            'index' => 120,
        );
    }

    public function executeIndex(HTTPRequest $request)
    {
        $this->page->addVar('title', 'Connexion');

        if ($request->postExists('login')) {
            $login = $request->postData('login');
            $password = $request->postData('password');

            if ($login == $this->app->config()->get('login') && $password == $this->app->config()->get('pass')) {
                $this->app->user()->setAuthenticated(true);
                $this->app->httpResponse()->redirect('.');
            } else {
                $this->app->user()->setFlash('Le pseudo ou le mot de passe est incorrect.');
            }
        }
    }
}