<?php

/*
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * Este archivo pertenece a la aplicaci�n de prueba Cupon.
 * El c�digo fuente de la aplicaci�n incluye un archivo llamado LICENSE
 * con toda la informaci�n sobre el copyright y la licencia.
 */

namespace Cupon\TiendaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Cupon\CiudadBundle\Entity\Ciudad;
use Cupon\TiendaBundle\Entity\Tienda;

/**
 * Fixtures de la entidad Tienda.
 * Crea para cada ciudad entre 2 y 5 tiendas con informaci�n muy realista.
 */
class Tiendas extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    public function getOrder()
    {
        return 20;
    }

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        // Obtener todas las ciudades de la base de datos
        $ciudades = $manager->getRepository('CiudadBundle:Ciudad')->findAll();

        $i = 1;
        foreach ($ciudades as $ciudad) {
            $numeroTiendas = rand(2, 5);
            for ($j=1; $j<=$numeroTiendas; $j++) {
                $tienda = new Tienda();

                $tienda->setNombre($this->getNombre());

                $tienda->setLogin('tienda'.$i);
                $tienda->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));

                $passwordEnClaro = 'tienda'.$i;
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($tienda);
                $passwordCodificado = $encoder->encodePassword($passwordEnClaro, $tienda->getSalt());
                $tienda->setPassword($passwordCodificado);

                $tienda->setDescripcion($this->getDescripcion());
                $tienda->setDireccion($this->getDireccion($ciudad));
                $tienda->setCiudad($ciudad);

                $manager->persist($tienda);

                $i++;
            }
        }

        $manager->flush();
    }

    /**
     * Generador aleatorio de nombres de tiendas
     *
     * @return string Nombre aleatorio generado para la tienda.
     */
    private function getNombre()
    {
        $prefijos = array('Restaurante', 'Cafeter�a', 'Bar', 'Pub', 'Pizza', 'Burger');
        $nombres = array(
            'Lorem ipsum', 'Sit amet', 'Consectetur', 'Adipiscing elit',
            'Nec sapien', 'Tincidunt', 'Facilisis', 'Nulla scelerisque',
            'Blandit ligula', 'Eget', 'Hendrerit', 'Malesuada', 'Enim sit'
        );

        return $prefijos[array_rand($prefijos)].' '.$nombres[array_rand($nombres)];
    }

    /**
     * Generador aleatorio de descripciones de tiendas
     *
     * @return  string Descripci�n aleatoria generada para la tienda.
     */
    private function getDescripcion()
    {
        $descripcion = '';

        $frases = array_flip(array(
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'Mauris ultricies nunc nec sapien tincidunt facilisis.',
            'Nulla scelerisque blandit ligula eget hendrerit.',
            'Sed malesuada, enim sit amet ultricies semper, elit leo lacinia massa, in tempus nisl ipsum quis libero.',
            'Aliquam molestie neque non augue molestie bibendum.',
            'Pellentesque ultricies erat ac lorem pharetra vulputate.',
            'Donec dapibus blandit odio, in auctor turpis commodo ut.',
            'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.',
            'Nam rhoncus lorem sed libero hendrerit accumsan.',
            'Maecenas non erat eu justo rutrum condimentum.',
            'Suspendisse leo tortor, tempus in lacinia sit amet, varius eu urna.',
            'Phasellus eu leo tellus, et accumsan libero.',
            'Pellentesque fringilla ipsum nec justo tempus elementum.',
            'Aliquam dapibus metus aliquam ante lacinia blandit.',
            'Donec ornare lacus vitae dolor imperdiet vitae ultricies nibh congue.',
        ));

        $numeroFrases = rand(3, 6);

        return implode(' ', array_rand($frases, $numeroFrases));
    }

    /**
     * Generador aleatorio de direcciones postales.
     *
     * @param  Ciudad $ciudad Objeto de la ciudad para la que se genera una direcci�n postal.
     * @return string         Direcci�n postal aleatoria generada para la tienda.
     */
    private function getDireccion(Ciudad $ciudad)
    {
        $prefijos = array('Calle', 'Avenida', 'Plaza');
        $nombres = array(
            'Lorem', 'Ipsum', 'Sitamet', 'Consectetur', 'Adipiscing',
            'Necsapien', 'Tincidunt', 'Facilisis', 'Nulla', 'Scelerisque',
            'Blandit', 'Ligula', 'Eget', 'Hendrerit', 'Malesuada', 'Enimsit'
        );

        return $prefijos[array_rand($prefijos)].' '.$nombres[array_rand($nombres)].', '.rand(1, 100)."\n"
        .$this->getCodigoPostal().' '.$ciudad->getNombre();
    }

    /**
     * Generador aleatorio de c�digos postales
     *
     * @return string C�digo postal aleatorio generado para la tienda.
     */
    private function getCodigoPostal()
    {
        return sprintf('%02s%03s', rand(1, 52), rand(0, 999));
    }
}

