<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    // Purpose: Allows requests only when the signed-in user has an allowed role.
    public function before(RequestInterface $request, $arguments = null)
    {
        $userRole = session()->get('role');

        if (! $userRole) {
            return redirect()->to('/login');
        }

        if (! empty($arguments) && ! in_array(strtolower($userRole), array_map('strtolower', $arguments))) {
            session()->setFlashdata('error', 'Unauthorized access: You do not have permission to view that resource.');
            return redirect()->to('/dashboard');
        }
    } // End of before()

    // Purpose: Performs no post-request processing for role authorization.
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed post-request
    } // End of after()
}