<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Token;
use Ramsey\Uuid\Uuid;

class TokenController extends Controller
{

    public function create()
    {
        $token = new Token();
        $token->token = (string) Uuid::uuid4();

        if ($token->save()) {
            return [
                'success' => true,
                'token' => $token->token
            ];
        }

        return [
            'success' => false,
            'token' => null
        ];
    }
}
