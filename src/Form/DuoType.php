<?php

namespace App\Form;

use App\Entity\Duo;
use App\Entity\Player;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class DuoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('player1', EntityType::class, [
                'class' => Player::class,
                'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('p')
                    ->where('p.available = true');
                }])
            ->add('player2', EntityType::class, [
                'class' => Player::class,
                'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('p')
                    ->where('p.available = true');
                }])            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Duo::class,
        ]);
    }
}
