<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\SettingsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class SettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                    new Regex(
                        [
                            'pattern' => '/[a-z]+/',
                            'message' => 'Name can\'t have spaces',
                        ]
                    )
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('value', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('profile', ChoiceType::class, [
                'required' => false,
            ])
        ;
    }

    public function getName()
    {
        return 'setting';
    }
}