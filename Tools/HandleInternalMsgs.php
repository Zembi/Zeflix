<?php

namespace Tools;

class HandleInternalMsgs {
    public static function succesMsgOnReturn(array $data = []): array {
        return HandleInternalMsgs::handleMsgOnReturn(1, $data);
    }

    public static function errorMsgOnReturn(array $data = []): array {
        return HandleInternalMsgs::handleMsgOnReturn(0, $data);
    }

    private static function handleMsgOnReturn(int $status, array $data): array {
        return [
            'status' => $status,
            'response' => count($data) == 0 ? 'Internal error' : $data
        ];
    }
}