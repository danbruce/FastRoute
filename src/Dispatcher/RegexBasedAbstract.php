<?php

namespace FastRoute\Dispatcher;

use FastRoute\Dispatcher;

abstract class RegexBasedAbstract implements Dispatcher {
    protected $staticRouteMap;
    protected $variableRouteData;

    protected abstract function dispatchVariableRoute($routeData, $uri);

    public function dispatch($httpMethod, $uri) {
        if (isset($this->staticRouteMap[$httpMethod][$uri])) {
            $handler = $this->staticRouteMap[$httpMethod][$uri];
            return array(self::FOUND, $handler, array());
        } else if ($httpMethod === 'HEAD' && isset($this->staticRouteMap['GET'][$uri])) {
            $handler = $this->staticRouteMap['GET'][$uri];
            return array(self::FOUND, $handler, array());
        }

        $varRouteData = $this->variableRouteData;
        if (isset($varRouteData[$httpMethod])) {
            $result = $this->dispatchVariableRoute($varRouteData[$httpMethod], $uri);
            if ($result[0] === self::FOUND) {
                return $result;
            }
        } else if ($httpMethod === 'HEAD' && isset($varRouteData['GET'])) {
            $result = $this->dispatchVariableRoute($varRouteData['GET'], $uri);
            if ($result[0] === self::FOUND) {
                return $result;
            }
        }

        // Find allowed methods for this URI by matching against all other HTTP methods as well
        $allowedMethods = array();

        foreach ($this->staticRouteMap as $method => $uriMap) {
            if ($method !== $httpMethod && isset($uriMap[$uri])) {
                $allowedMethods[] = $method;
            }
        }

        foreach ($varRouteData as $method => $routeData) {
            if ($method === $httpMethod) {
                continue;
            }

            $result = $this->dispatchVariableRoute($routeData, $uri);
            if ($result[0] === self::FOUND) {
                $allowedMethods[] = $method;
            }
        }

        // If there are no allowed methods the route simply does not exist
        if ($allowedMethods) {
            return array(self::METHOD_NOT_ALLOWED, $allowedMethods);
        } else {
            return array(self::NOT_FOUND);
        }
    }
}
