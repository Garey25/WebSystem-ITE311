<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session('isLoggedIn')) {
            return redirect()->to(site_url('login'))->with('error', 'Please log in to access this page.');
        }
        
        // Check if user account is still active
        $userId = session('user_id');
        if ($userId) {
            $userModel = new UserModel();
            $user = $userModel->find($userId);
            
            if ($user && isset($user['status']) && $user['status'] === 'inactive') {
                // Destroy session and redirect to login
                session()->destroy();
                return redirect()->to(site_url('login'))->with('error', 'Your account has been deactivated. Please contact an administrator.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
