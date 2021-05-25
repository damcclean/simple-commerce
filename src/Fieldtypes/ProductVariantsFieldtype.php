<?php

namespace DoubleThreeDigital\SimpleCommerce\Fieldtypes;

use Statamic\Fields\Field;
use Statamic\Fields\Fieldtype;
use Statamic\Fields\FieldtypeRepository;
use Statamic\Fieldtypes\Textarea;

class ProductVariantsFieldtype extends Fieldtype
{
    public function configFieldItems(): array
    {
        return [
            'option_fields' => [
                'display'      => __('simple-commerce::messages.fieldtypes.product_variants.config_fields.option_fields.display'),
                'type'         => 'fields',
                'instructions' => __('simple-commerce::messages.fieldtypes.product_variants.config_fields.option_fields.instructions'),
            ],
        ];
    }

    public function preload()
    {
        return [
            'variant_fields' => [
                (new Field('name', [
                    'type'       => 'text',
                    'listable'   => 'hidden',
                    'display'    => 'Name',
                    'width'      => 50,
                    'input_type' => 'text',
                    'validate'   => 'required',
                ]))->toBlueprintArray(),
                (new Field('values', [
                    'type'     => 'taggable',
                    'listable' => 'hidden',
                    'display'  => 'Values',
                    'width'    => 50,
                    'validate' => 'required',
                ]))->toPublishArray(),
            ],
            'option_fields' => array_merge(
                [
                    (new Field('key', [
                        'type'      => 'hidden',
                        'listable'  => 'hidden',
                        'display'   => 'Key',
                        'read_only' => true,
                        'validate'  => 'required',
                    ]))->toPublishArray(),
                    (new Field('variant', [
                        'type'      => 'textarea',
                        'listable'  => 'hidden',
                        'display'   => 'Variant',
                        'read_only' => true,
                        'validate'  => 'required',
                    ]))->toPublishArray(),
                    (new Field('price', [
                        'type'      => 'money',
                        'read_only' => false,
                        'listable'  => 'hidden',
                        'display'   => 'price',
                        'validate'  => 'required',
                    ]))->toPublishArray(),
                ],
                collect($this->config('option_fields'))
                    ->map(function ($field) {
                        return (
                            new Field($field['handle'], $field['field'])
                        )->toPublishArray();
                    })
                    ->toArray(),
            ),
            'variant' => resolve(Textarea::class)->preload(),
            'price'   => resolve(MoneyFieldtype::class)->preload(),
        ];
    }

    public function preProcess($data)
    {
        return [
            'variants' => $this->processInsideFields(
                isset($data['variants']) ? $data['variants'] : [],
                $this->preload()['variant_fields'],
                'preProcess'
            ),
            'options' => $this->processInsideFields(
                isset($data['options']) ? $data['options'] : [],
                $this->preload()['option_fields'],
                'preProcess'
            ),
        ];
    }

    public function process($data)
    {
        return [
            'variants' => $this->processInsideFields(
                $data['variants'],
                $this->preload()['variant_fields'],
                'process'
            ),
            'options' => $this->processInsideFields(
                $data['options'],
                $this->preload()['option_fields'],
                'process'
            ),
        ];
    }

    public static function title()
    {
        return __('simple-commerce::messages.fieldtypes.product_variants.title');
    }

    public function component(): string
    {
        return 'product-variants';
    }

    public function augment($value)
    {
        if (!$value) {
            return null;
        }

        return [
            'variants' => $this->processInsideFields(isset($value['variants']) ? $value['variants'] : [], $this->preload()['variant_fields'], 'augment'),
            'options'  => $this->processInsideFields(isset($value['options']) ? $value['options'] : [], $this->preload()['option_fields'], 'augment'),
        ];
    }

    protected function processInsideFields(array $fieldValues, array $fields, string $method)
    {
        return collect($fieldValues)
            ->map(function ($optionAttributes) use ($fields, $method) {
                return collect($optionAttributes)
                    ->map(function ($value, $key) use ($fields, $method) {
                        if ($key === 'key') {
                            return $value;
                        }

                        return collect($fields)
                            ->where('handle', $key)
                            ->map(function ($field) use ($value, $method) {
                                return (new FieldtypeRepository())
                                    ->find($field['type'])
                                    ->{$method}($value);
                            })
                            ->first();
                    })
                    ->toArray();
            })
            ->toArray();
    }

    public static function docsUrl()
    {
        return 'https://sc-docs.doublethree.digital/v2.3/product-variants';
    }

    public function preProcessIndex($value)
    {
        if (!$value) {
            return __('simple-commerce::messages.product_has_no_variants');
        }

        $optionsCount = collect($value['options'])->count();

        if ($optionsCount === 0) {
            return __('simple-commerce::messages.product_has_no_variants');
        } elseif ($optionsCount === 1) {
            return $optionsCount.' '.__('simple-commerce::messages.product_variants_singular');
        } else {
            return $optionsCount.' '.__('simple-commerce::messages.product_variants_plural');
        }
    }
}
