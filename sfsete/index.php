<?php
    header("Content-type: text/html; charset=utf-8");
    setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');

    
    function __autoload($classe)
    {
        $folders = array('model', 'class');
        foreach ($folders as $folder) {
            if (file_exists("models/{$folder}/{$classe}.class.php")) {
                include_once "models/{$folder}/{$classe}.class.php";
            }
        }
    }

    class Aplication
    {
        public static function run()
        {
            // ini_set("display_errors",1);
            // ini_set("display_startup_errors",1);
            // error_reporting(E_ALL);

            $content = "";
            if(isset($_GET["page"])) {
                $class = $_GET["page"];
                if(class_exists($class)) {
                    $pagina = new $class;
                    $content = $pagina->page();
                }
            }

            echo $content;
        }
    }

    Aplication::run();
?>