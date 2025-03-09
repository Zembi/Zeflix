<?php

class Session_Model extends Model {
    public function handle_user_session_token(?string $token, User $user, Page $currPage): ?string {
        $username = $user->getUsername();
        $newToken = bin2hex(random_bytes(15));

        var_dump($token);

        if($token) {
            $checkQuery = $this->db->prepare("SELECT token FROM session WHERE username = :username");
            $checkQuery->execute([":username" => $username]);
            $existingToken = $checkQuery->fetchColumn();

            if($existingToken) {
                $updateQuery = $this->db->prepare("UPDATE session SET created_at = NOW(), last_visited_page = :last_visited_page WHERE token = :token");
                $updateQuery->execute([
                    ":last_visited_page" => $currPage->getName(),
                    ":token" => $existingToken,
                ]);

                return $existingToken;
            }

            $deleteQuery = $this->db->prepare("DELETE FROM session WHERE username = :username");
            $deleteQuery->execute([":username" => $username]);
        }

        $insertQuery = $this->db->prepare("INSERT INTO session (username, token, last_visited_page, created_at) VALUES (:username, :token, :last_visited_page, NOW())");
        $executedIns = $insertQuery->execute([
            ":username" => $username,
            ":token" => $newToken,
            ":last_visited_page" => $currPage->getName()
        ]);

        if($executedIns) {
            return $newToken;
        }

        return null;
    }

    public function confirm_user_session_token(string $token): ?string {
        $checkQuery = $this->db->prepare("SELECT username FROM session WHERE token = :token");
        $checkQuery->execute([":token" => $token]);
        return $checkQuery->fetchColumn();
    }
}