<?php

namespace Controllers;

use Core\Attributes\Route;
use Core\Response;

#[Route('transaction')]
class TransactionController
{
    #[Route("Get", '/convert/{amount}/{toCurrency}')]
    public function convert(float $amount, string $toCurrency): Response
    {
        $result = $amount * 40;

        return new Response(body: "$result $toCurrency");
    }
}
