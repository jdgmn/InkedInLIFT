<?php
// calculates the remaining time of a membership
function getRemainingTime(DateTimeImmutable $end): string {
    $now = new DateTimeImmutable();
    if ($now > $end) return 'Expired';

    $diff = $now->diff($end);
    $months = $diff->y * 12 + $diff->m;
    $days = $diff->d;

    return ($months ? "$months month(s) " : "") . ($days ? "$days day(s)" : "");
}
?>
