<?php

namespace TFD\AIDA\Actions;

use Illuminate\Support\Collection;
use Statamic\Actions\Action;
use Statamic\Contracts\Assets\Asset;
use TFD\AIDA\GenerateAltText;
use TFD\AIDA\Generator\Generator;

class GenerateAltTextAction extends Action
{
    protected $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="none"><path d="M7.89 2.62c-1.12 0-2.22.1-3.29.22A3.135 3.135 0 0 0 1.85 5.6c-.12 1.07-.21 2.16-.21 3.27s.1 2.2.21 3.27m0 0c.16 1.45 1.3 2.59 2.75 2.76 1.07.12 2.17.22 3.29.22s2.22-.1 3.29-.22a3.125 3.125 0 0 0 2.75-2.76c.11-1.06.21-2.15.21-3.27s-.1-2.2-.21-3.27" stroke="currentColor" stroke-linejoin="round" stroke-width="1"/<path d="M3.18 11.32c.13-.68.52-3.28 1.3-3.28s1.17 2.6 1.3 3.28m-2.25-1.1h1.9m4.46-1.39v-.05c0-.35.23-.66.55-.7.53-.07 1.08-.07 1.61 0 .32.04.55.34.55.7v.05m-1.36-.66v2.99m-2.09 0c-.55.16-.95.2-1.44.1-.25-.05-.43-.27-.47-.55-.12-.89-.08-1.74.08-2.63m3.16-5.36c.64-.64.76-1.84 1.03-1.84s.39 1.2 1.03 1.84 1.83.71 1.83 1.02-1.19.38-1.83 1.02-.76 1.84-1.02 1.84c-.27 0-.39-1.2-1.03-1.84s-1.84-.71-1.84-1.03 1.19-.37 1.83-1.01" stroke="currentColor" stroke-linejoin="round" stroke-width="1"/></svg>';

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
