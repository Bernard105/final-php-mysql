<?php
namespace App\Core;

/**
 * Minimal router supporting:
 * - GET/POST routes
 * - Path parameters like /product/{id}
 * - Route groups with middleware (auth/admin)
 */
class Router
{
    /** @var array<string, array<int, array<string,mixed>>> */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    private Container $container;

    /** @var array<int, string> */
    private array $groupMiddlewareStack = [];

    private string $groupPrefix = '';

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function get(string $path, $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    public function post(string $path, $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    /**
     * Group routes under shared options.
     * Example: $router->group(['middleware' => 'auth'], function($router){ ... });
     */
    public function group(array $options, callable $callback): void
    {
        $prev = $this->groupMiddlewareStack;
        $prevPrefix = $this->groupPrefix;

        if (isset($options['prefix']) && is_string($options['prefix'])) {
            $prefix = '/' . trim($options['prefix'], '/');
            if ($prefix === '/') {
                $prefix = '';
            }
            $this->groupPrefix = rtrim($this->groupPrefix, '/') . $prefix;
        }

        if (isset($options['middleware'])) {
            $mw = $options['middleware'];
            if (is_string($mw)) {
                $this->groupMiddlewareStack[] = $mw;
            } elseif (is_array($mw)) {
                foreach ($mw as $m) {
                    if (is_string($m)) {
                        $this->groupMiddlewareStack[] = $m;
                    }
                }
            }
        }

        $callback($this);
        $this->groupMiddlewareStack = $prev;
        $this->groupPrefix = $prevPrefix;
    }

    private function addRoute(string $method, string $path, $callback): void
    {
        if ($this->groupPrefix) {
            $path = rtrim($this->groupPrefix, '/') . '/' . ltrim($path, '/');
            $path = '/' . trim($path, '/');
            if ($path === '//') {
                $path = '/';
            }
            if ($path === '') {
                $path = '/';
            }
        }

        [$regex, $paramNames] = $this->compilePath($path);

        $this->routes[$method][] = [
            'path' => $path,
            'regex' => $regex,
            'params' => $paramNames,
            'callback' => $callback,
            'middleware' => $this->groupMiddlewareStack,
        ];
    }

    /**
     * Convert /product/{id} into regex and capture param names.
     * @return array{0:string,1:array<int,string>}
     */
    private function compilePath(string $path): array
    {
        $paramNames = [];

        $regex = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', function ($m) use (&$paramNames) {
            $paramNames[] = $m[1];
            return '([^/]+)';
        }, $path);

        // Exact match
        $regex = '#^' . $regex . '$#';
        return [$regex, $paramNames];
    }

    public function dispatch(Request $request): void
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $route) {
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            // Extract params in the same order they were defined
            $params = [];
            foreach ($route['params'] as $i => $name) {
                $params[$name] = $matches[$i + 1] ?? null;
            }

            $handler = function (Request $req) use ($route, $params) {
                return $this->invokeCallback($route['callback'], $req, $params);
            };

            // Build middleware pipeline (last added middleware runs last)
            $pipeline = $handler;
            $middlewareList = $route['middleware'] ?? [];

            for ($i = count($middlewareList) - 1; $i >= 0; $i--) {
                $mwName = $middlewareList[$i];
                $middleware = $this->resolveMiddleware($mwName);

                $next = $pipeline;
                $pipeline = function (Request $req) use ($middleware, $next) {
                    return $middleware->handle($req, $next);
                };
            }

            // Execute pipeline and output returned content (most controllers return HTML strings).
            $result = $pipeline($request);
            if (is_string($result)) {
                echo $result;
            }
            return;
        }

        http_response_code(404);
        echo 'Page not found';
    }

    private function resolveMiddleware(string $name)
    {
        $map = [
            'auth' => \App\Middleware\AuthMiddleware::class,
            'admin' => \App\Middleware\AdminMiddleware::class,
        ];

        $class = $map[$name] ?? $name;

        if (!class_exists($class)) {
            throw new \Exception("Middleware {$class} not found");
        }

        return $this->container->make($class);
    }

    /**
     * Invoke a route callback.
     * - Controller callbacks: [ControllerClass::class, 'method']
     * - Closure callbacks: function(){}
     */
    private function invokeCallback($callback, Request $request, array $routeParams)
    {
        if (is_array($callback)) {
            [$controllerClass, $method] = $callback;

            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller {$controllerClass} not found");
            }

            $controller = new $controllerClass($this->container);

            if (!method_exists($controller, $method)) {
                throw new \Exception("Method {$method} not found in {$controllerClass}");
            }

            // Match controller method parameters by name/type
            $ref = new \ReflectionMethod($controller, $method);
            $args = [];
            foreach ($ref->getParameters() as $p) {
                $type = $p->getType();
                if ($type && !$type->isBuiltin() && $type->getName() === Request::class) {
                    $args[] = $request;
                    continue;
                }
                $name = $p->getName();
                if (array_key_exists($name, $routeParams)) {
                    $args[] = $routeParams[$name];
                } elseif ($p->isDefaultValueAvailable()) {
                    $args[] = $p->getDefaultValue();
                } else {
                    // Fallback: if route has exactly one param, pass it
                    if (count($routeParams) === 1) {
                        $args[] = array_values($routeParams)[0];
                    } else {
                        $args[] = null;
                    }
                }
            }

            return $ref->invokeArgs($controller, $args);
        }

        if (is_callable($callback)) {
            // If closure accepts Request, pass it
            $ref = new \ReflectionFunction(\Closure::fromCallable($callback));
            $args = [];
            foreach ($ref->getParameters() as $p) {
                $type = $p->getType();
                if ($type && !$type->isBuiltin() && $type->getName() === Request::class) {
                    $args[] = $request;
                } elseif (array_key_exists($p->getName(), $routeParams)) {
                    $args[] = $routeParams[$p->getName()];
                }
            }
            return $callback(...$args);
        }

        throw new \Exception('Invalid route callback');
    }
}
