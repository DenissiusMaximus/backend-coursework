<?php

namespace Controllers;

use Core\Attributes\Route;
use Core\MVC\ControllerBase;
use Core\Response;

#[Route('transaction')]
class TransactionController extends ControllerBase
{

    public function index(): Response
    {
        $data = [
            'title' => 'Мої транзакції',
            'balance' => 9500
        ];

        return $this->view('transactions/index', $data);
    }
}
