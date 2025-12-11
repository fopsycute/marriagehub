# Feature Testing Guide

## Pin Posts Feature Testing

### Prerequisites
1. Run migration: `admin/run-migrations.php` → Execute "add_pinned_field.sql"
2. Login as admin
3. Ensure you have approved blog posts and questions

### Test Cases

#### Test 1: Pin a Blog Post
1. Navigate to `admin/approved-blog.php`
2. Find any blog post
3. Click "Pin Post" button (should be gray/secondary color)
4. **Expected:** Button changes to "Unpin Post" (orange/warning color)
5. **Expected:** Pin icon (📌) appears next to post title
6. Refresh page
7. **Expected:** Pinned post appears at TOP of list
8. **Expected:** Pin icon and "Unpin Post" button still visible

#### Test 2: Unpin a Blog Post
1. On same page, find pinned post
2. Click "Unpin Post" button (orange)
3. **Expected:** Button changes to "Pin Post" (gray)
4. **Expected:** Pin icon disappears
5. Refresh page
6. **Expected:** Post moves back to date order (no longer at top)

#### Test 3: Pin Multiple Posts
1. Pin 3 different blog posts
2. Refresh page
3. **Expected:** All 3 pinned posts appear at top
4. **Expected:** Among pinned posts, order is by date (newest pinned first)
5. **Expected:** Unpinned posts appear below all pinned posts

#### Test 4: Pin Questions
1. Navigate to `admin/approved-questions.php`
2. Repeat Test 1-3 for questions
3. **Expected:** Same behavior as blog posts

### Success Criteria
- ✅ Pin button toggles state correctly
- ✅ Pin icon appears/disappears appropriately
- ✅ Pinned items always appear at top of listings
- ✅ Multiple pinned items ordered by date among themselves
- ✅ Changes persist after page refresh
- ✅ Works for both blogs and questions

---

## Private Messaging System Testing

### Prerequisites
1. Run migration: `admin/run-migrations.php` → Execute "create_messaging_tables.sql"
2. Have at least 2 user accounts for testing
3. Know user IDs or emails for test accounts

### Test Cases

#### Test 1: View Conversations Page
1. Login as User A
2. Navigate to `messages.php`
3. **Expected:** Page loads with 2-column layout
4. **Expected:** Left sidebar shows "Conversations" header
5. **Expected:** Right panel shows "Select a conversation" message
6. **Expected:** "New Message" button visible in sidebar

#### Test 2: Send New Message (By User ID)
1. Click "New Message" button
2. Modal appears with form
3. Enter User B's ID in "To" field (example: `42`)
4. Type message: "Hello, this is a test message"
5. Click "Send Message"
6. **Expected:** Modal closes
7. **Expected:** Redirects to conversation with User B
8. **Expected:** Message appears in thread with correct styling
9. **Expected:** Message shows as "sent" (right-aligned, blue background)

#### Test 3: Send New Message (By Email)
1. Click "New Message" button
2. Enter User B's email in "To" field (example: `userb@example.com`)
3. Type message: "Testing email lookup"
4. Click "Send Message"
5. **Expected:** Same results as Test 2
6. **Expected:** System finds user by email and creates conversation

#### Test 4: Reply to Existing Conversation
1. Stay in conversation thread with User B
2. Type reply in bottom text area: "This is a reply"
3. Click "Send" button
4. **Expected:** Page reloads
5. **Expected:** New message appears at bottom of thread
6. **Expected:** Auto-scrolls to show latest message

#### Test 5: Receive Messages
1. Logout and login as User B
2. Navigate to `messages.php`
3. **Expected:** Conversation with User A appears in left sidebar
4. **Expected:** Unread badge shows "2" (or number of unread messages)
5. Click conversation
6. **Expected:** Messages from User A display correctly
7. **Expected:** Messages show as "received" (left-aligned, gray background)
8. **Expected:** Unread badge disappears or shows 0

#### Test 6: Send Reply as User B
1. Still logged in as User B
2. Reply to User A: "Thanks for the message!"
3. Click "Send"
4. **Expected:** Message appears correctly
5. **Expected:** Auto-scroll to bottom

#### Test 7: View Updated Conversation as User A
1. Logout and login as User A
2. Navigate to `messages.php`
3. **Expected:** Conversation with User B shows unread badge "1"
4. Click conversation
5. **Expected:** User B's reply appears
6. **Expected:** Unread badge clears

#### Test 8: Multiple Conversations
1. As User A, send messages to 3 different users
2. View conversations list
3. **Expected:** All 3 conversations appear in left sidebar
4. **Expected:** Most recent conversation at top
5. **Expected:** Each shows correct recipient name
6. **Expected:** Unread counts accurate for each
7. Click each conversation
8. **Expected:** Correct message thread loads each time

#### Test 9: Error Handling - Invalid User
1. Click "New Message"
2. Enter non-existent user ID: `99999`
3. Try to send message
4. **Expected:** Error alert: "Receiver not found"
5. Enter fake email: `nonexistent@fake.com`
6. Try to send
7. **Expected:** Error alert: "User not found with that email"

