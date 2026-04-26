<?php

namespace Core\Pipeline;

use Core\Request;
use Core\Response;

class Pipeline
{
    private array $middlewares = [];

    public function pipe(Handler $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function process(Request $request): ?Response
    {
        if (empty($this->middlewares)) {
            return null;
        }

        $count = count($this->middlewares);
        for ($i = 0; $i < $count - 1; $i++) {
            $this->middlewares[$i]->setNext($this->middlewares[$i + 1]);
        }

        return $this->middlewares[0]->handle($request);
    }
}
