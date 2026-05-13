<?php

$pageTitle = 'Checkout - NIRMA PRAKASHAN';
$activePage = 'books';

require 'db_config.php';

$userId = $_GET['user_id'] ?? null;
$prodId = $_GET['prod_id'] ?? null;

if (!$userId) {
    header('Location: index.html?error=user_not_found');
    exit;
}

if (!$prodId) {
    header('Location: subscription.php?user_id=' . urlencode($userId));
    exit;
}

$stmt = $conn->prepare("SELECT name, email, mobile, address FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

$bookStmt = $conn->prepare("SELECT book_name, author_name, price, original_price, cover_picture, description FROM books WHERE id = ?");
$bookStmt->bind_param("i", $prodId);
$bookStmt->execute();
$bookResult = $bookStmt->get_result();
$bookData = $bookResult->fetch_assoc();
$bookStmt->close();
// $conn->close(); // Keep open if needed, but db_config handles it

$bookName = $bookData['book_name'] ?? '';
$bookAuthor = $bookData['author_name'] ?? '';
$bookPrice = $bookData['price'] ?? 0;
$bookOriginalPrice = $bookData['original_price'] ?? null;
$bookCover = $bookData['cover_picture'] ?? '';
$bookDescription = $bookData['description'] ?? '';

$userName = $userData['name'] ?? '';
$userEmail = $userData['email'] ?? '';
$userPhone = $userData['mobile'] ?? '';
$userAddress = $userData['address'] ?? '';

$nameParts = explode(' ', $userName, 2);
$firstName = $nameParts[0] ?? '';
$lastName = $nameParts[1] ?? '';

require 'header.php';
?>

    <!-- checkout form with full details  -->
    <main class="checkout-main">
      <div class="checkout-container">
        <h1 class="checkout-title">Checkout</h1>

        <?php if (isset($_GET['error'])): ?>
          <div class="error-banner" style="background-color: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
            <p><strong>Payment Error:</strong> <?= htmlspecialchars($_GET['error']) ?></p>
            <?php if (isset($_GET['status'])): ?>
              <p><small>Status: <?= htmlspecialchars($_GET['status']) ?></small></p>
            <?php endif; ?>
            <?php if (isset($_GET['msg'])): ?>
              <p><small>Message: <?= htmlspecialchars($_GET['msg']) ?></small></p>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <div class="checkout-grid">
          <!-- Checkout Form -->
          <div class="checkout-form-section">
            <form class="checkout-form" id="checkoutForm" onsubmit="placeOrder(event)">
              <!-- Billing Details -->
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
              <input type="hidden" id="prodId" name="prod_id" value="<?= htmlspecialchars($prodId) ?>">

              <button type="submit" class="btn btn-primary btn-place-order">Proceed to Pay</button>

            </form>
          </div>

          <!-- Order Summary -->
          <div class="order-summary-section">
            <div class="order-summary">
              <h2 class="order-summary-title">Order Summary</h2>

              <div class="order-summary-item">
                <div class="order-item-details">
                  <h3 class="order-item-name"><?= htmlspecialchars($bookName) ?></h3>
                  <p class="order-item-author"><?= htmlspecialchars($bookAuthor) ?></p>
                  <div class="order-item-price">
                    <span class="current-price">₹<?= htmlspecialchars($bookPrice) ?></span>
                    <?php if ($bookOriginalPrice): ?>
                      <span class="original-price">₹<?= htmlspecialchars($bookOriginalPrice) ?></span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <div class="order-totals">
                <div class="order-total-row">
                  <span>Subtotal</span>
                  <span>₹<?= htmlspecialchars($bookPrice) ?></span>
                </div>
                <div class="order-total-row total">
                  <span>Total</span>
                  <span id="totalPrice">₹<?= htmlspecialchars($bookPrice) ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <script>
      function placeOrder(e) {
        e.preventDefault();

        let orderId = "ORD" + Date.now();
        let price = "<?= htmlspecialchars($bookPrice) ?>";

        if (!price || price === "0" || price === "") {
            alert("Error: Product price not found. Please go back and try again.");
            return;
        }

        let redirectUrl = "https://dainiknirman.com/ccavenue/ccavResponseHandler";
        let cancelUrl = "https://dainiknirman.com/index";

        let form = document.getElementById('checkoutForm');
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'order_id';
        input.value = orderId;
        form.appendChild(input);

        let amountInput = document.createElement('input');
        amountInput.type = 'hidden';
        amountInput.name = 'amount';
        amountInput.value = price;
        form.appendChild(amountInput);

        let nameInput = document.createElement('input');
        nameInput.type = 'hidden';
        nameInput.name = 'billing_name';
        nameInput.value = document.getElementById('firstName').value + ' ' + document.getElementById('lastName').value;
        form.appendChild(nameInput);

        let addressInput = document.createElement('input');
        addressInput.type = 'hidden';
        addressInput.name = 'billing_address';
        addressInput.value = document.getElementById('address').value;
        form.appendChild(addressInput);

        let cityInput = document.createElement('input');
        cityInput.type = 'hidden';
        cityInput.name = 'billing_city';
        cityInput.value = document.getElementById('city').value;
        form.appendChild(cityInput);

        let stateInput = document.createElement('input');
        stateInput.type = 'hidden';
        stateInput.name = 'billing_state';
        stateInput.value = document.getElementById('state').value;
        form.appendChild(stateInput);

        let zipInput = document.createElement('input');
        zipInput.type = 'hidden';
        zipInput.name = 'billing_zip';
        zipInput.value = document.getElementById('pincode').value;
        form.appendChild(zipInput);

        let countryInput = document.createElement('input');
        countryInput.type = 'hidden';
        countryInput.name = 'billing_country';
        countryInput.value = document.getElementById('country').value;
        form.appendChild(countryInput);

        let phoneInput = document.createElement('input');
        phoneInput.type = 'hidden';
        phoneInput.name = 'billing_tel';
        phoneInput.value = document.getElementById('phone').value;
        form.appendChild(phoneInput);

        let emailInput = document.createElement('input');
        emailInput.type = 'hidden';
        emailInput.name = 'billing_email';
        emailInput.value = document.getElementById('email').value;
        form.appendChild(emailInput);

        let user_id_input = document.createElement('input');
        user_id_input.type = 'hidden';
        user_id_input.name = 'user_id';
        user_id_input.value = "<?= htmlspecialchars($userId) ?>";
        form.appendChild(user_id_input);

        let prod_id_input = document.createElement('input');
        prod_id_input.type = 'hidden';
        prod_id_input.name = 'prod_id';
        prod_id_input.value = "<?= htmlspecialchars($prodId) ?>";
        form.appendChild(prod_id_input);

        let redirectInput = document.createElement('input');
        redirectInput.type = 'hidden';
        redirectInput.name = 'redirect_url';
        redirectInput.value = redirectUrl;
        form.appendChild(redirectInput);

        let cancelInput = document.createElement('input');
        cancelInput.type = 'hidden';
        cancelInput.name = 'cancel_url';
        cancelInput.value = cancelUrl;
        form.appendChild(cancelInput);

        form.action = 'ccavenue/ccavRequestHandler';
        form.method = 'POST';
        form.submit();
      }
    </script>

<?php require 'footer.php'; ?>
