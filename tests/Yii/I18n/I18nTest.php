<?php

namespace Yii2tech\Illuminate\Test\Yii\I18n;

use yii\base\InvalidConfigException;
use Yii2tech\Illuminate\Test\TestCase;
use Illuminate\Translation\Translator;
use Yii2tech\Illuminate\Yii\I18n\I18n;
use Illuminate\Translation\ArrayLoader;

class I18nTest extends TestCase
{
    public function testTranslate()
    {
        $translationLoader = new ArrayLoader();
        $translationLoader->addMessages('en', 'illuminated', [
            'message1' => 'translate1',
            'message2' => 'translate2',
        ]);

        $i18n = (new I18n())->setIlluminateTranslator(new Translator($translationLoader, 'en'));
        $i18n->illuminateCategories = [
            'illuminated',
        ];

        $this->assertSame('translate1', $i18n->translate('illuminated', 'message1', [], 'en'));

        $this->expectException(InvalidConfigException::class);
        $i18n->translate('regular', 'yii', [], 'en');
    }

    public function testFormat()
    {
        $i18n = new I18n();

        $this->assertSame('Yii style value', $i18n->format('Yii style {name}', ['name' => 'value'], 'en'));

        $this->assertSame('Laravel style value', $i18n->format('Laravel style :name', ['name' => 'value'], 'en'));
    }
}
