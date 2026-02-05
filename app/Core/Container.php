<?php
namespace App\Core;

class Container
{
    private $bindings = [];
    private $instances = [];
    private $aliases = [];
    
    public function bind($abstract, $concrete = null, $shared = false)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }
        
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => $shared
        ];
        
        return $this;
    }
    
    public function singleton($abstract, $concrete = null)
    {
        return $this->bind($abstract, $concrete, true);
    }
    
    public function instance($abstract, $instance)
    {
        $this->instances[$abstract] = $instance;
        return $this;
    }
    
    public function alias($abstract, $alias)
    {
        $this->aliases[$alias] = $abstract;
        return $this;
    }
    
    public function make($abstract, array $parameters = [])
    {
        $abstract = $this->getAlias($abstract);
        
        // Return if already resolved as singleton
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }
        
        // Get concrete implementation
        $concrete = $this->getConcrete($abstract);
        
        // Build the object
        $object = $this->build($concrete, $parameters);
        
        // Store if shared
        if (isset($this->bindings[$abstract]['shared']) && $this->bindings[$abstract]['shared']) {
            $this->instances[$abstract] = $object;
        }
        
        return $object;
    }
    
    public function get($abstract)
    {
        return $this->make($abstract);
    }
    
    public function has($abstract)
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }
    
    private function getAlias($abstract)
    {
        return $this->aliases[$abstract] ?? $abstract;
    }
    
    private function getConcrete($abstract)
    {
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]['concrete'];
        }
        
        return $abstract;
    }
    
    private function build($concrete, $parameters)
    {
        if ($concrete instanceof \Closure) {
            // Allow factories with flexible signatures:
            // fn() => ..., fn(Container $c) => ..., fn(Container $c, array $params) => ...
            $ref = new \ReflectionFunction($concrete);
            $argc = $ref->getNumberOfParameters();
            if ($argc === 0) {
                return $concrete();
            }
            if ($argc === 1) {
                return $concrete($this);
            }
            return $concrete($this, $parameters);
        }
        
        $reflector = new \ReflectionClass($concrete);
        
        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class {$concrete} is not instantiable.");
        }
        
        $constructor = $reflector->getConstructor();
        
        if (is_null($constructor)) {
            return new $concrete;
        }
        
        $dependencies = $this->resolveDependencies($constructor->getParameters(), $parameters);
        
        return $reflector->newInstanceArgs($dependencies);
    }
    
    private function resolveDependencies($parameters, $overrideParameters)
    {
        $dependencies = [];
        
        foreach ($parameters as $parameter) {
            if (array_key_exists($parameter->name, $overrideParameters)) {
                $dependencies[] = $overrideParameters[$parameter->name];
                continue;
            }
            
            $dependency = $parameter->getType();
            
            if (is_null($dependency) || $dependency->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new \Exception("Cannot resolve dependency {$parameter->name}");
                }
            } else {
                $dependencies[] = $this->make($dependency->getName());
            }
        }
        
        return $dependencies;
    }
}