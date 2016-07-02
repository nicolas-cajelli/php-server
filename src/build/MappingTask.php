<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/23/16
 * Time: 11:18 PM
 */

namespace nicolascajelli\server\build;


use nicolascajelli\server\di\Service;
use ReflectionClass;
use ReflectionMethod;
use stdClass;

class MappingTask implements BuildTask
{
    protected $excludedDirs = ['.', '..', 'examples', 'bin', 'phpdocumentor', 'phpspec', 'test', 'tests', 'Test', 'Tests', 'composer'];
    protected $mappingsFile = 'build/path_mapping.php';
    protected $servicesFile = 'build/services_mapping.php';

    public function __construct($dir)
    {
        $files = $this->scan($dir);
        $paths = new stdClass();
        $paths->simple = [];
        $paths->dynamic = [];
        $dependencies = new DependenciesMap();
        foreach($files as $file) {
            $class = new ClassFile($file);

            if (! $class->parsed()) {
                continue;
            }
            $reader = new \DocBlockReader\Reader(strval($class));
            if ($reader->getParameter('Controller')) {
                $this->generateControllerMappings($reader, $class, $paths, $dependencies);
            } elseif ($reader->getParameter('NonSharedService')) {
                $dependencies->add(strval($class), $this->getReferences(strval($class)), false);
            } elseif ($reader->getParameter('Inject')) {
                $dependencies->add(strval($class), $this->getReferences(strval($class)));
            }
        }
        $this->writePathMapping($paths->simple, $paths->dynamic);
        $this->writeServicesMapping($dependencies);

    }

    protected function getReferences($class)
    {
        $ref = new ReflectionClass($class);
        $methods = $ref->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ($method->getName() == '__construct') {
                return $method->getParameters();
            }
        }
        return [];
    }

    function scan($dir) {
        $content = scandir($dir);
        $files = [];
        foreach ($content as $child) {
            if (is_dir($dir . '/' . $child)) {
                if (! in_array($child, $this->excludedDirs)) {
                    $files = array_merge($files, $this->scan($dir . '/' . $child));
                }
            } else {
                if (substr($child, -4) == '.php' && $child != 'build.php') {
                    $files[] = $dir . '/'  . $child;
                }
            }
        }
        return $files;
    }
    function writePathMapping($simplePaths, $dynamicPaths) {

        $pathMapping = "<?php \n\nreturn " . var_export(['simple_paths' => $simplePaths, 'dynamic_paths' => $dynamicPaths], true) . ';';

        file_put_contents($this->mappingsFile, $pathMapping);
    }
    /**
     * @param $reader
     * @param $className
     * @param $simplePaths
     * @param $dynamicPaths
     * @return array
     */
    protected function generateControllerMappings(\DocBlockReader\Reader $reader, ClassFile $class, stdClass $paths, DependenciesMap $dependencies)
    {
        $className = strval($class);
        $basePath = $reader->getParameter('Path');

        $ref = new ReflectionClass($className);
        $methods = $ref->getMethods(ReflectionMethod::IS_PUBLIC);
        $hasConstructor = false;
        foreach ($methods as $method) {
            if ($method->getName() == '__construct') {
                $dependencies->add($className, $method->getParameters());
                $hasConstructor = true;
                continue;
            }

            $returnType = strval($method->getReturnType());
            $methodReader = new \DocBlockReader\Reader($className, $method->getName());

            $methodType = strtolower($methodReader->getParameter('Method'));
            $path = $basePath;
            if (!$methodReader->getParameter('DynamicPath')) {
                $path .= $methodReader->getParameter('Path');
                if (!isset($paths->simple[$path])) {
                    $paths->simple[$path] = [
                        'controller' => $className,
                        'args' => []
                    ];
                }
                $args = [];
                foreach ($method->getParameters() as $param) {
                    $args[] = strval($param->getType());

                }
                $paths->simple[$path][$methodType] = [
                    'method' => $method->getName(),
                    'args' => $args,
                    'return' => $returnType
                ];
            } else {
                $path .= $methodReader->getParameter('DynamicPath');
                if (!isset($paths->dynamic[$path])) {
                    $paths->dynamic[$path] = [
                        'controller' => $className,
                    ];
                }
                $paths->dynamic[$path][$methodType] = [
                    'method' => $method->getName(),
                    'return' => $returnType
                ];
            }
        }
        if (! $hasConstructor) {
            $dependencies->add($className, []);
        }
    }

    function writeServicesMapping(DependenciesMap $map) {
        $mapping = "<?php\nuse " . Service::class . ";\n";
        $mapping .= "return [\n";
        foreach ($map->getServices() as $service => $config) {
            $serviceName = trim(str_replace('\\', '.', $service), '.');
            $mapping.= "\tService::create('$serviceName', \\$service::class)";
            if (! $config['shared']) {
                $mapping .= "\n\t\t->setShared(false)";
            }
            if (! empty($config['dependencies'])) {
                $mapping .= "\n\t\t->withReferences([";
                $first = true;
                foreach ($config['dependencies'] as $dependency) {
                    if (! $first) {
                        $mapping .= ', ';
                    } else {
                        $first = false;
                    }
                    $depName = trim(str_replace('\\', '.', $dependency), '.');
                    $mapping .= "'$depName'";
                }
                $mapping .= "])";
            }
            $mapping .= ",\n";
        }
        $mapping .= "];\n";
        file_put_contents($this->servicesFile, $mapping);
    }
}