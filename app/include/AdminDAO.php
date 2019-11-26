<?php

class AdminDAO {

    public function getAdmin($userid) {
        $sql = 'select * from admin where BINARY userid=:userid';

        $result = array();
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
    
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();
        while($row = $stmt->fetch()) {
            $result[] = new Admin($row['userid'], $row['password'], $row['name']);
        }

        return $result;
    }
}

?>