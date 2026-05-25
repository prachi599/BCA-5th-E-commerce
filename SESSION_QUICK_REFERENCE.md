# GiftHub - Session & Cookie Quick Reference

## 🔐 Session Setup on Every Page

### Minimum Required (No Auth)
```php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Rest of page code
```

### Admin Only Pages
```php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../shop.php");
    exit();
}
```

### User Only Pages
```php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
```

---

## 📋 Session Variables Available

After login, you have access to:
```php
$_SESSION['user_id']    // User ID (integer)
$_SESSION['user_name']  // User's name (string)
$_SESSION['user_role']  // 'admin' or 'customer' (string)
```

---

## 🍪 Cookie Management

### Check if User Remembered (Already Logged In From Cookie)
The navbar automatically handles this, but to manually check:
```php
include 'config/session.php';
$isLoggedIn = checkUserLogin();
```

### Set Remember Me Cookie
```php
include 'config/session.php';
if ($_POST['remember']) {
    setRememberCookie($user_id, $conn);
}
```

### Clear Remember Me Cookie
```php
include 'config/session.php';
clearRememberCookie($conn);
```

### Complete Logout
```php
include 'config/session.php';
include 'config/db.php';
logoutUser($conn);
```

---

## ✅ Security Best Practices

### DO:
- ✅ Always use prepared statements for database queries
- ✅ Check `$_SESSION['user_id']` before using user data
- ✅ Verify admin role before showing admin features
- ✅ Escape output with `htmlspecialchars()`
- ✅ Use integer casting for IDs: `(int) $_GET['id']`
- ✅ Validate email with `filter_var($email, FILTER_VALIDATE_EMAIL)`

### DON'T:
- ❌ Never directly concatenate user input into SQL queries
- ❌ Don't trust `$_GET` or `$_POST` without validation
- ❌ Don't output user data without escaping
- ❌ Don't store sensitive data in cookies
- ❌ Don't expose error messages to users
- ❌ Don't allow SQL errors to display on page

---

## 🐛 Common Issues & Solutions

### Issue: "Headers already sent" error
**Cause**: Output before `session_start()`
**Solution**: Put `session_start()` at the very top of file, before any `echo` or HTML

### Issue: Session not persisting between pages
**Cause**: Session not started on all pages
**Solution**: Add session initialization to every page

### Issue: Remember Me not working
**Cause**: Database columns missing
**Solution**: Run `helpers/setup_session.php` to add columns to users table

### Issue: Admin pages accessible by customers
**Cause**: Missing role check
**Solution**: Ensure admin pages have: `if ($_SESSION['user_role'] !== 'admin')`

### Issue: Can't access session variables
**Cause**: Session not started
**Solution**: Ensure `session_start()` is at the top of the file

---

## 📊 Database Queries

### Check if Email Exists
```php
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE LOWER(email) = LOWER(?)");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$exists = mysqli_num_rows($result) > 0;
```

### Get User by ID
```php
$user_id = (int) $_SESSION['user_id'];
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
```

### Get User's Cart
```php
$user_id = (int) $_SESSION['user_id'];
$cart = mysqli_query($conn, "
    SELECT c.*, p.name, p.price 
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
");
```

### Get User's Orders
```php
$user_id = (int) $_SESSION['user_id'];
$orders = mysqli_query($conn, "
    SELECT * FROM orders 
    WHERE user_id = $user_id 
    ORDER BY created_at DESC
");
```

---

## 🔄 Session Lifecycle

1. **User Registers** → Password hashed, record created
2. **User Logs In** → Session created, `$_SESSION` populated
3. **User Checks "Remember Me"** → Token created (30 days)
4. **Browser Closes** → Session ends locally, cookie persists
5. **User Returns** → Cookie read, session restored
6. **30 Days Pass** → Token expires, user must login again
7. **User Logs Out** → Session destroyed, cookie cleared

---

## 🎯 Common Workflows

### Login Flow
```
User enters email/password 
  → Prepare statement lookup
  → Verify password hash
  → Set $_SESSION['user_id']
  → If remember: create token
  → Redirect to shop
```

### Admin Access Flow
```
User navigates to admin page
  → Check $_SESSION['user_id'] exists
  → Check $_SESSION['user_role'] === 'admin'
  → If not: redirect to shop
  → Load admin page
```

### Logout Flow
```
User clicks logout
  → Include session.php
  → Call logoutUser($conn)
  → Function clears:
     - Remember token from DB
     - Session variables
     - Session cookies
     - Redirect to home
```

---

## 📱 Mobile Considerations

Session and cookies work the same on mobile, but:
- Cookies are stored per app/browser
- Some browsers have strict cookie policies
- Test on real devices for remember me
- Consider "Clear app data" as logout equivalent

---

## 🚀 Performance Tips

1. **Cache session data**: Don't query DB for every page for common fields
2. **Lazy load admin features**: Only query admin data if `$_SESSION['user_role'] === 'admin'`
3. **Use connection pooling**: For high traffic
4. **Index user tables**: Speed up login lookups

---

## 📝 Session Data Structure

```php
$_SESSION = [
    'user_id' => 42,           // From users.id
    'user_name' => 'John Doe', // From users.name
    'user_role' => 'customer',  // From users.role
    'LAST_REGENERATE' => 1716450000  // Internal security
];
```

---

## 🔗 Related Files

- `config/session.php` - Session/cookie functions
- `config/db.php` - Database connection
- `login.php` - Login form and logic
- `register.php` - Registration form
- `logout.php` - Logout handler
- `includes/navbar.php` - Displays logged-in status

---

**Last Updated**: May 23, 2026
**Version**: 1.0
