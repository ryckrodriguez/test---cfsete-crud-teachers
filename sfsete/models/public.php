<?php
    header("Content-type: text/html; charset=utf-8");
    setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');

    
    function __autoload($classe)
    {
        $folders = array('model', 'class');
        foreach ($folders as $folder) {
            if (file_exists("{$folder}/{$classe}.class.php")) {
                include_once "{$folder}/{$classe}.class.php";
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
                    $action = isset($_POST["action"]) && !empty($_POST["action"]) ? $_POST["action"] : $_GET["action"];

                    Transaction::open();

                    $pagina = new $class;
                    $response = $pagina->$action();

                    if($response["error"]) {
                        Transaction::rollback();
                    } else {
                        Transaction::close();
                    }

                    $content = $response;
                }
            }

            echo json_encode($content);
        }
    }

    Aplication::run();
?>  