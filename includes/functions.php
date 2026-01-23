<?php
// includes/functions.php -  notification icon function 
function getNotificationIcon($message) {
    if (strpos($message, '📅') !== false) return '📅';
    if (strpos($message, '✅') !== false) return '✅';
    if (strpos($n['message'], '❌') !== false) return '❌';
    if (strpos($n['message'], '💳') !== false) return '💳';
    if (strpos($n['message'], '🏨') !== false) return '🏨';
    if (strpos($n['message'], '⚠️') !== false) return '⚠️';
    if (strpos($n['message'], '💬') !== false) return '💬';
    return '🔔';
}
?>