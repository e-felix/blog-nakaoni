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
     *     targetEntity="App\Entity\Utilisateur",
     *     inversedBy="commentaires"
     * )
     */
    private $utilisateur;

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

    public function setTexte(string $texte)
    {
        $this->texte = $texte;
    }

    public function getStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut)
    {
        $this->statut = $statut;
    }

    public function getUtilisateurs(): ?Utilisateurs
    {
        return $this->utilisateurs;
    }

    public function setUtilisateurs(?Utilisateurs $utilisateurs)
    {
        $this->utilisateurs = $utilisateurs;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article)
    {
        $this->article = $article;
    }

    public function getReferent(): ?self
    {
        return $this->referent;
    }

    public function setReferent(?self $referent)
    {
        $this->referent = $referent;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getMesCommentaires(): Collection
    {
        return $this->mesCommentaires;
    }

    public function addMesCommentaire(Commentaire $mesCommentaire)
    {
        if (!$this->mesCommentaires->contains($mesCommentaire)) {
            $this->mesCommentaires[] = $mesCommentaire;
            $mesCommentaire->setReferent($this);
        }
    }

    public function removeMesCommentaire(Commentaire $mesCommentaire)
    {
        if ($this->mesCommentaires->contains($mesCommentaire)) {
            $this->mesCommentaires->removeElement($mesCommentaire);

            if ($mesCommentaire->getMesCommentaires() === $this) {
                $mesCommentaire->setReferent(null);
            }
        }
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt)
    {
        $this->createdAt = $createdAt;
    }
}
