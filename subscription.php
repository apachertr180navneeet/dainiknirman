<?php

$pageTitle = 'Subscription - NIRMA PRAKASHAN';
$activePage = 'books';

require 'db_config.php';

$userId = $_GET['user_id'] ?? null;

if (!$userId) {
    header('Location: index.html?error=user_not_found');
    exit;
}

$userStmt = $conn->prepare("SELECT name, email, mobile, address FROM users WHERE id = ?");
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userData = $userResult->fetch_assoc();
$userStmt->close();

$userName = $userData['name'] ?? '';
$userEmail = $userData['email'] ?? '';
$userPhone = $userData['mobile'] ?? '';
$userAddress = $userData['address'] ?? '';

$nameParts = explode(' ', $userName, 2);
$firstName = $nameParts[0] ?? '';
$lastName = $nameParts[1] ?? '';

$subsResult = $conn->query("SELECT * FROM subscriptions WHERE status = 1 AND deleted_at IS NULL ORDER BY amount ASC");
$subscriptions = [];
if ($subsResult && $subsResult->num_rows > 0) {
    while ($row = $subsResult->fetch_assoc()) {
        $subscriptions[] = $row;
    }
}

require 'header.php';
?>

    <main class="checkout-main">
      <div class="checkout-container">
        <h1 class="checkout-title">Choose Your Subscription Plan</h1>

        <div class="checkout-grid">
          <div class="checkout-form-section">
            <form class="checkout-form" id="subscriptionForm" onsubmit="placeOrder(event)">

              <div class="form-section">
                <h2 class="form-section-title">Select Plan</h2>

                <?php if (empty($subscriptions)): ?>
                  <p style="color: #9ca3af; padding: 1rem 0;">No subscription plans available at the moment.</p>
                <?php else: ?>
                  <?php foreach ($subscriptions as $index => $sub): ?>
                    <?php
                      $checked = $index === 0 ? 'checked' : '';
                      $typeLabel = $sub['type'] === 'AUTHOR' ? 'Author Plan' : 'Reader Plan';
                      $validityLabel = $sub['validity'] . ' ' . ($sub['validity'] > 1 ? 'Months' : 'Month');
                    ?>
                    <label class="payment-option">
                      <input type="radio" name="subscription_id" value="<?= htmlspecialchars($sub['id']) ?>" data-amount="<?= htmlspecialchars($sub['amount']) ?>" <?= $checked ?>>
                      <span class="payment-radio"></span>
                      <div class="payment-content">
                        <span class="payment-title"><?= htmlspecialchars($sub['name']) ?> (<?= $typeLabel ?>)</span>
                        <span class="payment-desc">
                          ₹<?= htmlspecialchars($sub['amount']) ?> &mdash; <?= htmlspecialchars($validityLabel) ?>
                        </span>
                        <?php if (!empty($sub['description'])): ?>
                          <p style="font-size:0.8rem;color:#6b7280;margin-top:4px;line-height:1.4;">
                            <?= htmlspecialchars($sub['description']) ?>
                          </p>
                        <?php endif; ?>
                      </div>
                    </label>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>

              <div class="form-section">
                <h2 class="form-section-title">Billing Details</h2>

                <div class="form-row">
                  <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" placeholder="Enter first name" value="<?= htmlspecialchars($firstName) ?>">
                  </div>
                  <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" placeholder="Enter last name" value="<?= htmlspecialchars($lastName) ?>">
                  </div>
                </div>

                <div class="form-group">
                  <label for="email">Email Address *</label>
                  <input type="email" id="email" name="email" placeholder="Enter email address" value="<?= htmlspecialchars($userEmail) ?>">
                </div>

                <div class="form-group">
                  <label for="phone">Phone Number *</label>
                  <input type="tel" id="phone" name="phone" placeholder="Enter phone number" value="<?= htmlspecialchars($userPhone) ?>">
                </div>

                <div class="form-group">
                  <label for="address">Street Address *</label>
                  <input type="text" id="address" name="address" placeholder="Enter street address" value="<?= htmlspecialchars($userAddress) ?>">
                </div>
              </div>

              <input type="hidden" id="city" name="city" value="Default City">
              <input type="hidden" id="state" name="state" value="Rajasthan">
              <input type="hidden" id="pincode" name="pincode" value="000000">
              <input type="hidden" id="country" name="country" value="India">
              <input type="hidden" id="userId" name="user_id" value="<?= htmlspecialchars($userId) ?>">

              <button type="submit" class="btn btn-primary btn-place-order" <?= empty($subscriptions) ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : '' ?>>Proceed to Pay</button>
            </form>
          </div>

          <div class="order-summary-section">
            <div class="order-summary">
              <h2 class="order-summary-title">Subscription Summary</h2>

              <?php if (!empty($subscriptions)): ?>
                <?php foreach ($subscriptions as $index => $sub): ?>
                  <?php
                    $typeLabel = $sub['type'] === 'AUTHOR' ? 'Author Plan' : 'Reader Plan';
                    $validityLabel = $sub['validity'] . ' ' . ($sub['validity'] > 1 ? 'Months' : 'Month');
                    $display = $index === 0 ? 'block' : 'none';
                  ?>
                  <div class="order-summary-item" data-sub-id="<?= htmlspecialchars($sub['id']) ?>" style="display: <?= $display ?>;">
                    <div class="order-item-details">
                      <h3 class="order-item-name"><?= htmlspecialchars($sub['name']) ?></h3>
                      <p class="order-item-author"><?= htmlspecialchars($typeLabel) ?> &middot; <?= htmlspecialchars($validityLabel) ?></p>
                      <div class="order-item-price">
                        <span class="current-price">₹<?= htmlspecialchars($sub['amount']) ?></span>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>

              <div class="order-totals">
                <div class="order-total-row">
                  <span>Subtotal</span>
                  <span id="totalPrice">₹<?= !empty($subscriptions) ? htmlspecialchars($subscriptions[0]['amount']) : '0' ?></span>
                </div>
                <div class="order-total-row total">
                  <span>Total</span>
                  <span id="totalPriceFinal">₹<?= !empty($subscriptions) ? htmlspecialchars($subscriptions[0]['amount']) : '0' ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <script>
      document.querySelectorAll('input[name="subscription_id"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
          var subId = this.value;
          var amount = this.getAttribute('data-amount');
          document.querySelectorAll('.order-summary-item').forEach(function(item) {
            item.style.display = item.getAttribute('data-sub-id') === subId ? 'block' : 'none';
          });
          document.getElementById('totalPrice').textContent = '₹' + amount;
          document.getElementById('totalPriceFinal').textContent = '₹' + amount;
        });
      });

      function placeOrder(e) {
        e.preventDefault();

        var selectedSub = document.querySelector('input[name="subscription_id"]:checked');
        if (!selectedSub) {
          alert('Please select a subscription plan.');
          return;
        }

        var subId = selectedSub.value;
        var amount = selectedSub.getAttribute('data-amount');

        if (!amount || amount === '0' || amount === '') {
          alert('Error: Subscription price not found. Please try again.');
          return;
        }

        var orderId = 'ORD' + Date.now();
        var redirectUrl = 'https://dainiknirman.com/ccavenue/ccavResponseHandler';
        var cancelUrl = 'https://dainiknirman.com/subscription?user_id=<?= htmlspecialchars($userId) ?>';

        var form = document.getElementById('subscriptionForm');

        form.appendChild(createHidden('order_id', orderId));
        form.appendChild(createHidden('amount', amount));
        form.appendChild(createHidden('billing_name', document.getElementById('firstName').value + ' ' + document.getElementById('lastName').value));
        form.appendChild(createHidden('billing_address', document.getElementById('address').value));
        form.appendChild(createHidden('billing_city', document.getElementById('city').value));
        form.appendChild(createHidden('billing_state', document.getElementById('state').value));
        form.appendChild(createHidden('billing_zip', document.getElementById('pincode').value));
        form.appendChild(createHidden('billing_country', document.getElementById('country').value));
        form.appendChild(createHidden('billing_tel', document.getElementById('phone').value));
        form.appendChild(createHidden('billing_email', document.getElementById('email').value));
        form.appendChild(createHidden('user_id', '<?= htmlspecialchars($userId) ?>'));
        form.appendChild(createHidden('prod_id', subId));
        form.appendChild(createHidden('type', 'subscription'));
        form.appendChild(createHidden('redirect_url', redirectUrl));
        form.appendChild(createHidden('cancel_url', cancelUrl));

        form.action = 'ccavenue/ccavRequestHandler';
        form.method = 'POST';
        form.submit();
      }

      function createHidden(name, value) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        return input;
      }
    </script>

<?php require 'footer.php'; ?>
