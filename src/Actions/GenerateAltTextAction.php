<?php

namespace TFD\AIDA\Actions;

use Illuminate\Support\Collection;
use Statamic\Actions\Action;
use Statamic\Contracts\Assets\Asset;
use TFD\AIDA\GenerateAltText;
use TFD\AIDA\Generator\Generator;

class GenerateAltTextAction extends Action
{
    /**
     * @var Generator
     */
    protected $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * The text of the confirmation button.
     *
     * @return string
     */
    public function buttonText()
    {
        return __('Generate Alt Text|Generate Alt Texts');
    }

    /**
     * The confirmation text that is shown in the action's prompt.
     *
     * @return string
     */
    public function confirmationText()
    {
        return __('Are you sure you want to generate alt texts for this image?|Are you sure you want to generate alt texts for :count images?');
    }

    /**
     * Show a warning text if there is no queue defined and more than 1 items are selected.
     *
     * @return string|null
     */
    public function warningText()
    {
        if ($this->items->count() > 1 && config('queue.default') === 'sync') {
            return __('Generating alt texts for more than 1 image can take a long time. Are you sure to run this action?');
        }

        return null;
    }

    /**
     * Add a 'overwrite' field to the action prompt to allow users to overwrite existing alt texts.
     *
     * @return array<string, mixed>
     */
    protected function fieldItems()
    {
        return [
            'overwrite' => [
                'display' => __('Overwrite existing alt texts'),
                'type' => 'toggle',
                'inline_label' => __('no'),
                'inline_label_when_true' => __('yes'),
            ],
        ];
    }

    /**
     * The action title
     *
     * @return string
     */
    public static function title()
    {
        return __('Generate Alt Text');
    }

    /**
     * Only make the action visible to assets that are images.
     *
     * @return bool
     */
    public function visibleTo($item)
    {
        return $item instanceof Asset && $item->isImage();
    }

    // TODO: Specify, which users/roles/groups can use this action
    // public function authorize($user, $item) {}

    /**
     * Generate alt texts when the 'Generate Alt Text' Action is selected.
     *
     * @param  Collection|Asset[]  $assets
     * @param  array<string, mixed>  $values
     * @return string
     */
    public function run($assets, $values)
    {
        $overwrite = $values['overwrite'];
        $assets->each(function ($asset) use ($overwrite) {
            $generateAltText = new GenerateAltText($asset, $overwrite);
            $generateAltText->generate();
        });

        if (config('queue.default') === 'sync') {
            return __('Succesfully generated alt texts');
        } else {
            return __('Succesfully added jobs to queue');
        }
    }
}
