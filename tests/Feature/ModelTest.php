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

        $model->text_example = 'Text example';
        $model->longtext_example = 'Longtext example';
        $model->json_example = ['json', 'example'];
        dd($model);

        $this->assertEquals('String example', $model->string);
        $this->assertEquals('Text example', $model->text);
        $this->assertEquals(['json', 'example'], $model->json);
    }
}
