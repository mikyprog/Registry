<?php

/*
 * This file is part of the Miky package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Component\Registry;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ServiceRegistry implements ServiceRegistryInterface
{
    /**
     * @var array
     */
    protected $services = [];

    /**
     * @var array
     */
    protected $identifiers = [];

    /**
     * Interface which is required by all services.
     *
     * @var string
     */
    protected $interface;

    /**
     * Human readable context for these services, e.g. "grid field"
     *
     * @var string
     */
    protected $context;

    /**
     * @param string $interface
     * @param string $context
     */
    public function __construct($interface, $context = 'service')
    {
        $this->interface = $interface;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->services;
    }

    /**
     * {@inheritdoc}
     */
    public function register($identifier, $service)
    {
        if ($this->has($identifier)) {
            throw new ExistingServiceException($this->context, $identifier);
        }

        if (!is_object($service)) {
            throw new \InvalidArgumentException(sprintf('%s needs to be an object, %s given.', ucfirst($this->context), gettype($service)));
        }

        if (!in_array($this->interface, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', ucfirst($this->context), $this->interface, get_class($service))
            );
        }

        $this->services[$identifier] = $service;
        $this->identifiers[$identifier] = $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function unregister($identifier)
    {
        if (!$this->has($identifier)) {
            throw new NonExistingServiceException($this->context, $identifier, array_keys($this->services));
        }

        unset($this->services[$identifier]);
        unset($this->identifiers[$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function has($identifier)
    {
        return isset($this->services[$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($identifier)
    {
        if (!$this->has($identifier)) {
            throw new NonExistingServiceException($this->context, $identifier, array_keys($this->services));
        }

        return $this->services[$identifier];
    }

    public function getIdentifiers()
    {
        return $this->identifiers;
    }

}
