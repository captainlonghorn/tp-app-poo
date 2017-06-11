<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 10/06/2017
 * Time: 09:46
 */

namespace OCFram;


class AppCacheView extends AppCache
{

    public function __construct($appname, string $module, string $viewname)
    {
        //parent::__construct($app);

        $this->setFileName($appname, $module, $viewname);
        $this->setFilePath();
    }

    function setFileName($name, $module, $viewname)
    {
        // TODO: Implement setFileName() method.
        if ($name and $module and $viewname) {
            $this->fileName = ucfirst($name) . '_' . ucfirst($module) . '_' . strtolower($viewname);
        } else {
            throw new \Exception ('Impossible de construire le nom du cache');
        }
    }

    function getFileName()
    {
        return $this->fileName;
    }

    function setFilePath()
    {
        $path = parent::VIEWS_CACHE_DIR_PATH;
        $name = $this->getFileName();
        $this->filePath = $path . '\\' . $name . '.html';
    }

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
    }

    public function getCache()
    {
        // On va récupérer le contenu du html et le passer à la page
        $file = fopen($this->getFilePath(), 'r');
        $buffer = '';
        // sauf la ligne 1
        $premiere_ligne = true;
        while ($line = fgets($file)) {
            if ($premiere_ligne){
                $premiere_ligne = false;
                continue;
            }
            $buffer .= $line;
        }
        return $buffer;

    }


}