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
     * @Groups({"list", "settings_account", "settings", "user_search"})
     */
    protected $email;

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
     * @var string
     * @Assert\Type("string")
     * @Assert\Url(
     *    checkDNS = "ANY"
     * )
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"user_profile_public"})
     */
    protected $avatarUrl;

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
     * @param  string $mLinkValidUntil
     *
     * @return User
     */
    public function setMLinkValidUntil(?\DateTime $mLinkValidUntil = null): self
    {
        $this->mLinkValidUntil = $mLinkValidUntil;

        return $this;
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
        return $this->avatarUrl;
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
}
