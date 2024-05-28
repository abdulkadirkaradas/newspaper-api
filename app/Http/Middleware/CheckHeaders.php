<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('Content-Type') !== 'application/json') {
            return response()->json($this->errorMessage(BAD_REQUEST, INCORRECT_CONTENT_TYPE));
        }

        $bodyContent = $request->getContent();
        if ($bodyContent !== "") {
            $data = json_decode($bodyContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json($this->errorMessage(BAD_REQUEST, "The fields must be made with JSON!"));
            }

            $request['bodyContent'] = $data;
        }

        return $next($request);
    }

    private function errorMessage(int $status, string $message): array
    {
        return [
            "status" => $status,
            "message" => $message
        ];
    }
}
