<?php

class Teachers {

    private $name;
    public function __construct()
    {
        $this->name = "Docentes";
    }

    public function page()
    {
        $page = file_get_contents("views/pages/teacher.html");
        return $page;
    }

    public function listing()
    {
        try {
            
            $query = "SELECT 
                    teachers.id
                    ,teachers.name
                    ,teachers.birth_date
                    ,teachers.rg_number
                    ,teachers.cpf_number
                    ,teachers.email_address
                    ,teachers.phone
                    ,teachers.gender
                    ,teachers.address
                    ,teachers.address_number
                    ,teachers.address_district
                    ,teachers.address_city
                    ,teachers.address_state
                    ,teachers.address_cep
                    ,teachers.address_city_origin
                    ,GROUP_CONCAT(
                        qualifications.name
                        ORDER BY qualifications.id
                        SEPARATOR ' | '
                    ) AS qualifications
                FROM t_teachers AS teachers
                INNER JOIN t_qualifications AS qualifications
                    ON qualifications.teacher_id = teachers.id
                    AND qualifications.flg_active = 1
                WHERE
                    teachers.flg_active = 1
                GROUP BY teachers.id
                ORDER BY teachers.name";
            $result = Connection::search($query);
    
            if(!$result["error"]){
    
                $content = [];
                foreach ($result["msg"] as $key => $value) {
                    $objTeachers = new stdClass();
    
                    $objTeachers->id = !empty($value->id) ? $value->id : "";
                    $objTeachers->name = !empty($value->name) ? $value->name : "--";
                    $objTeachers->qualifications = !empty($value->qualifications) ? $value->qualifications : "--";
                    $objTeachers->birth_date = !empty($value->birth_date) ? $value->birth_date : "--";
                    $objTeachers->birth_date_br = !empty($value->birth_date) ? date("d/m/Y", strtotime($value->birth_date)) : "--";
                    $objTeachers->rg_number = !empty($value->rg_number) ? $value->rg_number : "--";
                    $objTeachers->cpf_number = !empty($value->cpf_number) ? $value->cpf_number : "--";
                    $objTeachers->email_address = !empty($value->email_address) ? $value->email_address : "--";
                    $objTeachers->phone = !empty($value->phone) ? $value->phone : "--";
                    $objTeachers->gender = !empty($value->gender) ? $value->gender : "--";
                    $objTeachers->address = !empty($value->address) ? $value->address : "--";
                    $objTeachers->address_number = !empty($value->address_number) ? $value->address_number : "--";
                    $objTeachers->address_district = !empty($value->address_district) ? $value->address_district : "--";
                    $objTeachers->address_city = !empty($value->address_city) ? $value->address_city : "--";
                    $objTeachers->address_state = !empty($value->address_state) ? $value->address_state : "--";
                    $objTeachers->address_cep = !empty($value->address_cep) ? $value->address_cep : "--";
                    $objTeachers->address_city_origin = !empty($value->address_city_origin) ? $value->address_city_origin : "--";
    
                    $content[] = $objTeachers;
                }
            }
    
            $response["error"] = empty($content);
            $response["msg"] = empty($content) ? "Nenhum {$this->name} encontrado." : $content;
        } catch (\Throwable $th) {
            $response["error"] = true;
            $response["msg"] = $th->getMessage();
        }

        return $response;
    }

    public function getToEdit()
    {
        try {
            $idTeacher = $_POST["ref"];
            $result = self::getTeacher($idTeacher);
            if(is_object($result)){
                $result->qualification = self::getQualificationByTeacher($idTeacher);
            }
    
            $response["error"] = !is_object($result);
            $response["msg"] = $result;
        } catch (\Throwable $th) {
            $response["error"] = true;
            $response["msg"] = $th->getMessage();
        }

        return $response;
    }

