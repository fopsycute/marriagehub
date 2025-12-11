
<?php

$requireLogin = true;
include "header.php"; ?>

<section id="checkout" class="checkout section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <?php
    // Fetch order total
    $totalurl = $siteurl . "script/user.php?action=orderdata&order_id=" . urlencode($order_id);
    $data = curl_get_contents($totalurl);
    $order_total = 0;

    if ($data !== false) {
        $totalData = json_decode($data);
        if ($totalData && isset($totalData->status) && $totalData->status === 'success') {
            $order_total = $totalData->total ?? 0;
        }
    }
    ?>

    <div class="row">
      <div class="col-lg-7">
        <!-- Checkout Form -->
        <div class="checkout-container" data-aos="fade-up">
          <form class="checkout-form">
            <!-- Customer Information -->
            <div class="checkout-section" id="customer-info">
              <div class="section-header">
                <div class="section-number">1</div>
                <h3>Customer Information</h3>
              </div>
              <div class="section-content">
                <div class="row">
                  <div class="col-md-12 form-group">
                    <label for="first-name">First Name</label>
                    <input type="text" name="first-name" class="form-control" id="first-name" placeholder="Your First Name" required value="<?php echo $buyerName; ?>" readonly>
                  </div>
                </div>
                <div class="form-group">
                  <label for="email">Email Address</label>
                  <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required value="<?php echo $buyerEmail; ?>" readonly>
                </div>
                <input type="hidden" id="amount" value="<?php echo $order_total; ?>"/>
                <input type="hidden" id="ref" value="<?php echo $order_id; ?>"/>
                <input type="hidden" id="refer" value="<?php echo $siteurl; ?>/pay_success.php?ref=<?php echo $order_id; ?>"/>
                <div class="form-group">
                  <label for="phone">Phone Number</label>
                  <input type="tel" class="form-control" name="phone" id="phone" placeholder="Your Phone Number" required value="<?php echo $phone; ?>">
                </div>
              </div>
            </div>
        <input type="hidden" id="order_total" value="<?php echo number_format($order_total, 2); ?>">
        <input type="hidden" id="site_currency" value="<?php echo $sitecurrency; ?>">

            <!-- Payment Methods -->
            <?php if ($order_total > 0) { ?>
              <div class="checkout-section" id="payment-methods">
                <div class="section-header">
                  <div class="section-number">2</div>
                  <h3>Payment Method</h3>
                </div>
                <div class="section-content">
                  <div class="payment_methods mt-3">
                <h4>Select Payment Method</h4>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="paystack" value="paystack"  onchange="updatePaymentButton()" checked>
                    <label class="form-check-label" for="paystack">Pay with VPay</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="manual" value="manual"  onchange="updatePaymentButton()">
                    <label class="form-check-label" for="manual">Manual Bank Transfer</label>
                </div>
                </div>
                </div>
              </div>
            <?php } ?>
          </form>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="col-lg-5">
        <div class="order-summary" data-aos="fade-left" data-aos-delay="200">
          <div class="order-summary-header">
            <h3>Order Summary</h3>
          </div>

          <div class="order-summary-content">
            <div class="order-items">
              <?php
              // Fetch cart items dynamically
              $apiurl = $siteurl . "script/user.php?action=getcartitems&order_id=" . urlencode($order_id);
              $data = curl_get_contents($apiurl);


              if ($data !== false) {
                  $items = json_decode($data, true);
                  if (!empty($items)) {
                      foreach ($items as $item) {
                          $title = $item['listing_title'];
                          $price = $item['price'];
                          $quantity = $item['quantity'];
                          $total = $item['total_price'];
                          $variation = !empty($item['variation']) ? $item['variation'] : '—';
                          $image = !empty($item['main_image']) ? $imagePath . $item['main_image'] : $siteurl . "assets/img/no-image.webp";
                          ?>
                          <div class="order-item">
                            <div class="order-item-image">
                              <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($title); ?>" class="img-fluid">
                            </div>
                            <div class="order-item-details">
                              <h4><?php echo htmlspecialchars($title); ?></h4>
                              <p class="order-item-variant">Variation: <?php echo htmlspecialchars($variation); ?></p>
                              <div class="order-item-price">
                                <span class="quantity"><?php echo $quantity; ?> ×</span>
                                <span class="price"><?php echo $sitecurrency . number_format($price, 2); ?></span>
                              </div>
                            </div>
                          </div>
                          <?php
                      }
                  } else {
                      echo "<p class='text-center mt-3'><i class='bi bi-cart'></i> Your cart is empty.</p>";
                  }
              }
              ?>
            </div>

            <div class="order-totals">
              <div class="order-subtotal d-flex justify-content-between">
                <span>Subtotal</span>
                <span><?php echo $sitecurrency . number_format($order_total, 2); ?></span>
              </div>
              <div class="order-total d-flex justify-content-between fw-bold">
                <span>Total</span>
                <span><?php echo $sitecurrency . number_format($order_total, 2); ?></span>
              </div>
            </div>

            <div class="place-order-container mt-4">
        <button type="button" id="paymentButton"
            class="btn btn-primary w-100 paystack-button"
            onClick="payWithPaystack()">
            <span class="btn-text">Pay with VPay</span>
            <span class="btn-price" id="btn-price-text"></span>
        </button>
        </div>


            <div class="secure-checkout mt-3">
              <div class="secure-checkout-header">
                <i class="bi bi-shield-lock"></i>
                <span>Secure Checkout</span>
              </div>
              <div class="payment-icons">
                <i class="bi bi-credit-card-2-front"></i>
                <i class="bi bi-credit-card"></i>
                <i class="bi bi-paypal"></i>
                <i class="bi bi-apple"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Manual Payment Modal -->
    <div class="modal fade" id="manualPaymentModal" tabindex="-1" aria-labelledby="manualPaymentModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="manualPaymentModalLabel">Manual Bank Transfer</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="post" enctype="multipart/form-data" id="manual-payment">
            <div class="modal-body">
                <div id="messages"></div>
              <p>Please transfer the total amount to the following bank account:</p>
              <ul>
                <li><strong>Bank Name:</strong> <?php echo $site_bank; ?></li>
                <li><strong>Account Name:</strong> <?php echo $siteaccname; ?></li>
                <li><strong>Account Number:</strong> <?php echo $siteaccno; ?></li>
              </ul>
              <p><strong>Total Amount:</strong> <?php echo $sitecurrency . number_format($order_total, 2); ?></p>
              <p>After making the payment, upload the proof of payment below:</p>
              <div class="mb-3">
                <label for="proof_of_payment" class="form-label">Upload Proof of Payment</label>
                <input type="file" class="form-control" id="proof_of_payment" name="proof_of_payment" required>
                <input type="hidden" name="action" value="paymanual">
              </div>
              <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
              <input type="hidden" name="user_id" value="<?php echo $buyerId; ?>">
              <input type="hidden" name="amount" value="<?php echo $order_total; ?>">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" name="submit_manual_payment" class="btn btn-primary" id="submit-btn">Submit Payment</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</section>
<script>



  </script>

<?php include "footer.php"; ?>