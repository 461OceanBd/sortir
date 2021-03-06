<?php

namespace App\Form;

use App\Entity\SchoolSite;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Modification du profil utilisateur
 * @TODO: autre formulaire pour le mot de passe
 *
 * Class ProfileType
 * @package App\Form
 */
class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('firstname', null, ['label' => 'Prénom'])
            ->add('lastname', null, ['label' => 'Nom'])
            ->add('phone', null, ['label' => 'Téléphone'])
            ->add('school', EntityType::class, [
                'label' => 'Votre école de rattachement',
                'class' => SchoolSite::class,
                'choice_label' => 'name'
            ])
            ->add('submit', SubmitType::class, ['label' => 'OK'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