#### Test 10: Empty Message Validation
1. Open existing conversation
2. Try to send empty message (just spaces)
3. **Expected:** Browser validation prevents submission
4. Or backend returns error

### Success Criteria
- ✅ New conversation creation works with user ID
- ✅ New conversation creation works with email
- ✅ Messages send successfully in existing conversations
- ✅ Messages display with correct styling (sent vs received)
- ✅ Unread badges show accurate counts
- ✅ Messages marked as read when viewed
- ✅ Conversations list orders by most recent
- ✅ Auto-scroll to latest message works
- ✅ Error handling for invalid users
- ✅ Multiple conversations display correctly

---

## Integration Testing

### Test 1: Both Features Together
1. Pin a blog post about messaging feature
2. Send message to another admin about the pinned post
3. **Expected:** Both features work independently
4. **Expected:** No conflicts or errors

### Test 2: Performance Check
1. Pin 10 blog posts
2. Send 50 messages across 5 conversations
3. Refresh pages multiple times
4. **Expected:** Pages load quickly (<2 seconds)
5. **Expected:** No database errors in logs
6. **Expected:** Pin ordering correct with many items
7. **Expected:** Message pagination handles large threads

---

## Browser Compatibility Testing

### Browsers to Test
- ✅ Chrome (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Edge (latest)
- ✅ Mobile browsers (iOS Safari, Chrome Android)

### Test Each Browser
1. Pin/unpin functionality works
2. AJAX requests complete successfully
3. Modals open and close properly
4. Message UI responsive on mobile
5. Auto-scroll functions correctly

---

## Database Verification

### After Testing, Check Database:

```sql
-- Verify pinned posts
SELECT id, title, is_pinned 
FROM ma_forums 
WHERE is_pinned = 1 
ORDER BY created_at DESC;

-- Verify pinned questions
SELECT id, title, is_pinned 
FROM ma_questions 
WHERE is_pinned = 1 
ORDER BY created_at DESC;

-- Verify messages
SELECT * FROM ma_messages 
ORDER BY created_at DESC 
LIMIT 20;

-- Check conversation threads
SELECT * FROM ma_message_threads 
ORDER BY last_message_at DESC;

-- Verify read status updates
SELECT sender_id, receiver_id, is_read, created_at 
FROM ma_messages 
WHERE receiver_id = [YOUR_USER_ID];
```

---

## Troubleshooting

### Pin Posts Not Working
- **Check:** Migration ran successfully
- **Check:** Columns `is_pinned` exist in both tables
- **Check:** Browser console for JavaScript errors
- **Check:** Admin user has permissions

### Messages Not Sending
- **Check:** Migration ran successfully
- **Check:** Tables `ma_messages` and `ma_message_threads` exist
- **Check:** User IDs valid in database
- **Check:** Network tab shows AJAX requests succeeding

### Unread Counts Wrong
- **Check:** SQL query in `getConversations()` function
- **Check:** Messages marked as read when viewed
- **Check:** `is_read` column updates correctly

---

## Sign-Off Checklist

Before marking features as production-ready:

### Pin Posts Feature
- [ ] All 4 test cases pass
- [ ] Works in all major browsers
- [ ] Admin UI displays correctly
- [ ] Pin icon appears properly
- [ ] Sorting works with many posts
- [ ] No console errors

### Private Messaging
- [ ] All 10 test cases pass
- [ ] Works in all major browsers
- [ ] Conversations load correctly
- [ ] Messages send and receive properly
- [ ] Unread badges accurate
- [ ] Auto-scroll functions
- [ ] Error handling works
- [ ] Email lookup functions
- [ ] No console errors

### Both Features
- [ ] Migrations run successfully
- [ ] Database structure correct
- [ ] Performance acceptable
- [ ] Documentation complete
- [ ] No known bugs
- [ ] Ready for production deployment

---

## Test Results Log

**Date:** _____________  
**Tester:** _____________  
**Environment:** _____________

| Test Case | Status | Notes |
|-----------|--------|-------|
| Pin Blog Post | ⬜ Pass / ⬜ Fail | |
| Unpin Blog Post | ⬜ Pass / ⬜ Fail | |
| Multiple Pins | ⬜ Pass / ⬜ Fail | |
| Pin Questions | ⬜ Pass / ⬜ Fail | |
| View Messages Page | ⬜ Pass / ⬜ Fail | |
| New Message (ID) | ⬜ Pass / ⬜ Fail | |
| New Message (Email) | ⬜ Pass / ⬜ Fail | |
| Reply to Message | ⬜ Pass / ⬜ Fail | |
| Receive Messages | ⬜ Pass / ⬜ Fail | |
| Multiple Conversations | ⬜ Pass / ⬜ Fail | |

**Overall Status:** ⬜ Ready for Production / ⬜ Needs Fixes

**Additional Notes:**
_______________________________________________________________
_______________________________________________________________
_______________________________________________________________
