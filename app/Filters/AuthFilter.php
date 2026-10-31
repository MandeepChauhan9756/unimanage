<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    // Purpose: Redirects unauthenticated requests to the login page.
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please log in to access this page.');
            return redirect()->to('/login');
        }
    } // End of before()

    // Purpose: Performs no post-request processing for authentication.
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed post-request
    } // End of after()
}