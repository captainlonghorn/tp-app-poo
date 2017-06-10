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


    public function __construct(Application $app, string $module, string $viewname)
    {
        parent::__construct($app);
        $this->setFileName($app->name(), $module, $viewname);
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
        $this->filePath = $path . '/' . $name . '.html';
    }

    function getFilePath()
    {
        return $this->filePath;
    }


}