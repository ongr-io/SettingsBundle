<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Settings type for changing all configured settings.
 *
 * Settings fields are being generated from $settingsMap passed to the constructor.
 */
class SettingsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'csrf_protection' => false,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'settingsStructure' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $settingsStructure = $options['settingsStructure'];
        foreach ($settingsStructure as $settingName => $setting) {
            $typeConfiguration = isset($setting['type']) ? $setting['type'] : null;
            $type = $typeConfiguration ? $typeConfiguration[0] : CheckboxType::class;
            $typeOptions = $typeConfiguration ? $typeConfiguration[1] : ['required' => false];
            $builder->add($settingName, $type, $typeOptions);
        }

        $builder->add('submit', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'settings';
    }
}
