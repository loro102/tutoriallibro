<?php

/*
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * Este archivo pertenece a la aplicaci�n de prueba Cupon.
 * El c�digo fuente de la aplicaci�n incluye un archivo llamado LICENSE
 * con toda la informaci�n sobre el copyright y la licencia.
 */

namespace Cupon\UsuarioBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Cupon\CiudadBundle\Entity\Ciudad;
use Cupon\UsuarioBundle\Entity\Usuario;

/**
 * Fixtures de la entidad Usuario.
 * Crea 200 usuarios de prueba con informaci�n muy realista.
 */
class Usuarios extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    public function getOrder()
    {
        return 40;
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

        for ($i=1; $i<=200; $i++) {
            $usuario = new Usuario();

            $usuario->setNombre($this->getNombre());
            $usuario->setApellidos($this->getApellidos());
            $usuario->setEmail('usuario'.$i.'@localhost');

            $usuario->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));

            $passwordEnClaro = 'usuario'.$i;
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($usuario);
            $passwordCodificado = $encoder->encodePassword($passwordEnClaro, $usuario->getSalt());
            $usuario->setPassword($passwordCodificado);

            $ciudad = $ciudades[array_rand($ciudades)];
            $usuario->setDireccion($this->getDireccion($ciudad));
            $usuario->setCiudad($ciudad);

            // El 60% de los usuarios permite email
            $usuario->setPermiteEmail((rand(1, 1000) % 10) < 6);

            $usuario->setFechaAlta(new \DateTime('now - '.rand(1, 150).' days'));
            $usuario->setFechaNacimiento(new \DateTime('now - '.rand(7000, 20000).' days'));

            $dni = substr(rand(), 0, 8);
            $usuario->setDni($dni.substr("TRWAGMYFPDXBNJZSQVHLCKE", strtr($dni, "XYZ", "012")%23, 1));

            $usuario->setNumeroTarjeta('1234567890123456');

            $manager->persist($usuario);
        }

        $manager->flush();
    }

    /**
     * Generador aleatorio de nombres de personas.
     * Aproximadamente genera un 50% de hombres y un 50% de mujeres.
     *
     * @return string Nombre aleatorio generado para el usuario.
     */
    private function getNombre()
    {
        // Los nombres m�s populares en Espa�a seg�n el INE
        // Fuente: http://www.ine.es/daco/daco42/nombyapel/nombyapel.htm

        $hombres = array(
            'Antonio', 'Jos�', 'Manuel', 'Francisco', 'Juan', 'David',
            'Jos� Antonio', 'Jos� Luis', 'Jes�s', 'Javier', 'Francisco Javier',
            'Carlos', 'Daniel', 'Miguel', 'Rafael', 'Pedro', 'Jos� Manuel',
            '�ngel', 'Alejandro', 'Miguel �ngel', 'Jos� Mar�a', 'Fernando',
            'Luis', 'Sergio', 'Pablo', 'Jorge', 'Alberto'
        );
        $mujeres = array(
            'Mar�a Carmen', 'Mar�a', 'Carmen', 'Josefa', 'Isabel', 'Ana Mar�a',
            'Mar�a Dolores', 'Mar�a Pilar', 'Mar�a Teresa', 'Ana', 'Francisca',
            'Laura', 'Antonia', 'Dolores', 'Mar�a Angeles', 'Cristina', 'Marta',
            'Mar�a Jos�', 'Mar�a Isabel', 'Pilar', 'Mar�a Luisa', 'Concepci�n',
            'Luc�a', 'Mercedes', 'Manuela', 'Elena', 'Rosa Mar�a'
        );

        if (rand() % 2) {
            return $hombres[array_rand($hombres)];
        } else {
            return $mujeres[array_rand($mujeres)];
        }
    }

    /**
     * Generador aleatorio de apellidos de personas.
     *
     * @return string Apellido aleatorio generado para el usuario.
     */
    private function getApellidos()
    {
        // Los apellidos m�s populares en Espa�a seg�n el INE
        // Fuente: http://www.ine.es/daco/daco42/nombyapel/nombyapel.htm

        $apellidos = array(
            'Garc�a', 'Gonz�lez', 'Rodr�guez', 'Fern�ndez', 'L�pez', 'Mart�nez',
            'S�nchez', 'P�rez', 'G�mez', 'Mart�n', 'Jim�nez', 'Ruiz',
            'Hern�ndez', 'D�az', 'Moreno', '�lvarez', 'Mu�oz', 'Romero',
            'Alonso', 'Guti�rrez', 'Navarro', 'Torres', 'Dom�nguez', 'V�zquez',
            'Ramos', 'Gil', 'Ram�rez', 'Serrano', 'Blanco', 'Su�rez', 'Molina',
            'Morales', 'Ortega', 'Delgado', 'Castro', 'Ort�z', 'Rubio', 'Mar�n',
            'Sanz', 'Iglesias', 'Nu�ez', 'Medina', 'Garrido'
        );

        return $apellidos[array_rand($apellidos)].' '.$apellidos[array_rand($apellidos)];
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

