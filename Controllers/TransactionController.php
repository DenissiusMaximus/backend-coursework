<?php

namespace Controllers;

use Core\Attributes\Route;
use Core\MVC\ControllerBase;
use Core\Request;
use Core\Response;
use Models\Transaction;
use Repositories\TransactionRepository;
use Repositories\CategoryRepository;
use Repositories\SourceRepository;

#[Route('transactions')]
class TransactionController extends ControllerBase
{
    private TransactionRepository $repo;
    private CategoryRepository $categoryRepo;
    private SourceRepository $sourceRepo;

    public function __construct()
    {
        $this->repo = new TransactionRepository();
        $this->categoryRepo = new CategoryRepository();
        $this->sourceRepo = new SourceRepository();
    }

    public function index(Request $request): Response
    {
        $userId = $request->user->id ?? 0;
        $role = $request->user->role ?? 'user';

        if ($role === 'admin') {
            $transactions = $this->repo->findAll();
            $sources = $this->sourceRepo->findAll();
        } else {
            $transactions = $this->repo->findByUserId($userId);
            $sources = $this->sourceRepo->findByUserId($userId);
        }

        $categories = $this->categoryRepo->findAll();

        return $this->view('transactions/index', [
            'title' => 'Транзакції',
            'transactions' => $transactions,
            'categories' => $categories,
            'sources' => $sources,
        ]);
    }

    #[Route('create', 'GET')]
    public function create(Request $request): Response
    {
        $userId = $request->user->id ?? 0;
        $role = $request->user->role ?? 'user';

        $categories = $this->categoryRepo->findAll();
        $sources = $role === 'admin' ? $this->sourceRepo->findAll() : $this->sourceRepo->findByUserId($userId);

        return $this->view('transactions/create', [
            'title' => 'Нова транзакція',
            'categories' => $categories,
            'sources' => $sources,
        ]);
    }

    #[Route('store', 'POST')]
    public function store(Request $request): Response
    {
        $transaction = new Transaction(
            user_id: (int) ($request->user->id ?? 1),
            source_id: (int) $request->post('source_id'),
            category_id: (int) $request->post('category_id'),
            amount: (float) $request->post('amount'),
            type: $request->post('type') ?? 'expense',
            comment: $request->post('comment'),
            date: $request->post('date'),
        );

        $this->repo->create($transaction);

        return $this->redirect('/transactions');
    }

    #[Route('edit/{id}', 'GET')]
    public function edit(int $id, Request $request): Response
    {
        $userId = $request->user->id ?? 0;
        $role = $request->user->role ?? 'user';

        $transaction = $this->repo->findById($id);
        if (!$transaction || ($role !== 'admin' && $transaction->user_id !== $userId)) {
            return $this->redirect('/transactions');
        }
        $categories = $this->categoryRepo->findAll();
        $sources = $role === 'admin' ? $this->sourceRepo->findAll() : $this->sourceRepo->findByUserId($userId);

        return $this->view('transactions/edit', [
            'title' => 'Редагувати транзакцію',
            'transaction' => $transaction,
            'categories' => $categories,
            'sources' => $sources,
        ]);
    }

    #[Route('update/{id}', 'POST')]
    public function update(int $id, Request $request): Response
    {
        $userId = $request->user->id ?? 0;
        $role = $request->user->role ?? 'user';

        $existing = $this->repo->findById($id);
        if (!$existing || ($role !== 'admin' && $existing->user_id !== $userId)) {
            return $this->redirect('/transactions');
        }
        $transaction = new Transaction(
            user_id: (int) ($request->user->id ?? 1),
            source_id: (int) $request->post('source_id'),
            category_id: (int) $request->post('category_id'),
            amount: (float) $request->post('amount'),
            type: $request->post('type') ?? 'expense',
            comment: $request->post('comment'),
            date: $request->post('date'),
            id: $id,
        );

        $this->repo->update($transaction);

        return $this->redirect('/transactions');
    }

    #[Route('delete/{id}', 'POST')]
    public function destroy(int $id, Request $request): Response
    {
        $userId = $request->user->id ?? 0;
        $role = $request->user->role ?? 'user';

        $existing = $this->repo->findById($id);
        if ($existing && ($role === 'admin' || $existing->user_id === $userId)) {
            $this->repo->delete($id);
        }

        return $this->redirect('/transactions');
    }

    #[Route('api', 'GET')]
    public function api(Request $request): Response
    {
        $userId = $request->user->id ?? 0;
        $role = $request->user->role ?? 'user';

        $limit = (int) ($request->get('limit') ?? 10);
        $offset = (int) ($request->get('offset') ?? 0);

        if ($role === 'admin') {
            $transactions = $this->repo->getPaginatedAll($limit, $offset);
        } else {
            $transactions = $this->repo->getPaginatedByUserId($userId, $limit, $offset);
        }

        $categories = $this->categoryRepo->findAll();
        $sources = $this->sourceRepo->findAll();

        $categoryMap = [];
        foreach ($categories as $cat) {
            $categoryMap[$cat->id] = $cat->name;
        }

        $sourceMap = [];
        foreach ($sources as $src) {
            $sourceMap[$src->id] = $src->name;
        }

        $data = array_map(function ($t) use ($categoryMap, $sourceMap) {
            return [
                'id' => $t->id,
                'amount' => $t->amount,
                'comment' => $t->comment,
                'date' => $t->date,
                'category_name' => $categoryMap[$t->category_id] ?? 'Невідомо',
                'source_name' => $sourceMap[$t->source_id] ?? 'Невідомо',
                'is_income' => $t->type === 'income',
            ];
        }, $transactions);

        return $this->json($data);
    }

    #[Route('api/filtered', 'GET')]
    public function apiFiltered(Request $request): Response
    {
        $userId = $request->user->id ?? 0;
        $role = $request->user->role ?? 'user';

        $limit = (int) ($request->get('limit') ?? 10);
        $offset = (int) ($request->get('offset') ?? 0);
        
        $categoryName = $request->get('category_name') ?: null;
        $type = $request->get('type') ?: null;
        $date = $request->get('date') ?: null;

        if ($role === 'admin') {
            $transactions = $this->repo->getFilteredPaginatedAll($limit, $offset, $categoryName, $type, $date);
        } else {
            $transactions = $this->repo->getFilteredPaginatedByUserId($userId, $limit, $offset, $categoryName, $type, $date);
        }

        $categories = $this->categoryRepo->findAll();
        $sources = $this->sourceRepo->findAll();

        $categoryMap = [];
        foreach ($categories as $cat) {
            $categoryMap[$cat->id] = $cat->name;
        }

        $sourceMap = [];
        foreach ($sources as $src) {
            $sourceMap[$src->id] = $src->name;
        }

        $data = array_map(function ($t) use ($categoryMap, $sourceMap) {
            return [
                'id' => $t->id,
                'amount' => $t->amount,
                'comment' => $t->comment,
                'date' => $t->date,
                'category_name' => $categoryMap[$t->category_id] ?? 'Невідомо',
                'source_name' => $sourceMap[$t->source_id] ?? 'Невідомо',
                'is_income' => $t->type === 'income',
            ];
        }, $transactions);

        return $this->json($data);
    }
}
