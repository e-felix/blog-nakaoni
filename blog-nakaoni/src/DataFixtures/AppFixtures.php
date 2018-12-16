<?php
/**
 * Created by PhpStorm.
 * User: stagiaire
 * Date: 26/06/2018
 * Time: 15:00
 */

namespace App\DataFixtures;

use App\Entity\Articles;
use App\Entity\Utilisateurs;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;

const TEST_IMAGE_PATH = 'public/images/articles/monimage.jpg';

class AppFixtures extends Fixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $categorie = ['films', 'series', 'mangas', 'games'];

        $image = new File(TEST_IMAGE_PATH);

        $date = new \DateTime('2018-06-26');

        $user = new Utilisateurs();
        $user->setEmail('chotenro@gmail.com');
        $user->setPassword('');
        $user->setUsername('Nakaoni');
        $user->setEnabled(1);

        for ($i = 0; $i < 10; $i++ )
        {

            $article = new Articles();
            $article->setTitre('Titre '.$i);
            $article->setAuteur($user);
            $article->setAccroche('Look, just because I don\'t be givin\' no man a foot massage don\'t make it right for Marsellus to throw Antwone into a glass motherfuckin\' house, fuckin\' up the way the nigger talks');
            $article->setTexte('
                Do you see any Teletubbies in here? Do you see a slender plastic tag clipped to my shirt with my name printed on it? Do you see a little Asian child with a blank expression on his face sitting outside on a mechanical helicopter that shakes when you put quarters in it? No? Well, that\'s what you see at a toy store. And you must think you\'re in a toy store, because you\'re here shopping for an infant named Jeb.

                Now that there is the Tec-9, a crappy spray gun from South Miami. This gun is advertised as the most popular gun in American crime. Do you believe that shit? It actually says that in the little book that comes with it: the most popular gun in American crime. Like they\'re actually proud of that shit.

                Now that we know who you are, I know who I am. I\'m not a mistake! It all makes sense! In a comic, you know how you can tell who the arch-villain\'s going to be? He\'s the exact opposite of the hero. And most times they\'re friends, like you and me! I should\'ve known way back when... You know why, David? Because of the kids. They called me Mr Glass.

            ');
            $article->setCategorie($categorie[0]);
            $article->setPublic(1);
            $article->setNbViews(mt_rand(1, 10000));
            $article->setliked(mt_rand(1, 10000));
            $article->setdisliked(mt_rand(1, 10000));
            $article->setCommentEnabled(1);
            $article->setCreatedAt($date);
            $article->setYoutube('OdKCzhU7kQI');
            $article->setImageFile($image);
            $article->setImageName('monimage.jpg');
            $article->setImageSize(79991);
            $manager->persist($article);
        }

        for ($i = 10; $i < 20; $i++ )
        {

            $article = new Articles();
            $article->setTitre('Titre '.$i);
            $article->setAuteur($user);
            $article->setAccroche('Look, just because I don\'t be givin\' no man a foot massage don\'t make it right for Marsellus to throw Antwone into a glass motherfuckin\' house, fuckin\' up the way the nigger talks');
            $article->setTexte('
                Do you see any Teletubbies in here? Do you see a slender plastic tag clipped to my shirt with my name printed on it? Do you see a little Asian child with a blank expression on his face sitting outside on a mechanical helicopter that shakes when you put quarters in it? No? Well, that\'s what you see at a toy store. And you must think you\'re in a toy store, because you\'re here shopping for an infant named Jeb.

                Now that there is the Tec-9, a crappy spray gun from South Miami. This gun is advertised as the most popular gun in American crime. Do you believe that shit? It actually says that in the little book that comes with it: the most popular gun in American crime. Like they\'re actually proud of that shit.

                Now that we know who you are, I know who I am. I\'m not a mistake! It all makes sense! In a comic, you know how you can tell who the arch-villain\'s going to be? He\'s the exact opposite of the hero. And most times they\'re friends, like you and me! I should\'ve known way back when... You know why, David? Because of the kids. They called me Mr Glass.

            ');
            $article->setCategorie($categorie[1]);
            $article->setPublic(1);
            $article->setNbViews(mt_rand(1, 10000));
            $article->setliked(mt_rand(1, 10000));
            $article->setdisliked(mt_rand(1, 10000));
            $article->setCommentEnabled(1);
            $article->setCreatedAt($date);
            $article->setYoutube('OdKCzhU7kQI');
            $article->setImageFile($image);
            $article->setImageName('monimage.jpg');
            $article->setImageSize(79991);
            $manager->persist($article);
        }

        for ($i = 20; $i < 30; $i++ )
        {

            $article = new Articles();
            $article->setTitre('Titre '.$i);
            $article->setAuteur($user);
            $article->setAccroche('Look, just because I don\'t be givin\' no man a foot massage don\'t make it right for Marsellus to throw Antwone into a glass motherfuckin\' house, fuckin\' up the way the nigger talks');
            $article->setTexte('
                Do you see any Teletubbies in here? Do you see a slender plastic tag clipped to my shirt with my name printed on it? Do you see a little Asian child with a blank expression on his face sitting outside on a mechanical helicopter that shakes when you put quarters in it? No? Well, that\'s what you see at a toy store. And you must think you\'re in a toy store, because you\'re here shopping for an infant named Jeb.

                Now that there is the Tec-9, a crappy spray gun from South Miami. This gun is advertised as the most popular gun in American crime. Do you believe that shit? It actually says that in the little book that comes with it: the most popular gun in American crime. Like they\'re actually proud of that shit.

                Now that we know who you are, I know who I am. I\'m not a mistake! It all makes sense! In a comic, you know how you can tell who the arch-villain\'s going to be? He\'s the exact opposite of the hero. And most times they\'re friends, like you and me! I should\'ve known way back when... You know why, David? Because of the kids. They called me Mr Glass.

            ');
            $article->setCategorie($categorie[2]);
            $article->setPublic(1);
            $article->setNbViews(mt_rand(1, 10000));
            $article->setliked(mt_rand(1, 10000));
            $article->setdisliked(mt_rand(1, 10000));
            $article->setCommentEnabled(1);
            $article->setCreatedAt($date);
            $article->setYoutube('OdKCzhU7kQI');
            $article->setImageFile($image);
            $article->setImageName('monimage.jpg');
            $article->setImageSize(79991);
            $manager->persist($article);
        }


        for ($i = 30; $i < 40; $i++ )
        {

            $article = new Articles();
            $article->setTitre('Titre '.$i);
            $article->setAuteur($user);
            $article->setAccroche('Look, just because I don\'t be givin\' no man a foot massage don\'t make it right for Marsellus to throw Antwone into a glass motherfuckin\' house, fuckin\' up the way the nigger talks');
            $article->setTexte('
                Do you see any Teletubbies in here? Do you see a slender plastic tag clipped to my shirt with my name printed on it? Do you see a little Asian child with a blank expression on his face sitting outside on a mechanical helicopter that shakes when you put quarters in it? No? Well, that\'s what you see at a toy store. And you must think you\'re in a toy store, because you\'re here shopping for an infant named Jeb.

                Now that there is the Tec-9, a crappy spray gun from South Miami. This gun is advertised as the most popular gun in American crime. Do you believe that shit? It actually says that in the little book that comes with it: the most popular gun in American crime. Like they\'re actually proud of that shit.

                Now that we know who you are, I know who I am. I\'m not a mistake! It all makes sense! In a comic, you know how you can tell who the arch-villain\'s going to be? He\'s the exact opposite of the hero. And most times they\'re friends, like you and me! I should\'ve known way back when... You know why, David? Because of the kids. They called me Mr Glass.

            ');
            $article->setCategorie($categorie[3]);
            $article->setPublic(1);
            $article->setNbViews(mt_rand(1, 10000));
            $article->setliked(mt_rand(1, 10000));
            $article->setdisliked(mt_rand(1, 10000));
            $article->setCommentEnabled(1);
            $article->setCreatedAt($date);
            $article->setYoutube('OdKCzhU7kQI');
            $article->setImageFile($image);
            $article->setImageName('monimage.jpg');
            $article->setImageSize(79991);
            $manager->persist($article);
        }

        $manager->flush();
    }
}