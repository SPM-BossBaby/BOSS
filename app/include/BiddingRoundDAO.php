<?php

class BiddingRoundDAO {
    
    public function retrieveAll() {
        $sql = 'select * from bidding_round';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();  

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new BiddingRound($row['roundNo'], $row['active'], $row['start'], $row['end']);
        }
        return $result;
    }

    public function startRound($biddingRound) {
        $sql = 'INSERT IGNORE INTO bidding_round (roundNo, active, start, end) VALUES (:roundNo, :active, :start, :end)';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':roundNo', $biddingRound->roundNo, PDO::PARAM_STR);
        $stmt->bindParam(':active', $biddingRound->active, PDO::PARAM_BOOL);
        $stmt->bindParam(':start', $biddingRound->start, PDO::PARAM_STR);
        $stmt->bindParam(':end', $biddingRound->end, PDO::PARAM_STR);
        
        $isStartOK = False;
        if ($stmt->execute()) {
            $isStartOK = True;
        }

        return $isStartOK;
    }

    public function endRound() {
        $sql = 'UPDATE bidding_round SET active=:newactive, end=:end WHERE active <> 0';;
    
        $connMgr = new ConnectionManager();           
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $isactive = 0;
        $currdate = date('Y-m-d H:i:s');

        $stmt->bindParam(':newactive', $isactive, PDO::PARAM_INT);
        $stmt->bindParam(':end', $currdate, PDO::PARAM_STR);

        $isEndOK = FALSE;
        if ($stmt->execute()) {
            $isEndOK = TRUE;
        }

        return $isEndOK;
    
    }

    public function activeRound() {
        $sql = 'SELECT * FROM bidding_round WHERE active <> 0';

        $connMgr = new ConnectionManager();           
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        if (!($stmt->execute())) {
            return FALSE;
        }

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new BiddingRound($row['roundNo'], $row['active'], $row['start'], $row['end']);
        }
    }

    public function getLastRound() {
        $sql = 'select max(roundNo) from bidding_round';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result["max(roundNo)"];
    }

    public function getCurrentRound() {
        $sql = 'SELECT * FROM bidding_round ORDER BY roundNo DESC LIMIT 1';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return new BiddingRound($result["roundNo"], $result['active'], $result['start'], $result['end']);
    }

    public function removeAll() {
        $sql = 'SET FOREIGN_KEY_CHECKS=0; TRUNCATE TABLE bidding_round; SET FOREIGN_KEY_CHECKS=1;';
        
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}

?>