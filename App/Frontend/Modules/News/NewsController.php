<?php
namespace App\Frontend\Modules\News;

use OCFram\AppCacheData;
use \OCFram\BackController;
use \OCFram\HTTPRequest;
use \Entity\Comment;
use \FormBuilder\CommentFormBuilder;
use \OCFram\FormHandler;
use \OCFram\AppCacheView;

class NewsController extends BackController
{
    /*
     * La fonction demandée pour stocker les vues à cacher et leurs valeurs
     * Le tableau est de la forme nom-de-la-vue => duree-en-secondes
     */
    public function createCache()
    {
        $this->caches = array(
            'index' => 120,
            'show' => 60,
            'commentsList' => 60
        );
    }

    public function executeIndex(HTTPRequest $request)
    {
        $nombreNews = $this->app->config()->get('nombre_news');
        $nombreCaracteres = $this->app->config()->get('nombre_caracteres');

        // On ajoute une définition pour le titre.
        $this->page->addVar('title', 'Liste des ' . $nombreNews . ' dernières news');

        // On récupère le manager des news.
        $manager = $this->managers->getManagerOf('News');

        $listeNews = $manager->getList(0, $nombreNews);

        foreach ($listeNews as $news) {
            if (strlen($news->contenu()) > $nombreCaracteres) {
                $debut = substr($news->contenu(), 0, $nombreCaracteres);
                $debut = substr($debut, 0, strrpos($debut, ' ')) . '...';

                $news->setContenu($debut);
            }
        }

        // On ajoute la variable $listeNews à la vue.
        $this->page->addVar('listeNews', $listeNews);
    }

    public function executeShow(HTTPRequest $request)
    {
        $news = $this->managers->getManagerOf('News')->getUnique($request->getData('id'));

        if (empty($news)) {
            $this->app->httpResponse()->redirect404();
        }

        $this->page->addVar('title', $news->titre());
        $this->page->addVar('news', $news);

        // On utilise un marqueur pour statuer sur la reconstruction de liste
        $buildList = true;

        /*
         * Gestion du cache
         */
        // On souhaite mettre cette liste en cache
        $dataCacheName = 'commentsList';
        $dataCache = new AppCacheData($this->app, null, $dataCacheName);
        $dataCacheDuration = $this->getUseCache($dataCacheName);

        // Existe t'il une déja liste sérialisée ?
        if ($dataCache->cacheExists($dataCacheDuration)) {
            // récupération de la page en cache (elle existe, a une durée valide et est débarrassée de la ligne du timestamp)
            $data = $dataCache->getCache();
            $comments = unserialize($data);
            // Modification du marqueur pour indiquer qu'on ne reconstruira pas la vue
            $buildList = false;
        }

        if ($buildList) {
            // si on est ici c'est que le cache était invalide ou inexistant
            // construction de la liste
            $comments = $this->managers->getManagerOf('Comments')->getListOf($news->id());
            // serialization
            $serial = serialize($comments);
            // ecriture du cache
            $dataCache->cacheWrite($serial);
        }
        // ajout de la liste de comments à la page
        $this->page->addVar('comments', $comments);
    }

    public function executeInsertComment(HTTPRequest $request)
    {
        // Si le formulaire a été envoyé.
        if ($request->method() == 'POST') {
            $comment = new Comment([
                'news' => $request->getData('news'),
                'auteur' => $request->postData('auteur'),
                'contenu' => $request->postData('contenu')
            ]);
        } else {
            $comment = new Comment;
        }

        $formBuilder = new CommentFormBuilder($comment);
        $formBuilder->build();

        $form = $formBuilder->form();

        $formHandler = new FormHandler($form, $this->managers->getManagerOf('Comments'), $request);

        //$request->getData('news'));
        if ($formHandler->process()) {

            $this->app->user()->setFlash('Le commentaire a bien été ajouté, merci !');

            // Destruction du cache de la news associée
            $cache = new AppCacheView('Frontend', 'News', 'show' . '-' . $request->getData('news'));
            $cache->delete();

            $this->app->httpResponse()->redirect('news-' . $request->getData('news') . '.html');


        }

        $this->page->addVar('comment', $comment);
        $this->page->addVar('form', $form->createView());
        $this->page->addVar('title', 'Ajout d\'un commentaire');
    }
}