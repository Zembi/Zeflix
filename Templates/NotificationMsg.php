<?php

namespace Templates;

class NotificationMsg {
    public static function successfulNotify(string $msg = ''): void {
        ?>
            <div class="notification-container successful-notify">
                <span class="msg"><?= $msg ?></span>
            </div>
        <?php
    }

    public static function errorNotify(string $msg = ''): void {
        ?>
        <div class="notification-container error-notify">
            <span class="msg"><?= $msg ?></span>
        </div>
        <?php
    }
}