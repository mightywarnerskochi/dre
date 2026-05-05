<?php

namespace App\Http\Controllers\CmsKit;

use App\Models\CmsKit\Language;
use App\Models\CmsKit\SectionLabel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ContactSectionController extends Controller
{
    protected function validationRules(): array
    {
        $rules = [];

        foreach (Language::active()->get() as $lang) {
            foreach (['title', 'sub_title', 'content'] as $field) {
                $rules["translations.{$lang->code}.{$field}"] = 'nullable|string';
            }
        }

        return $rules;
    }

    public function edit()
    {
        $section = SectionLabel::firstOrCreate(['section_key' => 'contact']);
        $languages = Language::active()->get();

        return view('cms-kit::contact-section.edit', compact('section', 'languages'));
    }

    public function update(Request $request)
    {
        $data = $request->validate($this->validationRules());

        SectionLabel::updateOrCreate(
            ['section_key' => 'contact'],
            ['translations' => $data['translations'] ?? []]
        );

        return redirect()->route('cms.contact-section.edit')->with('success', 'Contact section updated successfully.');
    }
}
