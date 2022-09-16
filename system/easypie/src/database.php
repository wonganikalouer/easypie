<?php
    namespace EaseRoutes;

    use stdClass;

    class DatabaseConnection{
        var $host = "localhost";
        var $username = "root";
        var $password = "";
        var $database = "";
        var $mysqlLink;

        var $TABLE;
        public function __construct(){
            //automatically switch from localhost to server based
            $this->host = Settings::get("HOST");
            $this->username = Settings::get("USERNAME","");
            $this->password = Settings::get("PASSWORD","");
            $this->database = Settings::get("DATABASE","");
        }

        private function con(){
            $this->mysqlLink = mysqli_connect($this->host,$this->username,$this->password,$this->database);
            return $this->mysqlLink;//we did this to fetch the error easily
        }

        /**
         * @param string $limit eg. "LIMIT=4"
         */
        public function select($limit = ""){
            return $this->fetchAll($this->query("SELECT * FROM $this->TABLE $limit"));
        }
        
        /**
         * @param string $whereClause tells which column should be selected with filter eg.
         * user_id="1" or user_email="alfred@khusco.com"
         */
        public function selectWhere($whereClause)
        {
            return $this->fetchAll($this->query("SELECT * FROM $this->TABLE WHERE $whereClause"));
        }

        /**
         * @param string $tableName = tableName to join to
         * @param array $data = an array of all rows to join to eg. ["user_id"=>"user_id"]
         * the second argument or value is always for the other table
         * @param string $joinType default at LEFT JOIN, you can also use INNER, RIGHT, LEFT OUTER
         * @param string $whereClause defaul 1, if you have any where clause eg. user_id=1
         */
        public function selectJoin($otherTable, $data=[], $joinType="LEFT", $whereClause="1"){
            $sql = "SELECT * FROM $this->TABLE $joinType JOIN $otherTable ON ";
            foreach ($data as $key => $value) {
                $sql .=$this->TABLE.".$key = $otherTable.$value";
            }
            $sql .=" WHERE $whereClause";
            return $this->fetchAll($this->query($sql));
        }

        /**
         * @param array $data a well matched dataset for this table in an array form,
         * make sure that the keys matches that of the table column names, otherwise...
         */
        public function insert($data=[]){
            return $this->query($this->buildQuery($data, $this->TABLE)) or die(false);
        }

        /**
         * @param array $constraint
         */
        public function delete($constraint=[]){
            $sql = "DELETE FROM $this->TABLE WHERE ";
            foreach ($constraint as $key => $value) {
                $sql .="$key = $value ";
            }
            $this->query($sql);
        }

        public function query($sql){
            return $this->con()->query($sql);
        }

        public function fetch($results){
            return mysqli_fetch_array($results);
        }

        public function fetchAll($results){
            $array= [];
            while($row = $this->fetch($results)){
                array_push($array,$row);
            }
            return $array;
        }

        public function fetchOne($results){
            return mysqli_fetch_array($results)[0];
        }

        public function fetchLast($results){
            //will work later on this one
            $numberOfRows   =   mysqli_num_rows($results);
            return 0;
        }

        public function lastInsertID($results){
            return $this->mysqlLink->insert_id;
        }

        public function buildQuery($array,$tableName){
            if(!is_array($array)){
                //assuming its a json string
                $array = json_decode($array,true);
            }
            $sqlBuild   =   "INSERT INTO $tableName";
            $columns    =   array();
            $values     =   array();
            foreach ($array as $key => $value) {
                array_push($columns,$key);
                array_push($values,"\"".$value."\"");
            }
            $sqlBuild.=" (".implode(",",$columns).") VALUES (".implode(",",$values).")";
            return $sqlBuild;
        }

        /**
         * @param array $data Is the data to be updated, make sure that they match the exact columns from the class table name
         * @param string $condition default = 1 if ignored, all rows will be updated, so place the exact conditions that has to be met before updating, eg. user_id=1
         * @return array or Error
         */
        public function update($data,$condition="1")
        {
            $array = $data;
            if(!is_array($data)){
                //assuming its a json string
                $array = json_decode($data,true);
            }
            $sqlBuild   =   "UPDATE $this->TABLE SET ";
            $i=-1;
            foreach ($array as $key => $value) {
                // array_push($columns,$key);
                // array_push($values,"\"".$value."\"");
                $i++;
                if($i==0){
                    $sqlBuild.="$key=\"$value\" ";
                }else{
                    $sqlBuild.=", $key=\"$value\" ";
                }
            }
            $sqlBuild.=" WHERE $condition";
            return $this->query($sqlBuild) or die($this->getError());
        }

        public function buildInsertQuery($array)
        {
            # this one will be used for anyonymous tables
            $sqlBuild   =   "";
            $columns    =   array();
            $values     =   array();
            foreach ($array as $key => $value) {
                array_push($columns,$key);
                array_push($values,"\"".$value."\"");
            }
            $sqlBuild.=" (".implode(",",$columns).") VALUES (".implode(",",$values).")";
            return $sqlBuild;
        }

        public function getError(){
            return $this->mysqlLink->error;
        }

        /**
         * @param int $countPerPage defaulted at 12 as the number of items to show per page
         * @param string $pageIndicator is a _GET parameter with defaulted to 'page'
         * @return stdClass $pageData [->currentPage:int, ->results:array]
         */
        public function paginate($countPerPage=12, $pageIndicator="page", $filter="1")
        {
            //man this is very easy mwee
            $totalItems = count($this->selectWhere($filter));
            $numberofPage = ceil($totalItems/$countPerPage);
            $page = 1;
            if(!isset($_REQUEST[$pageIndicator])){
                $page=1;
            }else{
                $page = $_REQUEST[$pageIndicator];
            }

            $pageResults = ($page-1) * $countPerPage;

            $query = "SELECT * FROM $this->TABLE WHERE $filter LIMIT ".$pageResults.",".$countPerPage;

            $pageData = new stdClass;
            $pageData->currentPage = $page;
            $pageData->numberOfPages = $numberofPage;
            $pageData->results = $this->fetchAll($this->query($query));
            $pageData->totalRows = $totalItems;
            return $pageData;
        }
    }
?>