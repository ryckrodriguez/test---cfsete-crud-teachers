<?php
    final class Connection {
        private function __construct() {}

        public static function connect() {
            try {
                $server = "container_db";
                $port = "3306";
                $db = "db_md_sfsete";
                $user = "root";
                $pass = "SFSete2023!";
                $connect = new PDO("mysql:host={$server};port={$port};dbname={$db}", $user, $pass);
                // $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $connect;
            } catch (Exception $e) {
                return "Falha na conexão";
            }
        }

        public static function search($query){
            try {
                $conection = Transaction::get();
                $result = $conection->Query($query);
                if(empty($result)) {
                    $response["error"] = true;
                    $response["msg"] = "Nenhum valor encontrado!";
                    return $response;
                } else {
                    if($result->rowCount() == 0) {
                        $response["error"] = true;
                        $response["msg"] = "Nenhum valor encontrado!";
                    } else {
                        if($result->rowCount()==1){
                            $table[] = $result->fetchObject();
                        }else{	

                            while($data = $result->fetchObject()) {
                                foreach ($data as $key => $value) {
                                    // $data->$key = utf8_encode($value);
                                    $data->$key = $value;
                                }
                                $table[] = $data;
                            }
                        }
                        $response["msg"] = $table;
                        $response["error"] = false;
                        $response["title"] = "Sucesso";
                    }
                }

            } catch(Exception $e) {
                $response["error"] = true;
                $response["msg"] = "Ocorreu um erro, por favor, entre em contato com o administrador";
                // $response["msg"] = $e->getMessage();
            }
            return $response;
        }

        public static function execute($query){
            try {
                $conection = Transaction::get();
                $result = $conection->Query($query);

                $response["error"] = true;
                $response["title"] = "Erro";
                $response["msg"] = "Erro ao executar a query!";
                if(!empty($result)) {
                    $response["error"] = false;
                    $response["title"] = "Sucesso";
                    $response["msg"] = "Operação Realizada com Sucesso";
                }
            }
            catch(Exception $e) {
                $response["error"] = true;
                $response["msg"] = "Ocorreu um erro entre em contato com o Administrador!";
            }

            return $response;
        }

        public static function delete_register($table, $id){
            try {
                $conection = Transaction::get();
                $query = "UPDATE {$table} SET flg_active = 0, deleted_at = NOW() WHERE id = {$id}";
                $result = $conection->Query($query);

                $response["error"] = false;
                $response["title"] = "Sucesso";
                $response["msg"] = "Operação Realizada com Sucesso";
                if(empty($result)) {
                    $response["error"] = true;
                    $response["title"] = "Erro";
                    $response["msg"] = "Nenhum valor alterado";
                }
            } catch(Exception $e) {
                $response["error"] = true;
                $response["msg"] = "Ocorreu um erro entre em contato com o Administrador ";
            }
            return $response;
        }

        public static function insert_data($table, $data){
            try {
                $conection = Transaction::get();
                $arrFields = [];
                $arrValues = [];
                foreach ($data as $key => $value) {
                    $arrFields[] = $key;
                    $arrValues[] = strip_tags($value);
                }
                $fields = implode(" ,", $arrFields);
                $values = implode(" ,", $arrValues);

                $query = "INSERT INTO {$table} ({$fields}) VALUES ({$values})";
                $result = $conection->Query($query);

                $response["error"] = true;
                $response["title"] = "Erro";
                $response["msg"] = "Nenhum valor inserido!";
                if(!empty($result)) {
                    $response["error"] = false;
                    $response["title"] = "Sucesso";
                    $response["msg"] = "Operação Realizada com Sucesso";
                    $response["id"] = $conection->lastInsertId();
                }
            } catch(Exception $e) {
                $response["error"] = true;
                $response["msg"] = "Ocorreu um erro, por favor, entre em contato com o administrador";
            }
            return $response;
        }

        public static function insert_data_multi($table, $data){
            try {
                $conection = Transaction::get();
                $arrFields = [];
                $arrValues = [];
                foreach ($data as $dataKey => $dataValue) {
                    $arrFields = [];
                    foreach ($dataValue as $key => $value) {
                        $arrFields[] = $key;
                        $arrValues[$dataKey][] = strip_tags($value);
                    }
                    $fields = implode(" ,", $arrFields);
                    $valuesAux[$dataKey] = "(".implode(" ,", $arrValues[$dataKey]).")";
                }
                $values = implode(" ,",$valuesAux);

                $query = "INSERT INTO {$table} ({$fields}) VALUES {$values}";
                $result = $conection->Query($query);

                $response["error"] = true;
                $response["title"] = "Erro";
                $response["msg"] = "Nenhum valor inserido!";
                if(!empty($result)) {
                    $response["error"] = false;
                    $response["title"] = "Sucesso";
                    $response["msg"] = "Operação Realizada com Sucesso";
                    $response["id"] = $conection->lastInsertId();
                }
            } catch(Exception $e) {
                $response["error"] = true;
                $response["msg"] = "Ocorreu um erro, por favor, entre em contato com o administrador";
            }
            return $response;
        }

        public static function edit_data($table, $data){
            try {
                $conection = Transaction::get();
                $arrFields = [];
                foreach ($data as $key => $value) {
                    if ($key!="id") {
                        $arrFields[] = "{$key} = " . strip_tags($value);
                    }
                }

                $fields = implode(" ,", $arrFields);
                $id = $data["id"];
                $query = "UPDATE {$table} SET {$fields} WHERE id = {$id}";
                $result = $conection->Query($query);

                $response["error"] = true;
                $response["title"] = "Erro";
                $response["msg"] = "Nenhum valor Alterado!";
                if(!empty($result)) {
                    $response["error"] = false;
                    $response["title"] = "Sucesso";
                    $response["msg"] = "Operação Realizada com Sucesso";
                }
            } catch(Exception $e) {
                $response["error"] = true;
                $response["msg"] = "Ocorreu um erro, por favor, entre em contato com o administrador";
            }
            return $response;
        }

    }
?>
