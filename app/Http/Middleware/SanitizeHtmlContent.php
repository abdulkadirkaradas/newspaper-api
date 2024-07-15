<?php

namespace App\Http\Middleware;

use Closure;
use HTMLPurifier;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeHtmlContent
{
    protected $purifier;

    public function __construct(HTMLPurifier $purifier)
    {
        $this->purifier = $purifier;
    }

    /**
     * Verify and Sanitize an incoming HTML content requests.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post') && $request->has('content')) {
            $cleanHtml = $this->purifier->purify($request->input('content'));

            $request['news_content'] = [
                "title" => $request->input('title'),
                "content" => $cleanHtml
            ];
        }

        return $next($request);
    }
}