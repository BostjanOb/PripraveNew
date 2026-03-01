<?php

use App\Support\SchoolTypeUiConfig;

it('returns expected school type slugs', function () {
    $config = SchoolTypeUiConfig::all();

    expect(array_keys($config))->toBe(['pv', 'os', 'ss']);
});

it('returns required config keys for each school type', function () {
    $requiredTopLevelKeys = [
        'label',
        'shortLabel',
        'icon',
        'badge',
        'filterActive',
        'latestFilterActive',
        'dot',
        'dotActive',
        'create',
        'card',
        'level',
    ];

    $requiredCreateKeys = [
        'bg',
        'border',
        'text',
        'active',
        'checkBg',
        'checkBorder',
        'iconSvg',
    ];

    $requiredCardKeys = [
        'title',
        'description',
        'gradient',
        'bgLight',
        'borderColor',
        'textColor',
        'iconBg',
        'iconColor',
        'badgeBg',
        'hoverBorder',
        'shadowColor',
    ];

    $requiredLevelKeys = [
        'bg',
        'text',
        'border',
        'dot',
        'icon',
    ];

    foreach (SchoolTypeUiConfig::all() as $typeConfig) {
        foreach ($requiredTopLevelKeys as $key) {
            expect(array_key_exists($key, $typeConfig))->toBeTrue();
        }

        foreach ($requiredCreateKeys as $key) {
            expect(array_key_exists($key, $typeConfig['create']))->toBeTrue();
        }

        foreach ($requiredCardKeys as $key) {
            expect(array_key_exists($key, $typeConfig['card']))->toBeTrue();
        }

        foreach ($requiredLevelKeys as $key) {
            expect(array_key_exists($key, $typeConfig['level']))->toBeTrue();
        }
    }
});

it('falls back to os config for unknown or null slug', function () {
    $defaultConfig = SchoolTypeUiConfig::forSlug('os');

    expect(SchoolTypeUiConfig::forSlug('unknown-slug'))->toBe($defaultConfig)
        ->and(SchoolTypeUiConfig::forSlug(null))->toBe($defaultConfig);
});

it('provides non empty style values used by filters create and cards', function () {
    foreach (SchoolTypeUiConfig::all() as $typeConfig) {
        $keysToValidate = [
            $typeConfig['label'],
            $typeConfig['shortLabel'],
            $typeConfig['icon'],
            $typeConfig['badge'],
            $typeConfig['filterActive'],
            $typeConfig['latestFilterActive'],
            $typeConfig['dot'],
            $typeConfig['dotActive'],
            $typeConfig['create']['bg'],
            $typeConfig['create']['border'],
            $typeConfig['create']['text'],
            $typeConfig['create']['active'],
            $typeConfig['create']['checkBg'],
            $typeConfig['create']['checkBorder'],
            $typeConfig['create']['iconSvg'],
            $typeConfig['card']['title'],
            $typeConfig['card']['description'],
            $typeConfig['card']['gradient'],
            $typeConfig['card']['bgLight'],
            $typeConfig['card']['borderColor'],
            $typeConfig['card']['textColor'],
            $typeConfig['card']['iconBg'],
            $typeConfig['card']['iconColor'],
            $typeConfig['card']['badgeBg'],
            $typeConfig['card']['hoverBorder'],
            $typeConfig['card']['shadowColor'],
        ];

        foreach ($keysToValidate as $value) {
            expect(is_string($value))->toBeTrue()
                ->and(trim($value))->not->toBe('');
        }
    }
});
