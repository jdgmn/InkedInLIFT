<form action="checkin_checkout.php" method="POST" id="checkin-form">
    <div class="comp-container">
        <input type="text" id="checkin-name" placeholder="Customer Name" autocomplete="off" required>
        <input type="hidden" name="membership_id" id="checkin-membership-id" required>
        <div id="autocomplete-list" class="autocomplete-items"></div>
        <button type="submit" class="checkin">Check-in</button>
    </div>
</form>