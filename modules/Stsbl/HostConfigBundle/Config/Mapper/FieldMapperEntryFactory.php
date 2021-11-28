<?php

declare(strict_types=1);

namespace Stsbl\HostConfigBundle\Config\Mapper;

use IServ\CoreBundle\Logger\ModuleLogger;
use Psr\Log\LoggerInterface;
use Stsbl\HostConfigBundle\Config\HostConfigExtension;
use Stsbl\HostConfigBundle\FieldDefinition\FieldDefinition;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/*
 * The MIT License
 *
 * Copyright 2021 Felix Jacobi.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @author Felix Jacobi <felix.jacobi@stsbl.de>
 * @license MIT license <https://opensource.org/licenses/MIT>
 */
final class FieldMapperEntryFactory
{
    private const FALLBACK_FORM_TYPE = TextType::class;
    private const FALLBACK_SHOW_TYPE = null;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = new ModuleLogger('Host Config', $logger);
    }

    public function createFormMapperEntry(FieldDefinition $fieldDefinition): MapperEntry
    {
        $name = $fieldDefinition->getName();
        $options = [
            'label' => _p('host-config-description', $fieldDefinition->getDescription()),
            'property_path' => self::buildExtensionPropertyPath($name),
            'required' => false,
        ];

        switch ($fieldType = $fieldDefinition->getType()) {
            case FieldDefinition::TYPE_TEXT:
                $type = TextType::class;
                break;
            case FieldDefinition::TYPE_PASSWORD:
                $type = PasswordType::class;
                break;
            default:
                $this->logger->warning('Unknown field type "{type}". Using Symfony form type "{fallback_type}".', [
                    'type' => $fieldType,
                    'fallback_type' => self::FALLBACK_FORM_TYPE,
                ]);
                $type = self::FALLBACK_FORM_TYPE;
        }

        if (null !== $helpText = $fieldDefinition->getHelpText()) {
            $options['help'] = _p('host-config-help-text', $helpText);
        }

        return new MapperEntry(
            \sprintf('%s_%s', HostConfigExtension::NAME, $name),
            $type,
            $options
        );
    }

    public function createShowMapperEntry(FieldDefinition $fieldDefinition): MapperEntry
    {
        $group = $fieldDefinition->getGroup();
        $options = [
            'label' => _p('host-config-description', $fieldDefinition->getDescription()),
            'fieldset' => null === $group ? _('Host configuration') : _p('host-config-group', $group),
        ];

        switch ($fieldType = $fieldDefinition->getType()) {
            case FieldDefinition::TYPE_TEXT:
                $type = null;
                break;
            case FieldDefinition::TYPE_PASSWORD:
                $type = PasswordType::class;
                $options['template'] = '@StsblHostConfig/crud/show/field_password.html.twig';
                break;
            default:
                $this->logger->warning('Unknown field type "{type}". Using "{fallback_type}".', [
                    'type' => $fieldType,
                    'fallback_type' => self::FALLBACK_SHOW_TYPE,
                ]);
                $type = self::FALLBACK_SHOW_TYPE;
        }

        return new MapperEntry(
            self::buildExtensionPropertyPath($fieldDefinition->getName()),
            $type,
            $options
        );
    }

    private static function buildExtensionPropertyPath(string $name): string
    {
        return sprintf('extensions[%s_%s]', HostConfigExtension::NAME, $name);
    }
}
