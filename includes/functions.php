<?php
// includes/functions.php - শুধুমাত্র notification icon function থাকবে
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