<?php

namespace App\Form;

use App\Entity\Articles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('accroche')
            ->add('texte')
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    'FILMS' => 'films',
                    'SERIES' => 'series',
                    'MANGAS' => 'mangas',
                    'GAMES' => 'games'
                ]
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false
            ])
            ->add('youtube')
            //->add('public')
            //->add('commentEnabled')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
