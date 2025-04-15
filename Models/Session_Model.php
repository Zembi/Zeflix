<?php

namespace Models;

use Items\User;
use Items\Page\Page;
use Tools\HandleInternalMsgs;

use \PDO;
use \PDOException;

class Session_Model extends Model {
    public function handle_user_session_token(?string $token, User $user, Page $currPage): array {
        $username = $user->getUsername();

        $this->setGreekTimezoneBeforeQuery();

        if(isset($token) && $token && trim($token) == '') {
            $response = $this->confirm_user_from_session_token($user, $token);
//            IF TOKEN EXISTS AND IS CONNECTED WITH USERNAME
            if($response['status']) {
                $updateQuery = $this->db->prepare("
                    UPDATE session 
                    SET created_at = NOW(), last_visited_page = :last_visited_page 
                    WHERE token = :token
                ");
                $updateQuery->execute([
                    ":last_visited_page" => $currPage->getName(),
                    ":token" => $token,
                ]);

                return HandleInternalMsgs::succesMsgOnReturn(['token' => $token]);
            }
//            IF TOKEN DOESN'T EXIST CONTINUE
//            $deleteQuery = $this->db->prepare("DELETE FROM session WHERE username = :username");
//            $deleteQuery->execute([":username" => $username]);
        }

//        GENERATE NEW TOKEN FOR USER SESSION
        while(true) {
            try {
                $newToken = bin2hex(random_bytes(15));

                $insertQuery = $this->db->prepare("
                    INSERT INTO session (username, token, last_visited_page, created_at) 
                    VALUES (:username, :token, :last_visited_page, NOW())
                ");
                $insertQuery->execute([
                    ":username" => $username,
                    ":token" => $newToken,
                    ":last_visited_page" => $currPage->getName()
                ]);

                return HandleInternalMsgs::succesMsgOnReturn(['token' => $newToken]);

            }
            catch(PDOException $e) {
                if ($e->getCode() == 23000) {
                    continue;
                }
                else {
                    return HandleInternalMsgs::errorMsgOnReturn(['msg' => $e->getMessage()]);
                }
            }
        }
    }

    public function delete_user_session_token(?string $token, User $user): array {
        if(!$token) {
            return HandleInternalMsgs::errorMsgOnReturn(['msg' => 'Invalid token']);
        }

        $deleteQuery = $this->db->prepare("
            DELETE FROM session
            WHERE username = :username 
            AND token = :token
        ");
        $deletedResponse = $deleteQuery->execute([
            ":username" => $user->getUsername(),
            ":token" => $token
        ]);

        if($deletedResponse && $deleteQuery->rowCount() > 0) {
            return HandleInternalMsgs::succesMsgOnReturn(['msg' => 'UserModel token has been deleted']);
        }

        return HandleInternalMsgs::errorMsgOnReturn(['msg' => 'UserModel with given token has not been found']);
    }

    public function confirm_user_from_session_token(User $user, string $token): array {
        $response = $this->retrieve_user_from_session_token($token);
        if($response['status']) {
            $username = $response['response']['username'];
            if($username == $user->getUsername()) {
                return HandleInternalMsgs::succesMsgOnReturn(['msg' => 'Username matches with token']);
            }
            return HandleInternalMsgs::errorMsgOnReturn(['msg' => 'Username does not match with token']);
        }
        return HandleInternalMsgs::errorMsgOnReturn(['msg' => 'Invalid token']);
    }

    public function retrieve_user_from_session_token(string $token): array {
        $checkQuery = $this->db->prepare("
            SELECT username
            FROM session
            WHERE token = :token
        ");
        $checkQuery->execute([":token" => $token]);
        $usernameFound = $checkQuery->fetch(PDO::FETCH_ASSOC);

        if($usernameFound) {
            return HandleInternalMsgs::succesMsgOnReturn($usernameFound);
        }
        return HandleInternalMsgs::errorMsgOnReturn(['msg' => 'Invalid token']);
    }
}