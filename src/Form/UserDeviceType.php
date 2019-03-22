<?php

namespace App\Form;

use App\Entity\UserDevice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserDeviceType
 *
 * @package      App\Form
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
class UserDeviceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('deviceId', TextType::class, ['required' => false])
            ->add('deviceToken', TextType::class, ['required' => false])
            ->add('capabilities', TextType::class, ['required' => false])
            ->add('extra');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => UserDevice::class,
                'csrf_protection' => false
            ]
        );
    }

    /**
     * @return null|string
     */
    public function getBlockPrefix()
    {
        return "device";
    }
}
