<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;

class DocumentCategorySeeder extends Seeder
{
    /**
     * Seed the document_categories table.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'statuti',
                'label' => 'Statuti e Atti Costitutivi',
                'icon' => 'document-text',
                'sort_order' => 1,
            ],
            [
                'name' => 'visure',
                'label' => 'Visure Camerali',
                'icon' => 'magnifying-glass',
                'sort_order' => 2,
            ],
            [
                'name' => 'bilanci',
                'label' => 'Bilanci',
                'icon' => 'chart-bar',
                'sort_order' => 3,
            ],
            [
                'name' => 'contratti',
                'label' => 'Contratti',
                'icon' => 'clipboard-document-check',
                'sort_order' => 4,
            ],
            [
                'name' => 'certificati',
                'label' => 'Certificati e Attestazioni',
                'icon' => 'check-badge',
                'sort_order' => 5,
            ],
            [
                'name' => 'verbali',
                'label' => 'Verbali Assemblee',
                'icon' => 'chat-bubble-left-right',
                'sort_order' => 6,
            ],
            [
                'name' => 'procure',
                'label' => 'Procure e Deleghe',
                'icon' => 'user-group',
                'sort_order' => 7,
            ],
            [
                'name' => 'polizze',
                'label' => 'Polizze Assicurative',
                'icon' => 'shield-check',
                'sort_order' => 8,
            ],
            [
                'name' => 'fiscale',
                'label' => 'Documentazione Fiscale',
                'icon' => 'calculator',
                'sort_order' => 9,
            ],
            [
                'name' => 'altro',
                'label' => 'Altro',
                'icon' => 'folder',
                'sort_order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            DocumentCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
