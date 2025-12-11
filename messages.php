<?php include "header.php"; ?>

<div class="container mt-4 mb-4">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Messages</h3>
        </div>

        <div class="row">
            <!-- Conversations List -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Conversations</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#composeModal">
                            <i class="bi bi-plus-circle"></i> New
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="conversationsList">
                            <?php
                            // Fetch conversations for logged-in user
                            $url = $siteurl . "script/user.php?action=getConversations&user_id=" . $buyerId;
                            $data = curl_get_contents($url);

                            if ($data !== false) {
                                $conversations = json_decode($data, true);

                                if (!empty($conversations) && !isset($conversations['error'])) {
                                    foreach ($conversations as $conv) {
                                        $otherId = $conv['other_user_id'];
                                        $otherName = htmlspecialchars($conv['other_user_name']);
                                        $lastMessage = htmlspecialchars(limitWords($conv['last_message'] ?? 'No messages yet', 10));
                                        $unreadCount = intval($conv['unread_count']);
                                        $isActive = isset($_GET['user_id']) && $_GET['user_id'] == $otherId ? 'active' : '';
                                        ?>
                                        <a href="?user_id=<?= $otherId ?>" class="list-group-item list-group-item-action <?= $isActive ?>">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?= $otherName ?></h6>
                                                <?php if ($unreadCount > 0): ?>
                                                    <span class="badge bg-primary rounded-pill"><?= $unreadCount ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="mb-1 text-muted small"><?= $lastMessage ?></p>
                                        </a>
                                        <?php
                                    }
                                } else {
                                    echo '<p class="text-center text-muted p-3">No conversations yet</p>';
                                }
                            } else {
                                echo '<p class="text-center text-danger p-3">Failed to load conversations</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="col-md-8">
                <div class="card" style="height: 600px; display: flex; flex-direction: column;">
                    <?php if (isset($_GET['user_id'])): 
                        $otherUserId = intval($_GET['user_id']);
                        
                        // Fetch other user's info
                        $userUrl = $siteurl . "script/user.php?action=buyerdata&buyer=" . $otherUserId;
                        $userData = curl_get_contents($userUrl);
                        $otherUser = json_decode($userData);
                        $otherUserName = $otherUser->first_name . ' ' . $otherUser->last_name;
                    ?>
                        <div class="card-header">
                            <h5><?= htmlspecialchars($otherUserName) ?></h5>
                        </div>
                        <div class="card-body" style="flex: 1; overflow-y: auto;" id="messagesContainer">
                            <?php
                            // Fetch messages between users
                            $msgUrl = $siteurl . "script/user.php?action=getMessages&user_id=" . $buyerId . "&other_user_id=" . $otherUserId;
                            $msgData = curl_get_contents($msgUrl);

                            if ($msgData !== false) {
                                $messages = json_decode($msgData, true);

                                if (!empty($messages) && !isset($messages['error'])) {
                                    foreach ($messages as $msg) {
                                        $isSender = $msg['sender_id'] == $buyerId;
                                        $alignClass = $isSender ? 'text-end' : 'text-start';
                                        $bgClass = $isSender ? 'bg-primary text-white' : 'bg-light';
                                        $messageText = htmlspecialchars($msg['message']);
                                        $messageTime = timeAgo($msg['created_at']);
                                        ?>
                                        <div class="<?= $alignClass ?> mb-3">
                                            <div class="d-inline-block p-2 rounded <?= $bgClass ?>" style="max-width: 70%;">
                                                <?= nl2br($messageText) ?>
                                                <div class="small mt-1 <?= $isSender ? 'text-white-50' : 'text-muted' ?>"><?= $messageTime ?></div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    echo '<p class="text-center text-muted">No messages yet. Start the conversation!</p>';
                                }
                            }
                            ?>
                        </div>
                        <div class="card-footer">
                            <form id="sendMessageForm" class="d-flex gap-2">
                                <input type="hidden" name="sender_id" value="<?= $buyerId ?>">
                                <input type="hidden" name="receiver_id" value="<?= $otherUserId ?>">
                                <textarea name="message" class="form-control" rows="2" placeholder="Type your message..." required></textarea>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i> Send
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="text-center text-muted">
                                <i class="bi bi-chat-dots" style="font-size: 4rem;"></i>
                                <p class="mt-3">Select a conversation or start a new one</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Compose Message Modal -->
<div class="modal fade" id="composeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="composeMessageForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">To (User ID or Email)</label>
                        <input type="text" name="receiver_identifier" class="form-control" placeholder="Enter user ID or email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="4" required></textarea>
                    </div>
                    <input type="hidden" name="sender_id" value="<?= $buyerId ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const siteUrl = $('#siteurl').val();

    // Send message from conversation
    $('#sendMessageForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: siteUrl + 'script/user.php?action=sendMessage',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    location.reload();
                } else {
                    alert(response.message || 'Failed to send message');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Compose new message
    $('#composeMessageForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        
        $.ajax({
            url: siteUrl + 'script/user.php?action=sendNewMessage',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    window.location.href = 'messages.php?user_id=' + response.receiver_id;
                } else {
                    alert(response.message || 'Failed to send message');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Auto-scroll to bottom of messages
    const messagesContainer = document.getElementById('messagesContainer');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});
</script>

<?php include "footer.php"; ?>