    public function add()
    {
        try {
            $connection = Transaction::get();
            $data = [
                "name" => $connection->quote($_POST["full_name"])
                ,"birth_date" => $connection->quote($_POST["birth_date"])
                ,"rg_number" => $connection->quote($_POST["number_rg"])
                ,"cpf_number" => $connection->quote($_POST["number_cpf"])
                ,"email_address" => $connection->quote($_POST["email_address"])
                ,"phone" => $connection->quote($_POST["phone_number"])
                ,"gender" => $connection->quote($_POST["gender"])
                ,"address" => $connection->quote($_POST["address"])
                ,"address_number" => $connection->quote($_POST["address_number"])
                ,"address_district" => $connection->quote($_POST["address_district"])
                ,"address_city" => $connection->quote($_POST["address_city"])
                ,"address_state" => $connection->quote($_POST["address_state"])
                ,"address_cep" => $connection->quote($_POST["address_cep"])
                ,"address_city_origin" => $connection->quote($_POST["address_city_origin"])
                ,"flg_active" => 1
                ,"created_at" => "NOW()"
            ];
            $resultInsertTeacher = Connection::insert_data("t_teachers", $data);
    
            if(!$resultInsertTeacher["error"]) {
    
                $data = [];
                if ( isset($_FILES['qualification_document']['name']) ) {
                    $full_name = !empty($_POST["full_name"]) ? strtolower(str_replace(" ", "_", $_POST["full_name"])) : "";
                    $file = self::uploadFile('qualification_document', $full_name);
                }
    
                foreach ($_POST["qualification_name"] as $key => $value) {
                    $documentFilePath = !empty($file[$key]) ? str_replace("/var/www/html/", "", $file[$key]) : "";
                    $data[] = [
                        "teacher_id" => $resultInsertTeacher["id"]
                        ,"name" => $connection->quote($_POST["qualification_name"][$key])
                        ,"level" => $connection->quote($_POST["qualification_level"][$key])
                        ,"institution_name" => $connection->quote($_POST["qualification_institution"][$key])
                        ,"country" => $connection->quote($_POST["qualification_country"][$key])
                        ,"state" => $connection->quote($_POST["qualification_state"][$key])
                        ,"started_at" => $connection->quote($_POST["qualification_started"][$key])
                        ,"end_at" => $connection->quote($_POST["qualification_end"][$key])
                        ,"flg_concluded" => $_POST["qualification_concluded"][$key]
                        ,"document_file_path" => $connection->quote($documentFilePath)
                        ,"flg_active" => 1
                        ,"created_at" => "NOW()"
                    ];
                }
    
                $resultInsertQualification = Connection::insert_data_multi("t_qualifications", $data);
            }
    
            $teacher = !empty($_POST["full_name"]) ? explode(" ", $_POST["full_name"])[0] : "docente";
            $response["error"] = $resultInsertTeacher["error"] || $resultInsertQualification["error"];
            $response["msg"] = $response["error"] ? "Erro ao inserir {$teacher}." : "Sucesso ao cadastrar {$teacher}";
            $response["json"] = $response["error"] ? "" : self::getTeacher($resultInsertTeacher["id"]);
        } catch (\Throwable $th) {
            $response["error"] = true;
            $response["msg"] = $th->getMessage();
        }

        return $response;
    }

    public function edit()
    {
        try {
            $connection = Transaction::get();
            $teacherID = $_POST["ref"];
            $data = [
                "id" => $connection->quote($teacherID)
                ,"name" => $connection->quote($_POST["full_name"])
                ,"birth_date" => $connection->quote($_POST["birth_date"])
                ,"rg_number" => $connection->quote($_POST["number_rg"])
                ,"cpf_number" => $connection->quote($_POST["number_cpf"])
                ,"email_address" => $connection->quote($_POST["email_address"])
                ,"phone" => $connection->quote($_POST["phone_number"])
                ,"gender" => $connection->quote($_POST["gender"])
                ,"address" => $connection->quote($_POST["address"])
                ,"address_number" => $connection->quote($_POST["address_number"])
                ,"address_district" => $connection->quote($_POST["address_district"])
                ,"address_city" => $connection->quote($_POST["address_city"])
                ,"address_state" => $connection->quote($_POST["address_state"])
                ,"address_cep" => $connection->quote($_POST["address_cep"])
                ,"address_city_origin" => $connection->quote($_POST["address_city_origin"])
                ,"flg_active" => 1
                ,"created_at" => "NOW()"
            ];
            $resultEditTeacher = Connection::edit_data("t_teachers", $data);
    
            if(!$resultEditTeacher["error"]) {
    
                $data = [];
                $file = [];
                if ( isset($_FILES['qualification_document']['name']) ) {
                    $full_name = !empty($_POST["full_name"]) ? strtolower(str_replace(" ", "_", $_POST["full_name"])) : "";
                    $file = self::uploadFile('qualification_document', $full_name);
                }

                foreach ($_POST["qualification_name"] as $key => $value) {
                    $documentFilePath = "";
                    if(isset($_FILES['qualification_document']['name'][$key])){
                        if(isset($file[$key]) && !empty($file[$key])){
                            $documentFilePath =  str_replace("/var/www/html/", "", $file[$key]);
                        }
                    }

                    $data = [
                        "id" => $connection->quote($_POST["qualification_ref"][$key])
                        ,"teacher_id" => $connection->quote($teacherID)
                        ,"name" => $connection->quote($_POST["qualification_name"][$key])
                        ,"level" => $connection->quote($_POST["qualification_level"][$key])
                        ,"institution_name" => $connection->quote($_POST["qualification_institution"][$key])
                        ,"country" => $connection->quote($_POST["qualification_country"][$key])
                        ,"state" => $connection->quote($_POST["qualification_state"][$key])
                        ,"started_at" => $connection->quote($_POST["qualification_started"][$key])
                        ,"end_at" => $connection->quote($_POST["qualification_end"][$key])
                        ,"flg_concluded" => $_POST["qualification_concluded"][$key]
                        ,"flg_active" => 1
                        ,"created_at" => "NOW()"
                    ];

                    if(!empty($documentFilePath)) {
                        $data["document_file_path"] = $connection->quote($documentFilePath);
                    }
    
                    $resultInsertQualification = Connection::edit_data("t_qualifications", $data);
                }
    
            }
    
            $teacher = !empty($_POST["full_name"]) ? explode(" ", $_POST["full_name"])[0] : "docente";
            $response["error"] = $resultEditTeacher["error"] || $resultInsertQualification["error"];
            $response["msg"] = $response["error"] ? "Erro ao editar {$teacher}." : "Sucesso ao editar {$teacher}";
            $response["json"] = $response["error"] ? "" : self::getTeacher($teacherID);
        } catch (\Throwable $th) {
            $response["error"] = true;
            $response["msg"] = $th->getMessage();
        }

        return $response;
    }

