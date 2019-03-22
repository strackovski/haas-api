<?php

namespace App\Form;

use App\Entity\User;
use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $builder
//            ->add('account', AccountSettingsType::class, ['required' => false])
//            ->add('privacy', PrivacySettingsType::class, ['required' => false])
//            ->add('wallet', WalletType::class, ['required' => false])
//        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'csrf_protection' => false
            ]
        );
    }

    /**
     * @return null|string
     */
    public function getParent()
    {
        return RegistrationFormType::class;
    }
}
