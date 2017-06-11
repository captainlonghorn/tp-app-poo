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
    const DATAS_CACHE_DIR_PATH = __DIR__ . '\..\..\tmp\cache\datas';
    const VIEWS_CACHE_DIR_PATH = __DIR__ . '\..\..\tmp\cache\views';

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

    function getFilePath()
    {
        return $this->filePath;
    }

    public function cacheExists($duration)
    {
        if ($this::SHOW_CACHE_INFO) {
            $cacheInfo = 'Info de cache : ';
        }
        if (file_exists($this->getFilePath())) {
            if ($this::SHOW_CACHE_INFO) {
                $cacheInfo .= ' fichier '.$this->getFilePath().' trouvé <br>';
            }
            // dans ce cas on teste directement la validité de ce cache
            if ($this->isValid($duration)) {
                if ($this::SHOW_CACHE_INFO) {
                    $cacheInfo .= ' timestamp de ' . $duration . ' sec. :  OK <br>';
                    echo '<p>'.$cacheInfo.'</p>';
                }
                return true;
            }
            if ($this::SHOW_CACHE_INFO) {
                $cacheInfo .= ' timestamp de ' . $duration . ' sec. :  KO <br>';
                echo '<p>' . $cacheInfo . '</p>';
            }
            return false;
        }
        if ($this::SHOW_CACHE_INFO) {
            $cacheInfo .= ' fichier '.$this->getFilePath().' non trouvé <br>';
            echo '<p>'.$cacheInfo.'</p>';
        }

        return false;
    }

    public function isValid(int $duration)
    {
        // lecture du fichier en cache
        $file = fopen($this->getFilePath(), 'r');
        // extraction du timestamp pour test de la validité > c'est la ligne 1
        $file_timestamp = fgets($file);
        // test de la validité du timestamp : différence entre timestamp de maintenant et celui du
        // fichier doit être < au nombre de seconds indiqué par le controller
        $maintenant = time();
        if (($maintenant-$file_timestamp) > $duration){
            return false;
        }
        return true;
    }

    abstract public function getCache();

    // l'écriture sera différente pour les vues et les listes
    abstract public function cacheWrite($buffer);

    public function delete()
    {
        if ($this::SHOW_CACHE_INFO) {
            echo '<p>Delete du fichier '.$this->getFilePath().'</p>';
        }
        if (file_exists($this->getFilePath())) {
            unlink($this->getFilePath());
        }
        return false;
    }


}