# Verification Steps for Manage Users Module

## Quick Checklist

### 1. Database Migration
Run this command in your terminal:
```bash
php spark migrate
```

This will add the `status` and `is_protected` fields to your users table.

### 2. Clear Browser Cache
- Press `Ctrl + F5` (Windows) or `Cmd + Shift + R` (Mac) to hard refresh
- Or clear your browser cache completely

### 3. Access the Page
Navigate to: `http://localhost/ITE311-REYES/admin/users`

Make sure you're logged in as an admin user.

### 4. What You Should See

#### ✅ Role Dropdown
- Should show: **Student**, **Librarian**, **Admin** (NOT Teacher)
- Should be a dropdown in the Role column for each user (except protected admin)

#### ✅ Status Column
- Should show **Active** or **Inactive** badge
- Green badge for Active, Gray badge for Inactive

#### ✅ Actions Column
- **Protected Admin**: Shows "Change Password" button only
- **Other Users**: Shows "Deactivate" or "Activate" button

#### ✅ Add User Button
- Top right corner: "Add User" button
- Opens a modal with: Name, Email, Password, Role fields
- Role dropdown should show: Student, Librarian, Admin

### 5. Test Functionality

1. **Change Role**: Click the role dropdown for any non-protected user → Should update instantly
2. **Deactivate User**: Click "Deactivate" button → Should show confirmation → User status changes to Inactive
3. **Add User**: Click "Add User" → Fill form → Should create new user
4. **Protected Admin**: Should only show "Change Password" button, no deactivate option

### 6. If Still Not Working

1. **Check Browser Console** (F12 → Console tab)
   - Look for JavaScript errors
   - Check if jQuery is loaded

2. **Check Network Tab** (F12 → Network tab)
   - When clicking buttons, check if AJAX requests are being sent
   - Check response status codes

3. **Verify Database**
   ```sql
   DESCRIBE users;
   ```
   Should show `status` and `is_protected` columns

4. **Check File Permissions**
   - Make sure files are readable by web server

### 7. Common Issues

**Issue**: Dropdown shows "Teacher" instead of "Librarian"
- **Solution**: Hard refresh browser (Ctrl+F5)

**Issue**: No "Deactivate" button showing
- **Solution**: Check if `status` column exists in database, run migration

**Issue**: Role change not working
- **Solution**: Check browser console for JavaScript errors, verify CSRF token

**Issue**: Can't access `/admin/users`
- **Solution**: Make sure you're logged in as admin, check Routes.php

