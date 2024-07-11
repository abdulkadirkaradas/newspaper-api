<?php

namespace App\Http\Middleware;

use Closure;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeHtmlContent
{
    /**
     * Verify and Sanitize an incoming HTML content requests.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post') && $request->has('content')) {
            $config = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($config);

            $cleanHtml = $purifier->purify($request->input('content'));

            $request->merge([
                'news_content' => [
                    "title" => $request->input('title'),
                    "content" => $cleanHtml
                ]
            ]);
        }

        return $next($request);
    }
}