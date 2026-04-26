<?php

namespace Core;

use Core\Builders\RequestBuilder;
use Core\Pipeline\Handler;
use Core\Pipeline\Pipeline;
use Core\Request;

class App
{
    private Pipeline $pipeline;

    public function __construct()
    {
        $this->pipeline = new Pipeline();
    }

    public function use(Handler $middleware): self
    {
        $this->pipeline->pipe($middleware);
        return $this;
    }

    public function run(?Request $request = null): void
    {
        if ($request === null) {
            $request = RequestBuilder::build();
        }

        $response = $this->pipeline->process($request);

        $response->send();
    }
}
