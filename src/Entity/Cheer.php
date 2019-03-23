<?php

namespace App\Entity;

use App\Entity\Traits\EntityTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"public", "requests"})
     */
    private $text;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     * @Groups({"public", "requests"})
     */
    private $byUser;

    /**
     * @ORM\OneToMany(targetEntity="Cheer", mappedBy="parent")
     */
    private $upVotes;

    /**
     * @ORM\ManyToOne(targetEntity="Cheer", inversedBy="upVotes")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * Cheer constructor.
     *
     * @param User $forUser
     * @param User $byUser
     */
    public function __construct(?User $forUser = null, ?User $byUser = null)
    {
        $this->upVotes = new ArrayCollection();
        $this->forUser = $forUser;
        $this->byUser = [
            "id" => $byUser->getId(),
            "email" => $byUser->getEmail(),
            "username" => $byUser->getUsername(),
            "avatar" => $byUser->getAvatarUrl(),
        ];
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
     * @Groups({"public", "requests"})
     */
    public function getUpVotes() {
        $x = [];

        /** @var Cheer $upVote */
        foreach ($this->upVotes as $upVote) {
            $x[] = $upVote->getByUser();
        }

        return $x;
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

    /**
     * @return array
     *
     */
    public function getByUser()
    {
        return $this->byUser;
    }

    /**
     * @param User $user
     *
     * @return User
     */
    public function setByUser(User $user)
    {
        $this->byUser = $user;

        return $user;
    }

//    /**
//     * @return ArrayCollection
//     */
//    public function getUpVotes() {
//        return $this->upVotes;
//    }

    /**
     * @param Cheer $cheer
     */
    public function addUpVote(Cheer $cheer) {
        $this->upVotes->add($cheer);
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent(Cheer $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text): void
    {
        $this->text = $text;
    }


}
