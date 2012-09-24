<?php

namespace Phpforce\SoapClient\Soap\TypeConverter;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * A collection of type converters
 */
class TypeConverterCollection
{
    protected $converters = array();

    /**
     * Construct type converter collection
     *
     * @param array $converters (optional) Array of type converters
     */
    public function __construct(array $converters = array())
    {
        foreach ($converters as $converter) {
            $this->add($converter);
        }
    }

    /**
     * Add a type converter to the collection
     *
     * @param TypeConverterInterface $converter Type converter
     *
     * @return TypeConverterCollection
     * @throws \InvalidArgumentException
     */
    public function add(TypeConverterInterface $converter)
    {
        if ($this->has($converter->getTypeNamespace(), $converter->getTypeName())) {
            throw new \InvalidArgumentException(
                'Converter for this type already exists'
            );
        }

        return $this->set($converter);
    }

    /**
     * Set (overwrite) a type converter in the collection 
     *
     * @param TypeConverterInterface $converter Type converter
     *
     * @return TypeConverterCollection
     */
    public function set(TypeConverterInterface $converter)
    {
        $this->converters[$converter->getTypeNamespace() . ':'
            . $converter->getTypeName()] = $converter;

        return $this;
    }

    /**
     * Returns true if the collection contains a type converter for a certain
     * namespace and name
     * 
     * @param string $namespace Converter namespace
     * @param string $name      Converter name
     *
     * @return boolean
     */
    public function has($namespace, $name)
    {
        if (isset($this->converters[$namespace . ':' . $name])) {
            return true;
        }

        return false;
    }

    /**
     * Get this collection as a typemap that can be used in PHP's \SoapClient
     * 
     * @return array
     */
    public function getTypemap()
    {
        $typemap = array();

        foreach ($this->converters as $converter) {
            $typemap[] = array(
                'type_name' => $converter->getTypeName(),
                'type_ns'   => $converter->getTypeNamespace(),
                'from_xml'  => function($input) use ($converter) {
                    return $converter->convertXmlToPhp($input);
                },
                'to_xml'    => function($input) use ($converter) {
                    return $converter->convertPhpToXml($input);
                },
            );
        }

        return $typemap;
    }
}