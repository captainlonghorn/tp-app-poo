<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 11/06/2017
 * Time: 14:19
 */

namespace OCFram;


class AppCacheData extends AppCache
{

    public function __construct($app, $module, $itemName)
    {
        //parent::__construct($app);

        $this->setFileName($app, $module, $itemName);
        $this->setFilePath();
    }

    function setFileName($app, $module, $itemName)
    {
        if ($itemName) {
            $this->fileName = ucfirst($itemName);
        } else {
            throw new \Exception ('Impossible de construire le nom du cache');
        }
    }

    function setFilePath()
    {
        $path = parent::DATAS_CACHE_DIR_PATH;
        $name = $this->getFileName();
        $this->filePath = $path . '\\' . $name . '.html';
    }
    /*
    public function getCache()
    {
        // TODO: Implement getCache() method.
    }
    */
    
    /*
    public function cacheWrite($buffer)
    {
        // on prépare le fichier
        // si le fichier existe on le détruit
        if (file_exists($this->getFilePath())) {
            $this->delete();
        }
        // et on prépare le nouveau fichier
        $file = fopen($this->getFilePath(), 'x+');
        // on écrit le timestamp
        $timestamp = time();
        fputs($file, $timestamp. "\r\n");
        // on écrit le html
        fputs($file, $buffer);
        fclose($file);
        if ($this::SHOW_CACHE_INFO) {
            echo '<p>Ecriture du nouveau fichier '.$this->getFilePath().' avec timestamp : '.$timestamp .'</p>';
        }
    }*/

}