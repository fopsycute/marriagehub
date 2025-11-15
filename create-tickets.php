<?php

$requireLogin = true;
include "header.php"; ?>


<main class="main">

  <div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">Create Ticket</h1>
      <nav class="breadcrumbs">
        <ol>
          <li><a href="index.php">Home</a></li>
          <li class="active"><a href="tickets.php">All Tickets</a></li>
        </ol>
      </nav>
    </div>
  </div>

  <section id="create-ticket" class="checkout section">
    <div class="container" data-aos="fade-up">

      <div class="row">
        <div class="col-lg-9 mx-auto">
          <div class="checkout-container">

            <form method="post" enctype="multipart/form-data" id="createTicketForm">

              <div class="checkout-section">
                <div class="section-header">
                  <h3>Create Ticket</h3>
                </div>
     <div class="col-lg-12 text-center mt-1" id="messages"></div> 
                <div class="section-content">

                  <!-- Category -->
                  <div class="mb-3">
                    <label>Dispute Category:</label>
                    <select name="category" class="form-control" required>
                      <option value="">Select Category</option>
                      <option value="Product Quality Issues">Product Quality Issues</option>
                      <option value="Wrong Item Received">Wrong Item Received</option>
                      <option value="Item Not Delivered">Item Not Delivered</option>
                      <option value="Refund Issues">Refund Issues</option>
                      <option value="Technical Bugs">Technical Bugs</option>
                      <option value="Payment Issues">Payment Issues</option>
                    </select>
                  </div>
    <input type="hidden" id="buyerId" name="user_id" value="<?php echo $buyerId; ?>">
                  <!-- Order Reference -->
                  <div class="mb-3">
                    <label>Order Reference:</label>
                    <select name="order_id" id="order_ids" class="form-control" required>
                      <option value="">Select Order</option>
                      <?php
                        $orders = curl_get_contents($siteurl."script/admin.php?action=getuserorders&user_id=".$buyerId);
                        $orders = json_decode($orders);
                        if (!empty($orders)) {
                          foreach ($orders as $order) {
                            echo '<option value="' . $order->order_id . '">' .
                              $order->order_id . ' (' . $order->status . ')</option>';
                          }
                        }
                      ?>
                    </select>
                  </div>

                  <input type="hidden" name="action" value="createticket">

                  <!-- Recipient -->
                  <div class="mb-3">
                    <label>Recipient Involved:</label>
                    <select id="recipient" name="recipient" class="form-control" required>
                      <option value="">Select recipient</option>
                    </select>
                  </div>

                  <!-- Issue Title -->
                  <div class="mb-3">
                    <label>Issue Title:</label>
                    <textarea name="issue" class="editor" maxlength="100" required></textarea>
                  </div>

                  <!-- Upload Evidence -->
                  <div class="mb-3">
                    <label>Upload Evidence:</label>
                    <input type="file" name="evidence[]" class="form-control" multiple>
                  </div>

                  <!-- Submit -->
                  <button type="submit" name="create_dispute" id="submitBtn" class="btn btn-primary w-100">
                    Submit Ticket
                  </button>

                </div>
              </div>

            </form>

          </div>
        </div>
      </div>

    </div>
  </section>

</main>

<script>

</script>

<?php include "footer.php"; ?>
