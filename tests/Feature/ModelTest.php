<?php

namespace Laramate\FlexProperties\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laramate\FlexProperties\Model;
use Laramate\FlexProperties\Tests\TestCase;
use Laramate\FlexProperties\Types2\Json;
use Laramate\FlexProperties\Types2\JsonFlexProperty;
use Laramate\FlexProperties\Types2\Longtext;
use Laramate\FlexProperties\Types2\StringFlexProperty;
use Laramate\FlexProperties\Types2\Text;

class ModelTest extends TestCase
{
    use DatabaseMigrations;

    protected function mockModel()
    {
        return new class() extends Model {

            protected $fillable = [
                'text_example',
                'longtext_example',
                'json_example',
            ];

            public $id = 1;

            public function flexProperties()
            {
                return [
                    new Text('text_example'),
                    new Longtext('longtext_example'),
                    new Json('json_example'),
                ];
            }

            public function save(array $options = [])
            {
                $this->fireModelEvent('saved');
            }
        };
    }

    public function testSetAndGetFlexProperties()
    {
        $model = $this->mockModel();

        $model->string = 'String example';
        $model->text = 'Text example';
        $model->json = ['json', 'example'];
        //dd($model);

        $this->assertInstanceOf(StringFlexProperty::class, $model->getFlexProperty('string'));
        $this->assertInstanceOf(Text::class, $model->getFlexProperty('text'));
        $this->assertInstanceOf(JsonFlexProperty::class, $model->getFlexProperty('json'));

        $this->assertEquals('String example', $model->string);
        $this->assertEquals('Text example', $model->text);
        $this->assertEquals(['json', 'example'], $model->json);
    }

    /**
     * @test
     * @group flexModel
     */
    public function testSaveAndGetFlexProperties()
    {
        $model = $this->mockModel();

        $model->string = 'String example';
        $model->text = 'Text example';
        $model->json = ['json', 'example'];

        $model->save();

        $this->assertEquals('String example', StringFlexProperty::first()->value);
        $this->assertEquals('Text example', Text::first()->value);
        $this->assertEquals(['json', 'example'], JsonFlexProperty::first()->value);
    }

    public function testSetAndGetFlexPropertiesWithLocale()
    {
        $model = $this->mockModel();

        $model->locale('en')->string = 'String example';
        $model->locale('de')->string = 'Zeichenkette';
        $model->locale('en')->text = 'Text example';
        $model->locale('de')->text = 'Textbeispiel';
        $model->locale('en')->json = ['json', 'example'];
        $model->locale('de')->json = ['json', 'beispiel'];

        $this->assertEquals('String example', $model->locale('en')->string);
        $this->assertEquals('Zeichenkette', $model->locale('de')->string);
        $this->assertEquals('Text example', $model->locale('en')->text);
        $this->assertEquals('Textbeispiel', $model->locale('de')->text);
        $this->assertEquals(['json', 'example'], $model->locale('en')->json);
        $this->assertEquals(['json', 'beispiel'], $model->locale('de')->json);
    }

    public function testCreateModel()
    {
        $model = $this->mockModel()->create([
            'string' => 'String example',
            'text'   => 'Text example',
            'json'   => ['json', 'example'],
        ]);

        $this->assertEquals('String example', StringFlexProperty::first()->value);
        $this->assertEquals('Text example', Text::first()->value);
        $this->assertEquals(['json', 'example'], JsonFlexProperty::first()->value);

        $this->assertEquals('String example', $model->string);
        $this->assertEquals('Text example', $model->text);
        $this->assertEquals(['json', 'example'], $model->json);
    }

    public function testUpdateModel()
    {
        $model = $this->mockModel()->create([
            'string' => 'String example',
            'text'   => 'Text example',
            'json'   => ['json', 'example'],
        ]);

        $model->fill([
            'string' => 'String updated',
            'text'   => 'Text updated',
            'json'   => ['json', 'updated'],
        ])->save();

        $this->assertEquals('String updated', $model->string);
        $this->assertEquals('Text updated', $model->text);
        $this->assertEquals(['json', 'updated'], $model->json);

        $this->assertEquals('String updated', StringFlexProperty::first()->value);
        $this->assertEquals('Text updated', Text::first()->value);
        $this->assertEquals(['json', 'updated'], JsonFlexProperty::first()->value);
    }

    public function testAccessFlexPropertyByReference()
    {
        $model = $this->mockModel()->create([
            'string' => 'String example',
        ]);

        $property = $model->flex('string');
        $property->value = 'Changed by reference';

        $model->save();

        $this->assertEquals('Changed by reference', $model->string);
        $this->assertEquals('Changed by reference', StringFlexProperty::first()->value);
    }

    public function testReloadFlexProperties()
    {
        $model = $this->mockModel()->create([
            'string' => 'String example',
        ]);

        $model->string = 'Reload';
        $model->reloadFlexProperties();

        $this->assertEquals('String example', $model->string);
    }
}
