<?php

namespace Controllers;

use Core\Attributes\Route;
use Core\MVC\ControllerBase;
use Core\Request;
use Core\Response;
use Models\Category;
use Repositories\CategoryRepository;

#[Route('categories')]
class CategoryController extends ControllerBase
{
    private CategoryRepository $repo;

    public function __construct()
    {
        $this->repo = new CategoryRepository();
    }

    public function index(): Response
    {
        return $this->view('categories/index', [
            'title' => 'Категорії',
            'categories' => $this->repo->findAll(),
        ]);
    }

    #[Route('create', 'GET')]
    public function create(Request $request): Response
    {
        if ($request->user->role !== 'admin') {
            return $this->redirect('/categories');
        }

        return $this->view('categories/create', [
            'title' => 'Нова категорія',
        ]);
    }

    #[Route('store', 'POST')]
    public function store(Request $request): Response
    {
        if ($request->user->role !== 'admin') {
            return $this->redirect('/categories');
        }

        $this->repo->create(new Category(
            name: $request->post('name'),
        ));

        return $this->redirect('/categories');
    }

    #[Route('edit/{id}', 'GET')]
    public function edit(int $id, Request $request): Response
    {
        if ($request->user->role !== 'admin') {
            return $this->redirect('/categories');
        }

        return $this->view('categories/edit', [
            'title' => 'Редагувати категорію',
            'category' => $this->repo->findById($id),
        ]);
    }

    #[Route('update/{id}', 'POST')]
    public function update(int $id, Request $request): Response
    {
        if ($request->user->role !== 'admin') {
            return $this->redirect('/categories');
        }

        $this->repo->update(new Category(
            name: $request->post('name'),
            id: $id,
        ));

        return $this->redirect('/categories');
    }

    #[Route('delete/{id}', 'POST')]
    public function destroy(int $id, Request $request): Response
    {
        if ($request->user->role !== 'admin') {
            return $this->redirect('/categories');
        }

        $this->repo->delete($id);

        return $this->redirect('/categories');
    }
}
