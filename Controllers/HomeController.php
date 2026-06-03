<?php

namespace Controllers;

use Core\Attributes\Route;
use Core\MVC\ControllerBase;
use Core\Response;
use Core\Request;

use Repositories\SourceRepository;
use Repositories\TransactionRepository;
use Repositories\CategoryRepository;
use Repositories\AimRepository;

#[Route('/')]
class HomeController extends ControllerBase
{
    public function index(Request $request): Response
    {
        $userId = $request->user->id ?? 0;
        $role = $request->user->role ?? 'user';

        $sourceRepo = new SourceRepository();
        $transactionRepo = new TransactionRepository();
        $categoryRepo = new CategoryRepository();
        $aimRepo = new AimRepository();

        if ($role === 'admin') {
            $sources = $sourceRepo->findAll();
            $transactions = $transactionRepo->getPaginatedAll(10, 0);
            $aims = $aimRepo->findAll();
        } else {
            $sources = $sourceRepo->findByUserId($userId);
            $transactions = $transactionRepo->getPaginatedByUserId($userId, 10, 0);
            $aims = $aimRepo->findByUserId($userId);
        }

        $categories = $categoryRepo->findAll();

        $totalBalance = array_reduce($sources, fn($carry, $source) => $carry + $source->balance, 0.0);

        return $this->view('home/index', [
            'title' => 'Головна',
            'sources' => $sources,
            'totalBalance' => $totalBalance,
            'transactions' => $transactions,
            'categories' => $categories,
            'aims' => $aims,
        ]);
    }
}