    public function delete()
    {
        try {
            $id = isset($_POST["ref"]) && !empty($_POST["ref"]) ? intval($_POST["ref"]) : "";
            if(empty($id)) {
                $response["error"] = true;
                $response["msg"] = "Usuário não encontrado!";
                return $response;
            }

            $result = Connection::delete_register("t_teachers", $id);
            if(!$result["error"]){
                $query = "UPDATE t_qualifications SET flg_active = 0, deleted_at = NOW() WHERE teacher_id = {$id}";
                $result = Connection::execute($query);
            }

            $response["error"] = $result["error"];
            $response["msg"] = $result["error"] ? "Erro ao excluir docente!" : "Docente excluido com sucesso!";
        } catch (\Throwable $th) {
            $response["error"] = true;
            $response["msg"] = $th->getMessage();
        }

        return $response;
    }

    public function deleteFileQualification()
    {
        try {
            $id = isset($_POST["ref"]) && !empty($_POST["ref"]) ? intval($_POST["ref"]) : "";
            if(empty($id)) {
                $response["error"] = true;
                $response["msg"] = "Fomação não encontrada!";
                return $response;
            }

            $directory = isset($_POST["path"]) && !empty($_POST["path"]) ? "/var/www/html/{$_POST["path"]}" : "";
            $result = Upload::deleteFile($directory);
            if(!$result["error"]){
                $data = [
                    "id" => $id
                    ,"document_file_path" => "NULL"
                    ,"updated_at" => "NOW()"
                ];
                $result = Connection::edit_data("t_qualifications", $data);
            }

            $response["error"] = $result["error"];
            $response["msg"] = $result["error"] ? "Erro ao excluir arquivo!" : "Arquivo excluido com sucesso!";
        } catch (\Throwable $th) {
            $response["error"] = true;
            $response["msg"] = $th->getMessage();
        }

        return $response;
    }

    private static function uploadFile($input, $name)
    {
        $objManipulaUpload = new Upload(
            $input,
            $name,
            "/var/www/html/sfsete/views/upload",
            ['pdf']
        );
        $arrArquivos = $objManipulaUpload->controller();

        $file = [];
        foreach ($arrArquivos as $key => $value) {
            $file[] = $value->getDiretorio();
        }

        return $file;
    }

