<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentaireRepository")
 */
class Commentaire
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $texte;

    /**
     * @ORM\Column(type="boolean")
     */
    private $statut;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Utilisateurs",
     *     inversedBy="commentaires"
     * )
     */
    private $utilisateurs;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Article",
     *     inversedBy="commentaires"
     * )
     */
    private $article;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Commentaire",
     *     inversedBy="mesCommentaires"
     * )
     */
    private $referent;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Commentaire",
     *     mappedBy="referent"
     * )
     */
    private $mesCommentaires;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->mesCommentaires = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function getStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getUtilisateurs(): ?Utilisateurs
    {
        return $this->utilisateurs;
    }

    public function setUtilisateurs(?Utilisateurs $utilisateurs): self
    {
        $this->utilisateurs = $utilisateurs;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getReferent(): ?self
    {
        return $this->referent;
    }

    public function setReferent(?self $referent): self
    {
        $this->referent = $referent;

        return $this;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getMesCommentaires(): Collection
    {
        return $this->mesCommentaires;
    }

    public function addMesCommentaire(Commentaire $mesCommentaire): self
    {
        if (!$this->mesCommentaires->contains($mesCommentaire)) {
            $this->mesCommentaires[] = $mesCommentaire;
            $mesCommentaire->setReferent($this);
        }

        return $this;
    }

    public function removeMesCommentaire(Commentaire $mesCommentaire): self
    {
        if ($this->mesCommentaires->contains($mesCommentaire)) {
            $this->mesCommentaires->removeElement($mesCommentaire);

            if ($mesCommentaire->getMesCommentaires() === $this) {
                $mesCommentaire->setReferent(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
