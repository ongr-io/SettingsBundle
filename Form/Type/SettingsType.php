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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Settings type for changing all configured settings.
 *
 * Settings fields are being generated from $settingsMap passed to the constructor.
 */
class SettingsType extends AbstractType
{
    /**
     * @var array
     */
    private $settingsStructure;

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
     * @param array $settingsStructure
     */
    public function __construct(array $settingsStructure)
    {
        $this->settingsStructure = $settingsStructure;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->settingsStructure as $settingName => $setting) {
            $typeConfiguration = isset($setting['type']) ? $setting['type'] : null;
            $type = $typeConfiguration ? $typeConfiguration[0] : 'checkbox';
            $typeOptions = $typeConfiguration ? $typeConfiguration[1] : ['required' => false];
            $builder->add($settingName, $type, $typeOptions);
        }

        $builder->add('submit', 'submit');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'settings';
    }
}
