<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdType extends AbstractType
{
    /**
     * Permet d'avoir la configuration de base d'un champ !
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */
    private function getConfiguration(string $label, string $placeholder, array $options = []) : array
    {
        return array_merge([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ], $options);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration('Titre', 'Titre de votre annonce'))
            ->add('slug', TextType::class, $this->getConfiguration('Slug', 'Slug de l\'annonce', ['required' => false]))
            ->add('coverImage', UrlType::class, $this->getConfiguration('URL de l\'image principale', 'Donnez l\'adresse d\'une image'))
            ->add('introduction', TextType::class, $this->getConfiguration('Introduction', 'Donnez une introduction global de l\'annonce'))
            ->add('description', TextType::class, $this->getConfiguration('Presentation', 'Tapez un presentation pour votre bien'))
            ->add('rooms', IntegerType::class, $this->getConfiguration('Nombre de chambre', 'Le nombre de chambre disponible'))
            ->add('price', MoneyType::class, $this->getConfiguration('Prix par nuit', 'Indiquez le prix pour une nuit'))
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
