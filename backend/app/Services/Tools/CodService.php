<?php

namespace App\Services\Tools;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CodService
{
    protected array $translations = [
        'SPANISH' => [
            'document_title' => "Solicitud de Cambio de Licencia",
            'cessation_cert' => "CERTIFICADO DE CESE",
            'cessation_p1' => "De acuerdo al contrato de SIEMENS de 'Suministro de Equipamiento'/ 'Master Software License and Service Agreement' firmado por las partes, tiene la obligación de interrumpir todo uso, destruir y/o devolver a SIEMENS todos los programas licenciados, incluyendo toda la documentación asociada y cualquier información confidencial al contrato inmediatamente al 1)expirar 2)cancelar 3)transferir la licencia. La licencia tendrá la consideración de expirada, cancelada o transferida en la fecha en la cual UGS reciba su notificación escrita.",
            'cessation_p2' => "Las licencias enumeradas a continuación y toda la documentación asociada e información confidencial, debe ser remitida a SIEMENS, destruida o su uso interrumpido o transferido a otro ordenador.",
            'company_cert' => "CERTIFICADO DE LA EMPRESA",
            'company_p1' => "El abajo firmante certifica que la lista de licencias de SIEMENS enumerada y todas sus copias, completas o parciales, sin considerar el formato, incluyendo copias parciales insertadas en otros programas o materiales, toda la documentación e información confidencial han sido remitidas a SIEMENS, destruidas o interrumpido su uso como consecuencia de transferir las licencias a otro ordenador.",
            'host_id' => "HOST ID",
            'host_id_new' => "New HOST ID",
            'composite_id' => "COMPOSITE ID",
            'hostname' => "HOSTNAME",
            'sold_to' => "SOLD TO",
            'applicant' => "SOLICITANTE",
            'company' => "EMPRESA",
            'date' => "FECHA",
            'signature' => "Firma Solicitante y Sello Empresa",
        ],
        'ENGLISH' => [
            'document_title' => "License Change Request",
            'cessation_cert' => "CERTIFICATE OF CESSATION",
            'cessation_p1' => "In accordance with the SIEMENS contract of 'Supply of Equipment'/ 'Master Software License and Service Agreement' signed by the parties, you are obliged to cease all use, destroy and/or return to SIEMENS all licensed programs, including all associated documentation and any confidential information under the contract immediately upon 1) expiration 2) cancellation 3) transfer of the license. The license shall be deemed expired, cancelled or transferred on the date on which UGS receives your written notification.",
            'cessation_p2' => "The licenses listed below and all associated documentation and confidential information must be returned to SIEMENS, destroyed or have their use ceased or transferred to another computer.",
            'company_cert' => "COMPANY CERTIFICATE",
            'company_p1' => "The undersigned certifies that the list of SIEMENS licenses enumerated and all their copies, complete or partial, regardless of format, including partial copies inserted into other programs or materials, all documentation and confidential information have been returned to SIEMENS, destroyed or have their use ceased as a result of transferring the licenses to another computer.",
            'host_id' => "HOST ID",
            'host_id_new' => "New HOST ID",
            'composite_id' => "COMPOSITE ID",
            'hostname' => "HOSTNAME",
            'sold_to' => "SOLD TO",
            'applicant' => "APPLICANT",
            'company' => "COMPANY",
            'date' => "DATE",
            'signature' => "Applicant's Signature and Company Seal",
        ]
    ];

    /**
     * Generates a COD PDF.
     * 
     * @param array $data Form data
     * @param string $language SPANISH or ENGLISH
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generatePdf(array $data, string $language = 'SPANISH')
    {
        $texts = $this->translations[strtoupper($language)] ?? $this->translations['SPANISH'];
        
        // Ensure OS specific formatting
        if (($data['os'] ?? '') === 'LINUX') {
            // Logic for Linux case-sensitivity if needed in the future
            // For now, we follow the original template logic
        }

        $pdf = Pdf::loadView('pdf.cod-template', [
            'texts' => $texts,
            'data' => $data,
            'fecha' => date('d-m-Y'),
        ]);

        // Paper configuration: A4, portrait
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    /**
     * Gets the coherent storage path for a client's COD.
     * 
     * @param string $clientSlug
     * @return string
     */
    public function getStoragePath(string $clientSlug): string
    {
        return "licenses/siemens/{$clientSlug}/COD";
    }
}
