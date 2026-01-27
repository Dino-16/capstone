<?php

namespace App\Models\Onboarding;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_name',
        'email',
        'documents',
        'status'
    ];

    protected $casts = [
        'documents' => 'array',
    ];

    public function getDocumentStatus($documentType)
    {
        $documents = $this->documents ?? [];
        return $documents[$documentType] ?? 'incomplete';
    }

    public function setDocumentStatus($documentType, $status)
    {
        $documents = $this->documents ?? [];
        $documents[$documentType] = $status;
        $this->documents = $documents;
        $this->save();
    }

    public function getCompletionPercentage()
    {
        $documents = $this->documents ?? [];
        
        // Calculate completion based on document count only (6 total documents)
        return (count($documents) / 6) * 100;
    }

    public function initializeDocuments()
    {
        $defaultDocuments = [
            'resume' => 'incomplete',
            'medical_certificate' => 'incomplete',
            'valid_government_id' => 'incomplete',
            'transcript_of_records' => 'incomplete',
            'nbi_clearance' => 'incomplete',
            'barangay_clearance' => 'incomplete',
        ];
        
        $this->documents = $defaultDocuments;
        $this->save();
    }
}