    private static function getTeacher($id)
    {
        $query = "SELECT 
                teachers.id
                ,teachers.name
                ,teachers.birth_date
                ,teachers.rg_number
                ,teachers.cpf_number
                ,teachers.email_address
                ,teachers.phone
                ,teachers.gender
                ,teachers.address
                ,teachers.address_number
                ,teachers.address_district
                ,teachers.address_city
                ,teachers.address_state
                ,teachers.address_cep
                ,teachers.address_city_origin
                ,GROUP_CONCAT(
                    qualifications.name
                    ORDER BY qualifications.id
                    SEPARATOR ' | '
                ) AS qualifications
            FROM t_teachers AS teachers
            INNER JOIN t_qualifications AS qualifications
                ON qualifications.teacher_id = teachers.id
                AND qualifications.flg_active = 1
            WHERE
                teachers.flg_active = 1
                AND teachers.id = {$id}
            GROUP BY teachers.id";
        $result = Connection::search($query);

        if(!$result["error"]){

            $content = "";
            foreach ($result["msg"] as $key => $value) {
                $objTeachers = new stdClass();

                $objTeachers->id = !empty($value->id) ? $value->id : "";
                $objTeachers->ref = $objTeachers->id;
                $objTeachers->name = !empty($value->name) ? $value->name : "--";
                $objTeachers->full_name = $objTeachers->name;
                $objTeachers->qualifications = !empty($value->qualifications) ? $value->qualifications : "--";
                $objTeachers->birth_date = !empty($value->birth_date) ? $value->birth_date : "--";
                $objTeachers->birth_date_br = !empty($value->birth_date) ? date("d/m/Y", strtotime($value->birth_date)) : "--";
                $objTeachers->rg_number = !empty($value->rg_number) ? $value->rg_number : "--";
                $objTeachers->number_rg = $objTeachers->rg_number;
                $objTeachers->cpf_number = !empty($value->cpf_number) ? $value->cpf_number : "--";
                $objTeachers->number_cpf = $objTeachers->cpf_number;
                $objTeachers->email_address = !empty($value->email_address) ? $value->email_address : "--";
                $objTeachers->phone = !empty($value->phone) ? $value->phone : "--";
                $objTeachers->phone_number = $objTeachers->phone;
                $objTeachers->gender = !empty($value->gender) ? $value->gender : "--";
                $objTeachers->address = !empty($value->address) ? $value->address : "--";
                $objTeachers->address_number = !empty($value->address_number) ? $value->address_number : "--";
                $objTeachers->address_district = !empty($value->address_district) ? $value->address_district : "--";
                $objTeachers->address_city = !empty($value->address_city) ? $value->address_city : "--";
                $objTeachers->address_state = !empty($value->address_state) ? $value->address_state : "--";
                $objTeachers->address_cep = !empty($value->address_cep) ? $value->address_cep : "--";
                $objTeachers->address_city_origin = !empty($value->address_city_origin) ? $value->address_city_origin : "--";

                $content = $objTeachers;
            }
        }

        $response = empty($content) ? "Docente não encontrado." : $content;
        return $response;
    }

    private static function getQualificationByTeacher($idTeacher)
    {
        $query = "SELECT
                id
                ,name
                ,level
                ,institution_name
                ,country
                ,state
                ,started_at
                ,end_at
                ,flg_concluded
                ,document_file_path
            FROM
                t_qualifications 
            WHERE
                flg_active = 1
                AND teacher_id = {$idTeacher}";
        $result = Connection::search($query);

        if(!$result["error"]){

            $content = [];
            foreach ($result["msg"] as $key => $value) {
                $objTeachers = new stdClass();

                $objTeachers->id = !empty($value->id) ? $value->id : "";
                $objTeachers->qualification_ref = $objTeachers->id;

                $objTeachers->name = !empty($value->name) ? $value->name : "";
                $objTeachers->qualification_name = $objTeachers->name;

                $objTeachers->level = !empty($value->level) ? $value->level : "";
                $objTeachers->qualification_level = $objTeachers->level;

                $objTeachers->institution_name = !empty($value->institution_name) ? $value->institution_name : "";
                $objTeachers->qualification_institution = $objTeachers->institution_name;

                $objTeachers->country = !empty($value->country) ? $value->country : "";
                $objTeachers->qualification_country = $objTeachers->country;

                $objTeachers->state = !empty($value->state) ? $value->state : "";
                $objTeachers->qualification_state = $objTeachers->state;

                $objTeachers->started_at = !empty($value->started_at) ? date("Y-m-d", strtotime($value->started_at)) : "";
                $objTeachers->qualification_started = $objTeachers->started_at;

                $objTeachers->end_at = !empty($value->end_at) ? date("Y-m-d", strtotime($value->end_at)) : "";
                $objTeachers->qualification_end = $objTeachers->end_at;

                $objTeachers->flg_concluded = !empty($value->flg_concluded) ? $value->flg_concluded : "0";
                $objTeachers->qualification_concluded = $objTeachers->flg_concluded;

                $objTeachers->document_file_path = !empty($value->document_file_path) ? $value->document_file_path : "";
                $objTeachers->qualification_document = $objTeachers->document_file_path;

                $content[] = $objTeachers;
            }
        }

        $response = $content;
        return $response;
    }

}


?>