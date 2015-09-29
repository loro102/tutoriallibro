<?php
// src/Cupon/TiendaBundle/Entity/Tienda.php
namespace Cupon\TiendaBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Tienda
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    /** @ORM\Column(type="string", length=100)
     * * @Assert\NotBlank*/
    protected $nombre;
    /** @ORM\Column(type="string", length=100)
     * * @Assert\NotBlank*/
    protected $slug;
    /** @ORM\Column(type="string", length=10)
     * * @Assert\NotBlank*/
    protected $login;
    /** @ORM\Column(type="string", length=255)
     * @Assert\NotBlank*/
    protected $password;
    /** @ORM\Column(type="string", length=255)
     * * @Assert\NotBlank*/
    protected $salt;
    /** @ORM\Column(type="text")
     * * @Assert\NotBlank*/
    protected $descripcion;
    /** @ORM\Column(type="text")
     * * @Assert\NotBlank*/
    protected $direccion;
    /**
     * @ORM\ManyToOne(targetEntity="Cupon\CiudadBundle\Entity\Ciudad")
     * * @Assert\NotBlank
     */
    protected $ciudad;
}
?>