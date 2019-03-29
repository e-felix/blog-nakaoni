<?php

namespace App\Form;

use App\Entity\Article;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

use Vich\UploaderBundle\Form\Type\VichImageType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("titre")
            ->add("accroche")
            ->add("texte")
            ->add(
                "categorie",
                ChoiceType::class,
                array(
                    "choices" => array(
                        "FILMS" => "films",
                        "SERIES" => "series",
                        "MANGAS" => "mangas",
                        "GAMES" => "games"
                    )
                )
            )
            ->add(
                "imageFile", VichImageType::class,
                array("required" => false)
            )
            ->add("youtube")
        ;
    }
}
