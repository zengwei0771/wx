<?php

define('DB_HOST','127.0.0.1');            //服务器
define('DB_USER','root');                 //数据库用户名
define('DB_PASSWORD','12qwaszx');         //数据库密码
define('DB_NAME','wx');          //默认数据库
define('DB_CHARSET','utf8mb4');              //数据库字符集

class DB
{
    public $host;            //服务器
    public $username;        //数据库用户名
    public $password;        //数据密码
    public $dbname;          //数据库名
    public $conn;            //数据库连接变量

    // DB类构造函数
    public function DB($host=DB_HOST ,$username=DB_USER,$password=DB_PASSWORD,$dbname=DB_NAME)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;

    }

    // 打开数据库连接
    public function open()
    {
        $this->conn = mysql_connect($this->host,$this->username,$this->password);
        mysql_select_db($this->dbname);
        mysql_query("SET CHARACTER SET utf8");
    }

    // 关闭数据连接
    public function close()
    {
        mysql_close($this->conn);
    }

    // 通过sql语句获取数据
    // @return: array()
    public function getObjListBySql($sql)
    {
        $this->open();
        $rs = mysql_query($sql,$this->conn);
        $objList = array();
        while($obj = mysql_fetch_object($rs))
        {
            if($obj)
            {
                $objList[] = $obj;
            }
        }
        $this->close();
        return $objList;
    }

    // 向数据库表中插入数据
    // @param：$table,表名
    // @param：$columns,包含表中所有字段名的数组。默认空数组，则是全部有序字段名
    // @param：$values,包含对应所有字段的属性值的数组
    public function insertData($table,$columns=array(),$values=array())
    {
        $sql = 'insert into '.$table .'( ';
        for($i = 0; $i < sizeof($columns);$i ++)
        {
            $sql .= $columns[$i];
            if($i < sizeof($columns) - 1)
            {
                $sql .= ',';
            }
        }
        $sql .= ') values ( ';
        for($i = 0; $i < sizeof($values);$i ++)
        {
            $sql .= "'".$values[$i]."'";
            if($i < sizeof($values) - 1)
            {
                $sql .= ',';
            }
        }
        $sql .= ' )';
        $this->open();
        mysql_query($sql,$this->conn);
        $id = mysql_insert_id($this->conn);
        $this->close();
        return $id;
    }

    // 通过表中的某一属性获取数据个数
    public function getCountByAtr($tableName,$atrName='',$atrValue=''){
        if ($atrName and $atrValue) {
            $sql = "SELECT count(*) as count FROM `".$tableName."` WHERE `$atrName` = '$atrValue'";
        } else {
            $sql = "SELECT count(*) as count FROM `".$tableName."`";
        }
        @$data = $this->getObjListBySql($sql);
        if(count($data)!=0)return $data[0]->count;
        return NULL;
    }

    // 通过表中的某一属性获取数据
    public function getDataByAtr($tableName,$atrName='',$atrValue='',$offset=-1,$limit=-1){
        if ($atrName) {
            $sql = "SELECT * FROM `".$tableName."` WHERE `$atrName` = '$atrValue'";
        } else {
            $sql = "SELECT * FROM `".$tableName."`";
        }
        if ($offset != -1 and $limit != -1) {
            $sql = $sql.' limit '.$offset.', '.$limit;
        }
        @$data = $this->getObjListBySql($sql);
        if(count($data)!=0)return $data;
        return NULL;
    }

    // 通过表中的"id"，删除记录
    public function del($tableName,$atrName,$atrValue){
        $this->open();
        $deleteResult = false;
        if(mysql_query("DELETE FROM `".$tableName."` WHERE `$atrName` = '$atrValue'")) $deleteResult = true;
        $this->close();
        if($deleteResult) return true;
        else return false;
    }

    // 更新表中的属性值
    public function updateParamById($tableName,$atrName,$atrValue,$key,$value){
        $db = new DB();
        $db->open();
        if(mysql_query("UPDATE `".$tableName."` SET `$key` = '$value' WHERE `$atrName` = '$atrValue' ")){  //$key不要单引号
            $db->close();
            return true;
        }
        else{
            $db->close();
            return false;
        }
    }

    // @description: 取得一个table的所有属性名
    // @param: $tbName 表名
    // @return：字符串数组
    public function fieldName($tbName){
        $resultName=array();
        $i=0;
        $this->open();
        $result = mysql_query("SELECT * FROM `$tbName`");
        while ($property = mysql_fetch_field($result)){
            $resultName[$i++]=$property->name;
        }
        $this->close();
        return $resultName;
    }
}

$db = new DB(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

?>
