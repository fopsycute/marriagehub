
<?php 
$requireLogin = true;
include "header.php"; ?>
<section id="cart" class="cart section">

      <div class="container aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
        <div class="row">
        
<div class="col-lg-8 aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
  <div class="cart-items">
  <?php
$totalurl = $siteurl . "script/user.php?action=orderdata&order_id=" . urlencode($order_id);
$data = curl_get_contents($totalurl);

$ordertotal = 0;

if ($data !== false) {
    $totalData = json_decode($data);

    if ($totalData && isset($totalData->status) && $totalData->status === 'success') {
        $ordertotal = $totalData->total ?? 0;
    }
}
?>
    <?php
    // Fetch cart items from API
    $apiurl = $siteurl . "script/user.php?action=getcartitems&order_id=" . urlencode($order_id);
    $data = curl_get_contents($apiurl);

    if ($data !== false) {
        $items = json_decode($data, true);

        if (!empty($items)) {
            ?>
            <div class="cart-header d-none d-lg-block">
              <div class="row align-items-center">
                <div class="col-lg-6">
                  <h5>Product</h5>
                </div>
                <div class="col-lg-2 text-center">
                  <h5>Price</h5>
                </div>
                <div class="col-lg-2 text-center">
                  <h5>Quantity</h5>
                </div>
                <div class="col-lg-2 text-center">
                  <h5>Total</h5>
                </div>
              </div>
            </div>
            <?php

            foreach ($items as $item) {
                $id = $item['id'];
                $title = $item['listing_title'];
                $slug = $item['slug'];
                $price = $item['price'];
                $quantity = $item['quantity'];
                $total = $item['total_price'];
                $variation = !empty($item['variation']) ? $item['variation'] : '—';
                $image = !empty($item['main_image']) ? $imagePath . $item['main_image'] : $siteurl . "assets/img/no-image.webp";
                ?>
                
                <div class="cart-item">
                  <div class="row align-items-center">
                    <div class="col-lg-6 col-12 mt-3 mt-lg-0 mb-lg-0 mb-3">
                      <div class="product-info d-flex align-items-center">
                        <div class="product-image">
                          <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($title); ?>" class="img-fluid" loading="lazy">
                        </div>
                        <div class="product-details">
                      <h6 class="product-title">
                <a href="<?php echo $siteurl . 'products.php?slug=' . $slug; ?>">
                  <?php echo htmlspecialchars($title); ?>
                </a>
              </h6>

                          <div class="product-meta">
                            <span class="product-variation">Variation: <?php echo htmlspecialchars($variation); ?></span>
                          </div>
                          <button class="remove-item" type="button" data-item-id="<?php echo $id; ?>">
                            <i class="bi bi-trash"></i> Remove
                          </button>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-2 col-12 mt-3 mt-lg-0 text-center">
                      <div class="price-tag">
                        <span class="current-price"><?php echo $sitecurrency.$price; ?></span>
                      </div>
                    </div>
                    <div class="col-lg-2 col-12 mt-3 mt-lg-0 text-center">
                       <div class="quantity-selector" data-item-id="<?php echo $id; ?>">
                            <button id="decrease-<?php echo $id; ?>" class="quantity-btn decrease" type="button">
                                <i class="bi bi-dash"></i>
                            </button>

                            <input id="quantity-<?php echo $id; ?>" 
                                    type="number" 
                                    class="quantity-input" 
                                    value="<?php echo $quantity; ?>" 
                                    min="1" 
                                    max="100" 
                                    data-item-id="<?php echo $id; ?>">

                            <button id="increase-<?php echo $id; ?>" class="quantity-btn increase" type="button">
                                <i class="bi bi-plus"></i>
                            </button>
                            </div>

                    </div>
                    <div class="col-lg-2 col-12 mt-3 mt-lg-0 text-center">
                      <div class="item-total">
                        <span><?php echo $sitecurrency.$total; ?></span>
                      </div>
                    </div>
                  </div>
                </div>
                <?php
            }
        } else {
            echo "<p class='text-center mt-4'><i class='bi bi-cart'></i> Your cart is empty.</p>";
        }
    } else {
        echo "<p class='text-danger text-center'>Error fetching cart items.</p>";
    }
    ?>

    <div class="cart-actions mt-4">
      <div class="row">
        <div class="col-lg-6 text-md-end">
         <button class="btn btn-outline-heading btn-update me-2">
            <i class="bi bi-arrow-clockwise"></i> Update Cart
        </button>
        </div>
      </div>
    </div>
  </div>
</div>


          <div class="col-lg-4 mt-4 mt-lg-0 aos-init aos-animate" data-aos="fade-up" data-aos-delay="300">
            <div class="cart-summary">
              <h4 class="summary-title">Order Summary</h4>

              <div class="summary-item">
                <span class="summary-label">Subtotal</span>
                <span class="summary-value"><?php echo $ordertotal; ?></span>
              </div>

              <div class="summary-total">
                <span class="summary-label">Total</span>
                <span class="summary-value"><?php echo $ordertotal; ?></span>
              </div>

              <div class="checkout-button">
                <a href="<?php echo $siteurl; ?>checkout" class="btn btn-accent w-100">
                  Proceed to Checkout <i class="bi bi-arrow-right"></i>
                </a>
              </div>

              <div class="continue-shopping">
                <a href="#" class="btn btn-link w-100">
                  <i class="bi bi-arrow-left"></i> Continue Shopping
                </a>
              </div>

              <div class="payment-methods">
                <p class="payment-title">We Accept</p>
                <div class="payment-icons">
                  <i class="bi bi-credit-card"></i>
                  <i class="bi bi-paypal"></i>
                  <i class="bi bi-wallet2"></i>
                  <i class="bi bi-bank"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </section>
<?php include "footer.php"; ?>