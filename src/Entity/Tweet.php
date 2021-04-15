<?php

namespace App\Entity;

use App\Repository\TweetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass=TweetRepository::class)
 */
class Tweet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=144)
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
<<<<<<< HEAD
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="tweets", cascade={"remove"})
     * @ORM\JoinColumn(name="users_id", onDelete="CASCADE")
=======
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="tweets")
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
>>>>>>> 84ac1062a1cd2df3559233c5be6e87b7b8b4fb60
     */
    private $users;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombre_like;

    /**
     * @ORM\OneToMany(targetEntity=Like::class, mappedBy="tweet")
     */
    private $likes;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getNombreLike(): ?int
    {
        return $this->nombre_like;
    }

    public function setNombreLike(int $nombre_like): self
    {
        $this->nombre_like = $nombre_like;

        return $this;
    }

    /**
     * @return Collection|Like[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setTweet($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getTweet() === $this) {
                $like->setTweet(null);
            }
        }

        return $this;
    }
}
