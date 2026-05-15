<?php

namespace Database\Seeders;

use App\Models\ResourceLink;
use Illuminate\Database\Seeder;

class ResourceLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Siemens Resources
        $siemensResources = [
            [
                'vendor' => 'siemens',
                'category' => 'official',
                'label' => 'Siemens Documentation Portal',
                'url' => 'https://docs.sw.siemens.com/en-US/',
                'description' => 'Acceso centralizado a manuales, guías de instalación y notas de versión de todo el ecosistema Siemens PLM.',
                'icon' => 'book',
                'order' => 1
            ],
            [
                'vendor' => 'siemens',
                'category' => 'official',
                'label' => 'Siemens Support Center (WebKey)',
                'url' => 'https://support.sw.siemens.com/',
                'description' => 'Gestión de tickets, descargas de software y administración de licencias mediante ID de WebKey.',
                'icon' => 'shield',
                'order' => 2
            ],
            [
                'vendor' => 'siemens',
                'category' => 'internal',
                'label' => 'Guía de Configuración SALT',
                'url' => 'https://soporteays.sharepoint.com/:b:/g/guia-salt-v2',
                'description' => 'Documento interno detallando la transición de servidores ugslmd a saltd (29000).',
                'icon' => 'book',
                'order' => 3
            ],
            [
                'vendor' => 'siemens',
                'category' => 'utility',
                'label' => 'Siemens License Server Tools',
                'url' => 'https://support.sw.siemens.com/en-US/product/282219420/downloads',
                'description' => 'Descarga de la última versión de Siemens License Server y daemons específicos.',
                'icon' => 'utility',
                'order' => 4
            ],
        ];

        // Moldex3D Resources
        $moldexResources = [
            [
                'vendor' => 'moldex3d',
                'category' => 'official',
                'label' => 'Moldex3D Help Center',
                'url' => 'https://support.moldex3d.com/',
                'description' => 'Documentación técnica oficial y base de conocimientos para usuarios de Moldex3D.',
                'icon' => 'book',
                'order' => 1
            ],
            [
                'vendor' => 'moldex3d',
                'category' => 'official',
                'label' => 'Moldex3D License Management Guide',
                'url' => 'https://support.moldex3d.com/license-management',
                'description' => 'Instrucciones detalladas sobre la activación y gestión de licencias flotantes y locales.',
                'icon' => 'shield',
                'order' => 2
            ],
        ];

        foreach (array_merge($siemensResources, $moldexResources) as $resource) {
            ResourceLink::create($resource);
        }
    }
}
