<?php

namespace App\Entity;

use App\Repository\TweetRepository;
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
}
