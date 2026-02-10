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
        'position',
        'department',
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

    public function getCompleteCount()
    {
        $documents = $this->documents ?? [];
        return collect($documents)->filter(function($status) {
            return $status === 'complete';
        })->count();
    }

    public function getTotalCount()
    {
        return count($this->documents ?? []);
    }

    public function getCompletionPercentage()
    {
        $total = $this->getTotalCount();
        if ($total === 0) return 0;
        
        return ($this->getCompleteCount() / $total) * 100;
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
