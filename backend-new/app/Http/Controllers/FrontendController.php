<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FrontendController extends Controller
{
    public function serve(Request $request)
    {
        $path = $request->path();
        $requestUri = $request->getRequestUri();

        // 1. DEADLOCK PROTECTION: Redirect all static files to Port 3000
        // This is THE MOST IMPORTANT fix. It prevents assets from clogging Laravel workers.
        $isAsset = preg_match('/\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|json|map|webmanifest)$/i', $path);
        if ($isAsset || str_starts_with($path, '_next/')) {
            return redirect('http://127.0.0.1:3000' . $requestUri, 307);
        }

        // 2. HTML PROXY: Proxy the main page HTML so the browser URL stays as Port 8000
        $url = "http://127.0.0.1:3000" . $requestUri;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15); // Faster timeout for HTML
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request->method());
        
        $forwardedHeaders = [];
        foreach ($request->headers->all() as $key => $values) {
            foreach ($values as $value) {
                if (!in_array(strtolower($key), ['host', 'connection', 'content-length', 'accept-encoding'])) {
                    $forwardedHeaders[] = "$key: $value";
                }
            }
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $forwardedHeaders);

        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getContent());
        }

        $content = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($content === false || $statusCode === 0) {
            return response('Server Busy / Connecting to Next.js...', 503);
        }

        return response($content, $statusCode)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('X-Proxy-Status', 'Stable-Hybrid');
    }
}
