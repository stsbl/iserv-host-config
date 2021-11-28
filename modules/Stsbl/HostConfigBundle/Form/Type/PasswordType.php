<?php

declare(strict_types=1);

namespace Stsbl\HostConfigBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\PasswordType as SymfonyPasswordType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class PasswordType extends SymfonyPasswordType
{
    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        // removes default handling of value displaying
        // (https://symfony.com/doc/current/reference/forms/types/password.html#always-empty)
        // Native always_empty only works if the form is submitted and intercepted by validation errors.
        if ($options['always_empty']) {
            $view->vars['value'] = '';
        }
    }
}
