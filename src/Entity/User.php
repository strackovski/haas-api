<?php

namespace App\Entity;

use App\Entity\Traits\EntityTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser implements EntityInterface
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
     * @Groups({"list", "public"})
     */
    protected $email;

    /**
     * @var string
     * @Assert\Type("string")
     * @Assert\Url(
     *    checkDNS = "ANY"
     * )
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"list", "public"})
     */
    protected $avatarUrl;

    /**
     *
     * @var string
     * @Assert\Type("string")
     * @ORM\Column(type="string", nullable=true)
     */
    private $mLinkHash;

    /**
     * @var string
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $mLinkValidUntil;

    /**
     * @var string
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $mLinkLastSent;

    /**
     * @ORM\OneToMany(targetEntity="Cheer", mappedBy="forUser")
     * @Groups({"list", "public"})
     */
    private $cheers;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->cheers = new ArrayCollection();
    }

    /**
     * @Groups({"list", "public"})
     */
    public function getCheerCount() {
        return $this->cheers->count();
    }

    /**
     * @Groups({"list", "public"})
     */
    public function getUpVoteCount() {
        $x = 0;

        /** @var Cheer $cheer */
        foreach ($this->cheers as $cheer) {
            if ($cheer->getParent() instanceof Cheer) {
                $x++;
            }
        }

        return $x;
    }

    /**
     * @return string
     */
    public function getMLinkHash(): ?string
    {
        return $this->mLinkHash;
    }

    /**
     * @param  string $mLinkHash
     *
     * @return User
     */
    public function setMLinkHash(?string $mLinkHash = null): self
    {
        $this->mLinkHash = $mLinkHash;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMLinkValidUntil()
    {
        return $this->mLinkValidUntil;
    }

    /**
     * @param \DateTime $mLinkValidUntil
     *
     * @return User
     */
    public function setMLinkValidUntil(?\DateTime $mLinkValidUntil = null): self
    {
        $this->mLinkValidUntil = $mLinkValidUntil;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMLinkValid(): bool
    {
        try {
            return $this->mLinkValidUntil < new \DateTime();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getMLinkLastSent(): ?string
    {
        return $this->mLinkLastSent;
    }

    /**
     * @param  string $mLinkLastSent
     *
     * @return User
     */
    public function setMLinkLastSent(?string $mLinkLastSent = null): self
    {
        $this->mLinkLastSent = $mLinkLastSent;

        return $this;
    }

    /**
     * @Groups({"list", "requests", "user_profile_public"})
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getAvatarUrl()
    {
//        return $this->avatarUrl;

        return "https://laurauinteriordesign.com/wp-content/uploads/2018/03/avatar-placeholder.png";
    }

    /**
     * @param  string $avatarUrl
     *
     * @return $this
     */
    public function setAvatarUrl(string $avatarUrl): self
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCheers(): Collection
    {
        return $this->cheers;
    }

    /**
     * @param Cheer $cheer
     *
     * @return mixed
     */
    public function addCheer(Cheer $cheer)
    {
        $this->cheers->add($cheer);

        return $this;
    }
}
