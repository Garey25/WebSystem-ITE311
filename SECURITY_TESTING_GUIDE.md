# Security Testing Guide for Course Enrollment System

## Overview
This document outlines the security testing procedures for the course enrollment system implemented in the ITE311-REYES project.

## Test Environment Setup
1. Ensure the application is running on `http://localhost:8080`
2. Have access to browser developer tools
3. Have Postman or similar API testing tool available
4. Create test user accounts with different roles (student, teacher, admin)

## Security Tests

### 1. Authorization Bypass Testing

#### Test 1.1: Unauthenticated Access
**Objective**: Verify that unauthenticated users cannot access the enrollment endpoint.

**Steps**:
1. Log out of the application
2. Open browser developer tools (F12)
3. Go to Console tab
4. Execute the following JavaScript:
```javascript
fetch('/course/enroll', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'course_id=1'
})
.then(response => response.json())
.then(data => console.log(data));
```

**Expected Result**: Should return an error message indicating the user must be logged in.

#### Test 1.2: Direct URL Access
**Objective**: Verify that direct POST requests to the enrollment endpoint are blocked.

**Steps**:
1. Using Postman or curl, send a POST request to `http://localhost:8080/course/enroll`
2. Include only `course_id=1` in the body
3. Do not include any authentication headers

**Expected Result**: Should return 401 Unauthorized or redirect to login page.

### 2. SQL Injection Testing

#### Test 2.1: Malicious Course ID
**Objective**: Verify that the application properly validates and sanitizes input.

**Steps**:
1. Log in as a student
2. Open browser developer tools
3. Go to Network tab
4. Click on an "Enroll" button
5. Right-click on the POST request and select "Copy as cURL"
6. Modify the course_id parameter to: `1 OR 1=1`
7. Execute the modified request

**Expected Result**: Should return an error message about invalid course ID, not execute the SQL injection.

#### Test 2.2: Non-numeric Course ID
**Objective**: Test input validation for non-numeric values.

**Steps**:
1. Log in as a student
2. Use browser console to send a request with non-numeric course_id:
```javascript
fetch('/course/enroll', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
    },
    body: 'course_id=abc'
})
.then(response => response.json())
.then(data => console.log(data));
```

**Expected Result**: Should return an error message about invalid course ID.

### 3. CSRF (Cross-Site Request Forgery) Testing

#### Test 3.1: Missing CSRF Token
**Objective**: Verify that CSRF protection is working.

**Steps**:
1. Log in as a student
2. Open browser developer tools
3. Go to Console tab
4. Execute the following JavaScript (without CSRF token):
```javascript
fetch('/course/enroll', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
    },
    body: 'course_id=1'
})
.then(response => response.json())
.then(data => console.log(data));
```

**Expected Result**: Should return a CSRF token validation error.

#### Test 3.2: Invalid CSRF Token
**Objective**: Test with an invalid CSRF token.

**Steps**:
1. Log in as a student
2. Get a valid CSRF token from the page source
3. Modify the token to make it invalid
4. Send the request with the invalid token

**Expected Result**: Should return a CSRF token validation error.

### 4. Data Tampering Testing

#### Test 4.1: User ID Manipulation
**Objective**: Verify that users cannot enroll other users in courses.

**Steps**:
1. Log in as a student (note the user_id)
2. Open browser developer tools
3. Try to modify the request to include a different user_id
4. Note: The server-side code should use the session user_id, not the client-provided one

**Expected Result**: The enrollment should be created for the logged-in user, not the manipulated user_id.

### 5. Input Validation Testing

#### Test 5.1: Non-existent Course
**Objective**: Verify that the application validates course existence.

**Steps**:
1. Log in as a student
2. Try to enroll in a course with ID 99999 (assuming it doesn't exist)
3. Use browser console or modify the AJAX request

**Expected Result**: Should return an error message that the course was not found.

#### Test 5.2: Negative Course ID
**Objective**: Test with negative course ID values.

**Steps**:
1. Log in as a student
2. Try to enroll in a course with ID -1

**Expected Result**: Should return an error message about invalid course ID.

#### Test 5.3: Duplicate Enrollment
**Objective**: Verify that users cannot enroll in the same course twice.

**Steps**:
1. Log in as a student
2. Enroll in a course
3. Try to enroll in the same course again

**Expected Result**: Should return an error message that the user is already enrolled.

## Test Results Documentation

For each test, document:
- Test ID and name
- Date and time of testing
- Steps performed
- Expected result
- Actual result
- Pass/Fail status
- Notes or observations

## Security Recommendations

1. **Input Validation**: All inputs should be validated on both client and server side
2. **Authentication**: Ensure all protected endpoints verify user authentication
3. **Authorization**: Verify that users can only perform actions they're authorized for
4. **CSRF Protection**: Keep CSRF protection enabled for all state-changing operations
5. **SQL Injection**: Use prepared statements and parameterized queries
6. **Rate Limiting**: Consider implementing rate limiting for enrollment requests
7. **Logging**: Log all enrollment attempts for security monitoring

## Conclusion

After completing all security tests, ensure that:
- All tests pass as expected
- No security vulnerabilities are found
- The application properly handles malicious input
- User data is protected from unauthorized access
- The system is resilient to common attack vectors
