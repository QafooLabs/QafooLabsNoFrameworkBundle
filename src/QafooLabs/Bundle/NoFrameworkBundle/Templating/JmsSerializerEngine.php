<?php
/**
 * QafooLabs NoFramework Bundle
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so I can send you a copy immediately.
 */

namespace QafooLabs\Bundle\NoFrameworkBundle\Templating;

use Symfony\Component\Templating\EngineInterface;
use JMS\Serializer\SerializerInterface;

class JmsSerializerEngine implements EngineInterface
{
    /**
     * @var JMS\Serializer\Serializer
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer = null)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritDoc}
     */
    public function render($name, array $parameters = array())
    {
        if (!isset($parameters['view'])) {
            throw new \RuntimeException('JmsSerializerEngine expects a template parameter "view" for serialization.');
        }

        return $this->serializer->serialize($parameters['view'], $this->extractFormat($name));
    }

    /**
     * @return string
     */
    private function extractFormat($name)
    {
        $parts = explode('.', $name);
        array_pop($parts);
        return array_pop($parts);
    }

    /**
     * {@inheritDoc}
     */
    public function exists($name)
    {
        return ($this->serializer instanceof SerializerInterface);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($name)
    {
        return ($this->serializer instanceof SerializerInterface) && (strpos($name, '.xml.') || strpos($name, '.json.'));
    }
}
