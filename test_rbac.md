# RBAC Testing Guide

## Test Users Created

The following test users have been created with the seeder:

1. **Admin User**
   - Email: `admin@example.com`
   - Password: `secret1234`
   - Role: `admin`

2. **Teacher User**
   - Email: `teacher@example.com`
   - Password: `secret1234`
   - Role: `teacher`

3. **Student User**
   - Email: `student@example.com`
   - Password: `secret1234`
   - Role: `student`

## Testing Steps

### 1. Test Admin Role
1. Go to `http://localhost:8080/login`
2. Login with `admin@example.com` / `secret1234`
3. Should redirect to `/dashboard`
4. Should see:
   - "System Overview" card with user/course/lesson counts
   - "Admin Actions" card with admin-specific buttons
   - Navbar should show "Users" and "Courses" links

### 2. Test Teacher Role
1. Logout and login with `teacher@example.com` / `secret1234`
2. Should see:
   - "My Teaching" card with course/quiz counts
   - "Teacher Shortcuts" card with teacher-specific buttons
   - Navbar should show "My Courses" and "Quizzes" links

### 3. Test Student Role
1. Logout and login with `student@example.com` / `secret1234`
2. Should see:
   - "My Learning" card with enrollment/quiz counts
   - "Student Shortcuts" card with student-specific buttons
   - Navbar should show "My Enrollments" link

### 4. Test Security
1. Try accessing `/dashboard` without login - should redirect to login
2. Test logout functionality - should clear session and redirect to login
3. Test session regeneration on login

## Expected Behavior

- Each role sees different dashboard content
- Navbar adapts to user role
- Session-based authentication works
- Proper redirects for unauthorized access
- Clean logout functionality
