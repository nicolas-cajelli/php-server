<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/23/16
 * Time: 11:36 PM
 */

namespace nicolascajelli\server\di;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class Service
{
    /**
     * @var string[]
     */
    protected $references = [];
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $class;

    protected $shared;

    /**
     * Service constructor.
     * @param string $id
     * @param string $class
     */
    public function __construct(string $id, string $class)
    {
        $this->id = $id;
        $this->class = $class;
    }

    public static function create($id, $class) : Service
    {
        return new static($id, $class);
    }

    public function setShared($flag) {
        $this->shared = $flag;
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    public function register(ContainerBuilder $container)
    {
        $def = $container->register($this->getId(), $this->getClass());
        foreach ($this->references as $reference) {
            $def->addArgument(new Reference($reference));
        }
        if (! $this->shared) {
            $def->setShared(false);
        }
    }

    public function withReferences(array $references) : Service
    {
        $this->references = $references;
        return $this;
    }
}