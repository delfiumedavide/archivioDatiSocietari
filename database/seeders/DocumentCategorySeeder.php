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
            // Company document categories
            [
                'name' => 'statuti',
                'label' => 'Statuti e Atti Costitutivi',
                'icon' => 'document-text',
                'scope' => 'company',
                'sort_order' => 1,
            ],
            [
                'name' => 'visure',
                'label' => 'Visure Camerali',
                'icon' => 'magnifying-glass',
                'scope' => 'company',
                'sort_order' => 2,
            ],
            [
                'name' => 'bilanci',
                'label' => 'Bilanci',
                'icon' => 'chart-bar',
                'scope' => 'company',
                'sort_order' => 3,
            ],
            [
                'name' => 'contratti',
                'label' => 'Contratti',
                'icon' => 'clipboard-document-check',
                'scope' => 'company',
                'sort_order' => 4,
            ],
            [
                'name' => 'certificati',
                'label' => 'Certificati e Attestazioni',
                'icon' => 'check-badge',
                'scope' => 'company',
                'sort_order' => 5,
            ],
            [
                'name' => 'verbali',
                'label' => 'Verbali Assemblee',
                'icon' => 'chat-bubble-left-right',
                'scope' => 'company',
                'sort_order' => 6,
            ],
            [
                'name' => 'procure',
                'label' => 'Procure e Deleghe',
                'icon' => 'user-group',
                'scope' => 'company',
                'sort_order' => 7,
            ],
            [
                'name' => 'polizze',
                'label' => 'Polizze Assicurative',
                'icon' => 'shield-check',
                'scope' => 'company',
                'sort_order' => 8,
            ],
            [
                'name' => 'fiscale',
                'label' => 'Documentazione Fiscale',
                'icon' => 'calculator',
                'scope' => 'company',
                'sort_order' => 9,
            ],
            [
                'name' => 'altro',
                'label' => 'Altro',
                'icon' => 'folder',
                'scope' => 'company',
                'sort_order' => 10,
            ],

            // Member document categories
            [
                'name' => 'documento_identita',
                'label' => 'Documento d\'Identita',
                'icon' => 'identification',
                'scope' => 'member',
                'sort_order' => 101,
            ],
            [
                'name' => 'codice_fiscale_doc',
                'label' => 'Tessera Codice Fiscale',
                'icon' => 'credit-card',
                'scope' => 'member',
                'sort_order' => 102,
            ],
            [
                'name' => 'certificato_casellario',
                'label' => 'Certificato Casellario Giudiziale',
                'icon' => 'document-check',
                'scope' => 'member',
                'sort_order' => 103,
            ],
            [
                'name' => 'certificato_carichi_pendenti',
                'label' => 'Certificato Carichi Pendenti',
                'icon' => 'document-magnifying-glass',
                'scope' => 'member',
                'sort_order' => 104,
            ],
            [
                'name' => 'visura_protesti',
                'label' => 'Visura Protesti',
                'icon' => 'exclamation-triangle',
                'scope' => 'member',
                'sort_order' => 105,
            ],
            [
                'name' => 'certificato_residenza',
                'label' => 'Certificato di Residenza',
                'icon' => 'home',
                'scope' => 'member',
                'sort_order' => 106,
            ],
            [
                'name' => 'stato_famiglia_doc',
                'label' => 'Stato di Famiglia',
                'icon' => 'user-group',
                'scope' => 'member',
                'sort_order' => 107,
            ],
            [
                'name' => 'curriculum_vitae',
                'label' => 'Curriculum Vitae',
                'icon' => 'academic-cap',
                'scope' => 'member',
                'sort_order' => 108,
            ],
            [
                'name' => 'altro_membro',
                'label' => 'Altro Documento Personale',
                'icon' => 'folder',
                'scope' => 'member',
                'sort_order' => 109,
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
