<?php

class Data {
    public function __construct() {}

    public static function getLevels()
    {
        $query = "SELECT id, name
        FROM t_levels
        WHERE flg_active = 1";
        $result = Connection::search($query);

        $content = [];
        if(!$result["error"]){
            foreach($result["msg"] as $key => $value)
            {
                $obj = new stdClass();

                $obj->id = $value->id;
                $obj->name = $value->name;

                $content[] = $obj;
            }
        }

        return $content;
    }

    public static function getCities()
    {
        $query = "SELECT id, name
        FROM t_cities
        WHERE flg_active = 1";
        $result = Connection::search($query);

        $content = [];
        if(!$result["error"]){
            foreach($result["msg"] as $key => $value)
            {
                $obj = new stdClass();

                $obj->id = $value->id;
                $obj->name = $value->name;

                $content[] = $obj;
            }
        }

        return $content;
    }

}
?>
