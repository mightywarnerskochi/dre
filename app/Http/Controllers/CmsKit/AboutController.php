<?php

namespace App\Http\Controllers\CmsKit;

use App\Http\Controllers\Controller;
use App\Models\CmsKit\About;
use App\Support\MediaStorage;
use App\Models\CmsKit\Language;
use CMS\SiteManager\Support\ValidatesImageDimensions;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    use ValidatesImageDimensions;

    protected function getRecord(): About
    {
        return About::query()->first() ?? About::create(['status' => true, 'translations' => []]);
    }

    protected function rules(bool $isUpdate = false): array
    {
        $rules = [];
        $languages = Language::active()->get();
        $required = config('cms-kit.database.about.items.required', []);
        $imagesConfig = config('cms-kit.images.about', []);

        foreach ($languages as $language) {
            if (in_array('title', $required, true)) {
                $rules["translations.{$language->code}.title"] = ['required', 'string', 'max:255'];
            }

            $rules["translations.{$language->code}.subtitle"] = ['nullable', 'string', 'max:255'];
            $rules["translations.{$language->code}.description"] = ['nullable', 'string'];
        }

        $rules['image_1_alt'] = ['nullable', 'string', 'max:255'];
        $rules['image_2_alt'] = ['nullable', 'string', 'max:255'];
        $rules['image_3_alt'] = ['nullable', 'string', 'max:255'];
        $rules['image_4_alt'] = ['nullable', 'string', 'max:255'];

        foreach (['image_1', 'image_2', 'image_3', 'image_4'] as $field) {
            $presence = !$isUpdate && in_array($field, $required, true) ? 'required' : 'nullable';
            $rules[$field] = [$presence, 'image', 'max:' . ($imagesConfig[$field]['max_size'] ?? 1024)];
        }

        return $rules;
    }

    public function edit()
    {
        $about = $this->getRecord();
        $languages = Language::active()->get();
        $imagesConfig = config('cms-kit.images.about', []);

        return view('cms-kit::about.edit', compact('about', 'languages', 'imagesConfig'));
    }

    public function update(Request $request)
    {
        $about = $this->getRecord();
        $imagesConfig = config('cms-kit.images.about', []);
        $required = config('cms-kit.database.about.items.required', []);
        $rules = $this->rules(true);

        foreach (['image_1', 'image_2', 'image_3', 'image_4'] as $field) {
            $rules["remove_{$field}"] = ['nullable', 'boolean'];

            if (
                in_array($field, $required, true)
                && $request->boolean("remove_{$field}")
                && ! $request->hasFile($field)
            ) {
                $rules[$field][0] = 'required';
            }
        }

        $request->validate($rules);

        foreach (['image_1', 'image_2', 'image_3', 'image_4'] as $field) {
            $this->validateImageWithinLimits($request, $field, $imagesConfig[$field] ?? [], str_replace('_', ' ', ucfirst($field)));
        }

        $translations = $request->input('translations', []);
        foreach ($translations as $code => $values) {
            if (!array_key_exists('description', $values) && array_key_exists('short_description', $values)) {
                $translations[$code]['description'] = $values['short_description'];
            }
            unset($translations[$code]['short_description']);
        }

        $data = [
            'status' => $request->boolean('status'),
            'image_1_alt' => $request->input('image_1_alt'),
            'image_2_alt' => $request->input('image_2_alt'),
            'image_3_alt' => $request->input('image_3_alt'),
            'image_4_alt' => $request->input('image_4_alt'),
            'translations' => $translations,
        ];

        foreach (['image_1', 'image_2', 'image_3', 'image_4'] as $field) {
            if ($request->hasFile($field)) {
                if ($about->{$field}) {
                    MediaStorage::delete($about->{$field});
                }

                $data[$field] = MediaStorage::store($request->file($field), 'about');
            } elseif ($request->boolean("remove_{$field}") && $about->{$field}) {
                MediaStorage::delete($about->{$field});
                $data[$field] = null;
            }
        }

        $about->update($data);

        return redirect()->route('cms.about.edit')->with('success', 'About section updated successfully.');
    }
}
