<?php

namespace App\Entity;

use App\Entity\Traits\EntityTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Cheer
 *
 * @ORM\Entity
 * @ORM\Table(name="cheers")
 */
class Cheer implements EntityInterface
{
    use EntityTrait, TimestampableTrait;

    /**
     * @var \Ramsey\Uuid\Uuid
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     * @Groups({"public", "requests"})
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="cheers")
     * @ORM\JoinColumn(name="for_user_id", referencedColumnName="id")
     */
    private $forUser;

    /**
     * Cheer constructor.
     *
     * @param $forUser
     */
    public function __construct(User $forUser)
    {
        $this->forUser = $forUser;
    }

    /**
     * @Groups({"public", "requests"})
     */
    public function getCreatedForUser()
    {
        return [
            "id" => $this->forUser->getId(),
            "email" => $this->forUser->getEmail(),
            "username" => $this->forUser->getUsername(),
            "avatar" => $this->forUser->getAvatarUrl(),
        ];
    }

    /**
     * @return User
     */
    public function getForUser()
    {
        return $this->forUser;
    }

    /**
     * @param User $user
     *
     * @return User
     */
    public function setForUser(User $user)
    {
        $this->forUser = $user;

        return $user;
    }
}
