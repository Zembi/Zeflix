<?php

class Session_Model extends Model {
    public function handle_user_session_token(?string $token, User $user): ?string {
        $username = $user->getUsername();
        $newToken = bin2hex(random_bytes(15));

        $checkQuery = $this->db->prepare("SELECT token FROM session WHERE username = :username");
        $checkQuery->execute([":username" => $username]);
        $existingToken = $checkQuery->fetchColumn();

        if($existingToken) {
            $updateQuery = $this->db->prepare("UPDATE session SET created_at = NOW() WHERE token = :token");
            $updateQuery->execute([":token" => $existingToken]);

            return $existingToken;
        }

        $deleteQuery = $this->db->prepare("DELETE FROM session WHERE username = :username");
        $deleteQuery->execute([":username" => $username]);

        $insertQuery = $this->db->prepare("INSERT INTO session (username, token, created_at) VALUES (:username, :token, NOW())");
        $executedIns = $insertQuery->execute([
            ":username" => $username,
            ":token" => $newToken
        ]);

        if($executedIns) {
            return $newToken;
        }

        return $token;
    }
}