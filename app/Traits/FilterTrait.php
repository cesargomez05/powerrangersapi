<?php

namespace App\Traits;

use CodeIgniter\Config\Services;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

trait FilterTrait
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = $request->getUri();
        // Se obtiene los segmentos de la URL, omitiendo la parte de "api" y del nombre del recurso "module".
        $segments = array_slice($uri->getSegments(), 2);

        $result = call_user_func_array([$this, 'checkRecord'], $segments);
        if (isset($result)) {
            return $result;
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Not apply action after filter
    }

    public static function isPublic()
    {
        $session = Services::session();
        return ($session->get('type') ?? '') == 'public';
    }

    protected static function throwError($code, $error)
    {
        return Services::response()->setStatusCode($code)->setJSON(['status' => $code, 'error' => $error]);
    }
}
