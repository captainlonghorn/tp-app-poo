<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 10/06/2017
 * Time: 09:21
 */

namespace OCFram;


abstract class AppCache extends ApplicationComponent
{
    protected $fileName;
    protected $filePath;
    // Constantes :
    // SHOW_CACHE_INFO permettra d'afficher une info lorsque le HTML est issu du cache
    const SHOW_CACHE_INFO = true;
    // Emplacement des dossiers de cache
    const DATAS_CACHE_DIR_PATH = 'tmp/cache/datas';
    const VIEWS_CACHE_DIR_PATH = 'tmp/cache/views';

    public function __construct(Application $app)
    {
        parent::__construct($app);

    }

    /**
     * Cette fonction sera utilisée dans les classes filles, de manière différente, selon
     * que l'on traite une vue ou des datas
     * @return mixed
     */
    abstract function setFileName($appName, $moduleName, $itemName);

    abstract function getFileName();

    abstract function setFilePath();

    abstract function getFilePath();

    public function cacheExists(){

    }

    public function getCache(){

    }

    public function cacheWrite() {

    }

    public function cacheDelete() {

    }


}