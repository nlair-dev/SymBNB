<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminBookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire',
            ])
            ->add('booker', EntityType::class, [
                'class' => User::class,
                'label' => 'Client',
                'choice_label' => function($user) {
                    return $user->getFirstName() . ' ' . strtoupper($user->getLastName());
                }
            ])
            ->add('ad', EntityType::class, [
                'class' => Ad::class,
                'label' => 'Annonce',
                'choice_label' => function($ad) {
                    return $ad->getId() . ' - ' . $ad->getTitle();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
